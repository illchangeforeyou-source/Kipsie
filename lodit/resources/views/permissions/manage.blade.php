@extends('layouts.app')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a8a;
            --light-bg: #f9fafb;
            --dark-bg: #1f2937;
        }

        body {
            background: var(--light-bg);
            transition: background-color 0.3s;
        }

        body.dark-mode {
            background: var(--dark-bg);
            color: #f9fafb;
        }

        .permissions-container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 15px;
        }

        .permissions-header {
            background: white;
            padding: 30px;
            margin-bottom: 30px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        body.dark-mode .permissions-header {
            background: #374151;
        }

        .permissions-header h1 {
            color: var(--primary);
            margin: 0;
            font-size: 32px;
        }

        .permissions-header p {
            color: #6b7280;
            margin: 10px 0 0 0;
        }

        body.dark-mode .permissions-header p {
            color: #9ca3af;
        }

        .permissions-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        body.dark-mode .permissions-card {
            background: #374151;
        }

        .permissions-table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
        }

        .permissions-table thead {
            background: #f3f4f6;
        }

        body.dark-mode .permissions-table thead {
            background: #4b5563;
        }

        .permissions-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border: none;
            position: sticky;
            top: 0;
            background: #f3f4f6;
        }

        body.dark-mode .permissions-table th {
            background: #4b5563;
            color: #e5e7eb;
        }

        .permissions-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        body.dark-mode .permissions-table td {
            border-bottom-color: #4b5563;
        }

        .permissions-table tbody tr:hover {
            background: #f9fafb;
        }

        body.dark-mode .permissions-table tbody tr:hover {
            background: #1f2937;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 180px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, #1e40af 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .permission-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .permission-cell {
            text-align: center;
            min-width: 100px;
        }

        .permission-category {
            background: #f3f4f6;
            padding: 15px;
            font-weight: 600;
            color: var(--primary);
            border-top: 2px solid #e5e7eb;
        }

        body.dark-mode .permission-category {
            background: #4b5563;
            color: #93c5fd;
            border-top-color: #374151;
        }

        /* Dark mode text fixes for visibility */
        body.dark-mode h1,
        body.dark-mode h2,
        body.dark-mode h3,
        body.dark-mode h4,
        body.dark-mode h5,
        body.dark-mode h6 {
            color: #f9fafb;
        }

        body.dark-mode .permissions-header h1 {
            color: #93c5fd;
        }

        body.dark-mode p {
            color: #e5e7eb;
        }

        body.dark-mode .permissions-card h4 {
            color: #93c5fd;
        }

        body.dark-mode .permissions-table tbody td {
            color: #e5e7eb;
        }

        body.dark-mode .level-row td small {
            color: #ffffff !important;
            font-weight: 700;
            font-size: 14px !important;
        }

        .level-text {
            color: #6b7280 !important;
            font-weight: 500;
        }

        body.dark-mode .level-text {
            color: #a3a3a3 !important;
            font-weight: 500;
        }

        .level-row td small {
            color: #1f2937;
            font-weight: 700;
            font-size: 14px;
        }

        body.dark-mode .user-info div[style*="font-weight"] {
            color: #f9fafb !important;
        }

        body.dark-mode .badge-admin,
        body.dark-mode .badge-employee,
        body.dark-mode .badge-superadmin {
            color: #f9fafb;
        }

        body.dark-mode .btn-save-all {
            background: #1e40af;
        }

        body.dark-mode .btn-save-all:hover {
            background: #1e3a8a;
        }

        .btn-save-all {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }

        .btn-save-all:hover {
            background: #1e40af;
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
        }

        .success-message {
            background: #d1fae5;
            color: #065f46;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        body.dark-mode .success-message {
            background: #064e3b;
            color: #86efac;
        }

        .filter-section {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .filter-group input,
        .filter-group select {
            padding: 8px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
        }

        body.dark-mode .filter-group input,
        body.dark-mode .filter-group select {
            background: #1f2937;
            color: #f9fafb;
            border-color: #4b5563;
        }

        .user-role-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 8px;
        }

        .badge-admin {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-employee {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-superadmin {
            background: #fee2e2;
            color: #991b1b;
        }

        @media (max-width: 1024px) {
            .permissions-table th,
            .permissions-table td {
                padding: 10px 8px;
                font-size: 13px;
            }

            .user-info {
                min-width: 140px;
            }
        }

        .table-scroll {
            overflow-x: auto;
        }
    </style>
@endsection

@section('content')
    <div class="permissions-container">
        <!-- Header -->
        <div class="permissions-header">
            <h1><i class="bi bi-shield-lock"></i> User Permissions</h1>
            <p>Manage access control and feature availability for users</p>
        </div>

        <!-- Success Message -->
        <div class="success-message" id="successMessage">
            <i class="bi bi-check-circle"></i> Permissions updated successfully!
        </div>

        <!-- Permissions Card -->
        <div class="permissions-card">
            <h4 style="margin-bottom: 20px;"><i class="bi bi-list-check"></i> Permission Matrix by User Level</h4>
            
            <div class="table-scroll">
                <table class="permissions-table">
                    <thead>
                        <tr>
                            <th style="min-width: 200px;">User Level</th>
                            <th class="permission-cell">Dashboard</th>
                            <th class="permission-cell">Medicines</th>
                            <th class="permission-cell">Orders</th>
                            <th class="permission-cell">Users</th>
                            <th class="permission-cell">Reports</th>
                            <th class="permission-cell">Consultations</th>
                            <th class="permission-cell">Deliveries</th>
                            <th class="permission-cell">Prescriptions</th>
                            <th style="min-width: 80px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="permissionsTableBody">
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 30px; color: #9ca3af;">
                                <i class="bi bi-hourglass"></i> Loading permissions...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button class="btn-save-all" id="saveBtnAll">
                <i class="bi bi-save"></i> Save All Changes
            </button>
        </div>

        <!-- Permission Legend -->
        <div class="permissions-card">
            <h4 style="margin-bottom: 15px;"><i class="bi bi-info-circle"></i> Permission Categories</h4>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div>
                    <h5 style="color: var(--primary); margin-bottom: 8px;">📊 Dashboard</h5>
                    <p style="font-size: 13px; margin: 0;">view_dashboard, view_analytics</p>
                </div>
                <div>
                    <h5 style="color: var(--primary); margin-bottom: 8px;">💊 Medicines</h5>
                    <p style="font-size: 13px; margin: 0;">view, create, edit, delete</p>
                </div>
                <div>
                    <h5 style="color: var(--primary); margin-bottom: 8px;">📦 Orders</h5>
                    <p style="font-size: 13px; margin: 0;">view, edit_status, process_payment</p>
                </div>
                <div>
                    <h5 style="color: var(--primary); margin-bottom: 8px;">👥 Users</h5>
                    <p style="font-size: 13px; margin: 0;">view, manage, assign_roles, manage_permissions</p>
                </div>
                <div>
                    <h5 style="color: var(--primary); margin-bottom: 8px;">📈 Reports</h5>
                    <p style="font-size: 13px; margin: 0;">view_sales, view_stock, export</p>
                </div>
                <div>
                    <h5 style="color: var(--primary); margin-bottom: 8px;">💬 Consultations</h5>
                    <p style="font-size: 13px; margin: 0;">view, answer</p>
                </div>
                <div>
                    <h5 style="color: var(--primary); margin-bottom: 8px;">🚚 Deliveries</h5>
                    <p style="font-size: 13px; margin: 0;">view, manage_status</p>
                </div>
                <div>
                    <h5 style="color: var(--primary); margin-bottom: 8px;">📋 Prescriptions</h5>
                    <p style="font-size: 13px; margin: 0;">view, validate</p>
                </div>
                <div>
                    <h5 style="color: var(--primary); margin-bottom: 8px;">💾 Database</h5>
                    <p style="font-size: 13px; margin: 0;">backup, reset, manage</p>
                </div>
            </div>
        </div>
    </div>

    </script>
@endsection

@section('scripts')
    <script>
        const permissionCategories = {
            'Dashboard': ['view_dashboard', 'view_analytics'],
            'Medicines': ['view_medicines', 'create_medicine', 'edit_medicine', 'delete_medicine'],
            'Orders': ['view_orders', 'edit_order_status', 'process_payment'],
            'Users': ['view_users', 'manage_users', 'assign_roles', 'manage_permissions'],
            'Reports': ['view_sales_report', 'view_stock_report', 'export_reports'],
            'Consultations': ['view_consultations', 'answer_consultations'],
            'Deliveries': ['view_deliveries', 'manage_delivery_status'],
            'Prescriptions': ['view_prescriptions', 'validate_prescriptions'],
            'Database': ['backup_database', 'reset_database', 'manage_database']
        };

        const categoryEmojis = {
            'Dashboard': '📊',
            'Medicines': '💊',
            'Orders': '📦',
            'Users': '👥',
            'Reports': '📈',
            'Consultations': '💬',
            'Deliveries': '🚚',
            'Prescriptions': '📋',
            'Database': '💾'
        };

        const levels = [
            { id: 1, name: 'Customer/Patient', badge: 'badge-employee' },
            { id: 2, name: 'Manager', badge: 'badge-employee' },
            { id: 3, name: 'Admin', badge: 'badge-admin' },
            { id: 4, name: 'Super Admin', badge: 'badge-superadmin' },
            { id: 5, name: 'Owner', badge: 'badge-superadmin' },
            { id: 6, name: 'Pharmacist', badge: 'badge-admin' },
            { id: 7, name: 'Cashier', badge: 'badge-employee' },
            { id: 8, name: 'Stocker', badge: 'badge-employee' },
            { id: 9, name: 'Cashier Leader', badge: 'badge-admin' }
        ];

        let levelPermissions = {};
        window.levelPermissions = levelPermissions;

        document.addEventListener('DOMContentLoaded', () => {
            loadPermissions();
            
            // Add save button listener
            const saveBtn = document.getElementById('saveBtnAll');
            if (saveBtn) {
                saveBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Save button clicked');
                    saveAllPermissions();
                });
            }
        });

        function loadPermissions() {
            fetch('/permissions/api/level-permissions')
                .then(r => {
                    console.log('Response status:', r.status);
                    if (!r.ok) {
                        throw new Error('Failed to load permissions: HTTP ' + r.status);
                    }
                    return r.json();
                })
                .then(data => {
                    console.log('Loaded data:', data);
                    
                    // Convert all permission values to proper booleans
                    const normalized = {};
                    Object.keys(data.permissions).forEach(level => {
                        normalized[level] = {};
                        Object.keys(data.permissions[level]).forEach(perm => {
                            const val = data.permissions[level][perm];
                            normalized[level][perm] = val === true || val === 1 || val === '1' || val === 'true';
                        });
                    });
                    
                    levelPermissions = normalized;
                    window.levelPermissions = levelPermissions;
                    console.log('Level permissions set to:', levelPermissions);
                    renderTable();
                    console.log('Loaded permissions for', Object.keys(levelPermissions).length, 'levels');
                })
                .catch(error => {
                    console.error('Error loading permissions:', error);
                    levelPermissions = {};
                    const tbody = document.getElementById('permissionsTableBody');
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 30px; color: #dc2626;">
                                <i class="bi bi-exclamation-circle"></i><br>
                                Error loading permissions: ${error.message}<br>
                                <small>Make sure you are logged in as a Super Admin (Level 4)</small>
                            </td>
                        </tr>
                    `;
                });
        }

        function renderTable() {
            const tbody = document.getElementById('permissionsTableBody');
            if (!tbody) {
                console.error('permissionsTableBody not found');
                return;
            }
            
            tbody.innerHTML = '';

            if (!levels || levels.length === 0) {
                tbody.innerHTML = '<tr><td colspan="10" style="text-align: center; padding: 30px;">No levels configured</td></tr>';
                return;
            }

            levels.forEach(level => {
                const levelRow = document.createElement('tr');
                levelRow.className = 'level-row';
                levelRow.setAttribute('data-level', level.id);
                
                // Determine text color based on dark mode
                const isDarkMode = document.body.classList.contains('dark-mode');
                const textColor = isDarkMode ? '#ffffff' : '#374151';
                const fontSize = '14px';
                const fontWeight = 'bold';

                let levelHTML = `
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="user-role-badge ${level.badge}">${level.name}</span>
                            <small style="color: ${textColor}; font-weight: ${fontWeight}; font-size: ${fontSize};">Level ${level.id}</small>
                        </div>
                    </td>
                `;

                // Permission cells
                const levelPerms = levelPermissions[level.id] || {};
                console.log('Level', level.id, 'permissions:', levelPerms);
                
                Object.keys(permissionCategories).forEach(category => {
                    const permissions = permissionCategories[category];
                    // Check if ANY permission in this category is TRUE (handle both boolean and integer values)
                    const categoryHasAccess = permissions.some(perm => {
                        const val = levelPerms[perm];
                        return val === true || val === 1 || val === '1' || val === 'true';
                    });
                    
                    levelHTML += `
                        <td class="permission-cell">
                            <input type="checkbox" class="permission-checkbox" 
                                data-level="${level.id}" 
                                data-category="${category}"
                                ${categoryHasAccess ? 'checked' : ''}
                                onchange="toggleCategoryPermissions(this, ${level.id}, '${category}', ${permissions.length})">
                        </td>
                    `;
                });

                levelHTML += `
                    <td style="text-align: center;">
                        <button class="btn btn-sm btn-outline-primary reset-btn" data-level-id="${level.id}">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </td>
                `;

                levelRow.innerHTML = levelHTML;
                tbody.appendChild(levelRow);
            });
            
            // Attach reset button event listeners
            document.querySelectorAll('.reset-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const levelId = this.getAttribute('data-level-id');
                    console.log('Reset button clicked for level:', levelId);
                    resetLevelPermissions(levelId);
                });
            });
        }

        function toggleCategoryPermissions(checkbox, levelId, category, permCount) {
            const permissions = permissionCategories[category];
            const isChecked = checkbox.checked;

            console.log(`Toggle ${category} for level ${levelId}: ${isChecked}`);

            if (!levelPermissions[levelId]) {
                levelPermissions[levelId] = {};
            }

            permissions.forEach(perm => {
                levelPermissions[levelId][perm] = isChecked;
            });
            
            // Update window variable
            window.levelPermissions = levelPermissions;
            
            console.log('Updated levelPermissions:', window.levelPermissions);

            // Visual feedback for changed rows
            const row = checkbox.closest('.level-row');
            if (row) {
                row.style.backgroundColor = isChecked ? 'rgba(34, 197, 94, 0.15)' : '';
                row.style.transition = 'background-color 0.3s';
            }
        }

        function filterUsers() {
            const searchText = document.getElementById('userSearch').value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter').value;

            document.querySelectorAll('.level-row').forEach(row => {
                const levelId = row.getAttribute('data-level');
                const matchesRole = !roleFilter || levelId == roleFilter;

                row.style.display = matchesRole ? '' : 'none';
            });
        }

        function saveAllPermissions() {
            console.log('=== SAVE BUTTON CLICKED ===');
            
            if (!window.levelPermissions || Object.keys(window.levelPermissions).length === 0) {
                console.warn('No permissions loaded');
                alert('Please wait for permissions to load');
                return;
            }
            
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (!csrfTokenMeta) {
                console.error('CSRF token meta not found');
                alert('Security error: CSRF token missing');
                return;
            }
            
            const csrfToken = csrfTokenMeta.getAttribute('content');
            console.log('CSRF token found:', csrfToken.substring(0, 10) + '...');
            
            const btn = document.querySelector('.btn-save-all');
            if (!btn) {
                console.error('Save button not found');
                return;
            }
            
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
            btn.disabled = true;

            const payload = { permissions: window.levelPermissions };
            console.log('Sending save request with payload:', JSON.stringify(payload).substring(0, 200) + '...');

            fetch('/permissions/api/save-level-permissions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => {
                console.log('Response received, status:', response.status);
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Save successful, response:', data);
                btn.innerHTML = originalText;
                btn.disabled = false;
                
                if (data.success) {
                    // Clear the visual feedback (green background)
                    document.querySelectorAll('.level-row').forEach(row => {
                        row.style.backgroundColor = '';
                    });
                    
                    showSuccessMessage();
                    
                    // Reload permissions from server to confirm save
                    console.log('Reloading permissions from server to confirm save');
                    loadPermissions();
                } else {
                    alert('Error: ' + (data.message || 'Unknown'));
                }
            })
            .catch(error => {
                console.error('Save failed:', error);
                btn.innerHTML = originalText;
                btn.disabled = false;
                const msg = error && error.message ? error.message : String(error);
                alert('Error: ' + msg);
            });
        }

        function resetLevelPermissions(levelId) {
            console.log('=== resetLevelPermissions called for level', levelId);
            
            // Check if levelId is valid
            if (!levelId || isNaN(levelId)) {
                console.error('Invalid levelId:', levelId);
                alert('Invalid level ID');
                return;
            }
            
            if (!confirm('Are you sure you want to reset all permissions for this level?')) {
                return;
            }

            try {
                const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfTokenMeta) {
                    alert('CSRF token not found');
                    return;
                }
                
                const csrfToken = csrfTokenMeta.getAttribute('content');
                console.log('Sending reset for level:', levelId);

                fetch(`/permissions/api/reset-level/${levelId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(r => {
                    console.log('Reset response status:', r.status);
                    if (!r.ok) {
                        throw new Error('HTTP ' + r.status);
                    }
                    return r.json();
                })
                .then(data => {
                    console.log('Reset response:', data);
                    if (data.success) {
                        console.log('Reset successful, reloading permissions');
                        loadPermissions();
                        showSuccessMessage();
                    } else {
                        alert('Error: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Reset error:', error);
                    const msg = error && error.message ? error.message : String(error);
                    alert('Error: ' + msg);
                });
            } catch (error) {
                console.error('Exception in reset:', error);
                const msg = error && error.message ? error.message : String(error);
                alert('Error: ' + msg);
            }
        }

        function showSuccessMessage(message = 'Permissions updated successfully!') {
            const msg = document.getElementById('successMessage');
            if (!msg) {
                alert(message);
                return;
            }
            msg.textContent = message;
            msg.style.display = 'block';
            setTimeout(() => {
                msg.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>
