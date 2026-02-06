<!-- Left Sidebar Navigation -->
<div class="sidebar">
    <!-- Logo/Brand -->
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <img id="sidebarLogo" src="" alt="Logo" class="sidebar-logo" onerror="this.src='https://via.placeholder.com/40'">
            <span id="sidebarTitle">LODIT</span>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
        <!-- Debug: permission payload (hidden) -->
        <div id="sidebarPermissionDebug" style="display:none; padding:8px; font-size:12px; color:#374151; background:#f3f4f6; margin:8px; border-radius:6px; max-height:120px; overflow:auto;"></div>

        <a href="/kli" class="nav-item" data-page="home">
            <i class="bi bi-house"></i>
            <span>Home</span>
        </a>
        
        <a href="/yao" class="nav-item permission-item" data-page="pos" data-permission="view_orders" data-visible-levels="1,3,4">
            <i class="bi bi-shop"></i>
            <span>POS System</span>
        </a>

        <!-- Patient: My Medicine History -->
        <a href="/medicine-history" class="nav-item" data-page="medicine-history" data-visible-level="1">
            <i class="bi bi-clock-history"></i>
            <span>My History</span>
        </a>

        <!-- Consultations (for regular users) -->
        <a href="/consultation/my-questions" class="nav-item" data-page="consultations" data-visible-levels="1,2,3,4,5,6,7,8,9">
            <i class="bi bi-chat-left-text"></i>
            <span>Consult Pharmacist</span>
        </a>

        <!-- Admin Section -->
        <div class="nav-divider admin-section" style="display: none;">
            <span>Admin Panel</span>
        </div>

        <a href="/dok" class="nav-item permission-item admin-item" data-page="medicines" data-permission="view_medicines" data-admin-levels="3,4">
            <i class="bi bi-capsule"></i>
            <span>Medicines</span>
        </a>

        <a href="/medtransactions" class="nav-item permission-item admin-item" data-page="transactions" data-permission="view_orders" data-admin-levels="3,4">
            <i class="bi bi-receipt"></i>
            <span>Transactions</span>
        </a>

        <a href="/admin/dashboard" class="nav-item permission-item admin-item" data-page="admin-dashboard" data-permission="view_dashboard" data-admin-levels="3,4">
            <i class="bi bi-graph-up"></i>
            <span>Dashboard</span>
        </a>

        <a href="/users" class="nav-item permission-item admin-item" data-page="users" data-permission="view_users" data-admin-levels="3,4">
            <i class="bi bi-people"></i>
            <span>Users</span>
        </a>

        <a href="/reports/" class="nav-item permission-item admin-item" data-page="reports" data-permission="view_sales_report" data-admin-levels="3,4">
            <i class="bi bi-bar-chart"></i>
            <span>Reports</span>
        </a>

        <a href="/permissions/manage" class="nav-item permission-item admin-item" data-page="permissions" data-permission="manage_permissions" data-admin-levels="4">
            <i class="bi bi-shield-lock"></i>
            <span>Permissions</span>
        </a>

        <a href="/settings" class="nav-item permission-item admin-item" data-page="settings" data-permission="view_dashboard" data-admin-levels="3,4">
            <i class="bi bi-gear"></i>
            <span>Settings</span>
        </a>

        <!-- Stocker Section -->
        <div class="nav-divider stocker-section" style="display: none;">
            <span>Stocker</span>
        </div>

        <a href="/stocker/stock-management" class="nav-item stocker-item" data-page="stocker-stock" data-visible-level="4,8">
            <i class="bi bi-boxes"></i>
            <span>Manage Stock</span>
        </a>

        <!-- Pharmacist Section -->
        <div class="nav-divider pharmacist-section" style="display: none;">
            <span>Pharmacist</span>
        </div>

        <a href="/prescription/pending" class="nav-item pharmacist-item" data-page="pharmacist-prescriptions" data-visible-level="4,7">
            <i class="bi bi-file-earmark-check"></i>
            <span>Validate Prescriptions</span>
        </a>

        <!-- Pharmacist admin: pending consultations -->
        <a href="/admin/pending-consultations" class="nav-item permission-item pharmacist-item" data-page="pending-consultations" data-permission="view_consultations" data-visible-levels="4,6">
            <i class="bi bi-chat-dots"></i>
            <span>Pending Consultations</span>
        </a>

        <a href="/notifications/list" class="nav-item" data-page="notifications" data-visible-level="1,3,4,7,8">
            <i class="bi bi-bell"></i>
            <span>Notifications</span>
            <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
        </a>

        <!-- Cashier Section -->
        <div class="nav-divider cashier-section" style="display: none;">
            <span>Cashier</span>
        </div>

        <a href="/cashier/dashboard" class="nav-item cashier-item" data-page="cashier-dashboard" data-visible-levels="3,7,9">
            <i class="bi bi-credit-card"></i>
            <span>Payment Management</span>
        </a>

        <!-- Super Admin Section -->
        <div class="nav-divider superadmin-section" style="display: none;">
            <span>Super Admin</span>
        </div>

        <a href="/superadmin/pending-changes" class="nav-item superadmin-item" data-page="superadmin-pending" data-visible-level="4">
            <i class="bi bi-clock-history"></i>
            <span>Pending Changes</span>
        </a>

        <a href="/superadmin/audit-log" class="nav-item superadmin-item" data-page="superadmin-audit" data-visible-level="4">
            <i class="bi bi-file-text"></i>
            <span>Audit Log</span>
        </a>

        <a href="/superadmin/hidden-users" class="nav-item superadmin-item" data-page="superadmin-hidden" data-visible-level="4">
            <i class="bi bi-eye-slash"></i>
            <span>Hidden Users</span>
        </a>

        <a href="/superadmin/backup-reset" class="nav-item permission-item superadmin-item" data-page="superadmin-backup" data-permission="manage_database" data-visible-level="4">
            <i class="bi bi-arrow-clockwise"></i>
            <span>Backup & Reset</span>
        </a>

        <!-- User Section -->
        <div class="nav-divider">
            <span>Account</span>
        </div>

        <a href="#" class="nav-item" onclick="openProfileModal()" data-page="profile">
            <i class="bi bi-person"></i>
            <span>Profile</span>
        </a>

        <a href="/logout" class="nav-item" data-page="logout">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="sidebar-version">
            <small>v1.0</small>
        </div>
    </div>
</div>

<script>
    let userPermissions = {};

    // Set active nav item based on current page
    document.addEventListener('DOMContentLoaded', () => {
        loadUserPermissions();
        const currentPage = getCurrentPage();
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
            if (item.getAttribute('data-page') === currentPage) {
                item.classList.add('active');
            }
        });

        // Load notification count
        loadNotificationCount();

        // Poll for permission updates every 5 seconds
        setInterval(loadUserPermissions, 5000);
    });

    function loadUserPermissions() {
        // Get permissions from session user's level
        console.log('Loading permissions for user level:', window.userLevel);
        
        fetch('/permissions/api/level-permissions')
            .then(r => {
                console.log('Permissions API response status:', r.status);
                if (!r.ok) {
                    throw new Error('HTTP ' + r.status);
                }
                return r.json();
            })
            .then(data => {
                if (!data || !data.permissions) {
                    throw new Error('Invalid permissions response');
                }
                
                const userLevel = window.userLevel || 2;
                const levelPerms = data.permissions[userLevel] || {};
                console.log('Loaded permissions for level', userLevel, ':', levelPerms);
                
                // If the level returned no permission keys at all, treat as "unconfigured" and show everything.
                // If permission keys are present but all false, treat that as "configured with no access" and hide items.
                const hasPermissionKeys = Object.keys(levelPerms || {}).length > 0;
                const hasAnyPermission = Object.values(levelPerms || {}).some(v => v === true || v === 1);
                console.log('Permission keys present:', hasPermissionKeys, 'Any permission true:', hasAnyPermission);

                userPermissions = levelPerms;
                // Update debug element so we can inspect permissions quickly
                try {
                    const debugEl = document.getElementById('sidebarPermissionDebug');
                    if (debugEl) {
                        debugEl.textContent = JSON.stringify(userPermissions, null, 2);
                        // Keep debug box hidden by default (set to '' to show for troubleshooting)
                        debugEl.style.display = 'none';
                    }
                } catch (e) { }
                filterSidebarByPermissions();
            })
            .catch(error => {
                console.error('Error loading permissions:', error);
                console.log('Showing all items since permissions failed to load');
                // If we can't load permissions, show all items by default
                userPermissions = {};
                filterSidebarByPermissions();
            });
    }

    function filterSidebarByPermissions() {
        const permissionItems = document.querySelectorAll('.permission-item');
        const allNavItems = document.querySelectorAll('.nav-item');
        const userLevel = parseInt(window.userLevel || 0, 10);
        
        console.log('=== FILTERING SIDEBAR ===');
        console.log('User level:', userLevel);
        console.log('User permissions:', userPermissions);
        console.log('Permission items to filter:', permissionItems.length);
        
        // Level 4 super admins see everything
        const isSuperAdmin = userLevel === 4;
        
        // Determine whether permissions were configured for this level (keys exist)
        const hasPermissionKeys = Object.keys(userPermissions || {}).length > 0;
        const hasAnyPermissionTrue = Object.values(userPermissions || {}).some(v => v === true || v === 1);
        console.log('Permission keys present:', hasPermissionKeys, 'Any permission true:', hasAnyPermissionTrue);
        
        let adminSectionHasVisibleItems = false;

        permissionItems.forEach(item => {
            const requiredPerm = item.getAttribute('data-permission');
            const permValue = (userPermissions && Object.prototype.hasOwnProperty.call(userPermissions, requiredPerm)) ? userPermissions[requiredPerm] : false;
            const adminLevels = item.getAttribute('data-admin-levels');
            const visibleLevels = item.getAttribute('data-visible-levels');
            
            // Check permission
            const hasPermission = (permValue === true || permValue === 1);
            
            // Check admin level restriction
            let hasAdminLevel = true;
            if (adminLevels) {
                const levels = adminLevels.split(',').map(l => parseInt(l.trim(), 10));
                hasAdminLevel = levels.includes(userLevel);
            }
            
            // Check visible levels restriction
            let hasVisibleLevel = true;
            if (visibleLevels) {
                const levels = visibleLevels.split(',').map(l => parseInt(l.trim(), 10));
                hasVisibleLevel = levels.includes(userLevel);
            }
            
            // Permission-controlled visibility for all users (including superadmins)
            // If there are no permission keys configured for this level, show everything;
            // otherwise only show when the specific permission is true.
            const shouldShow = ((hasPermission || !hasPermissionKeys) && hasAdminLevel && hasVisibleLevel);
            
            console.log(`Item: ${item.textContent.trim()} | Permission: ${requiredPerm} (${String(permValue)}) | AdminLevels: ${adminLevels} | VisibleLevels: ${visibleLevels} | UserLevel: ${userLevel} | HasAdminLevel: ${hasAdminLevel} | HasVisibleLevel: ${hasVisibleLevel} | IsSuperAdmin: ${isSuperAdmin} | Show: ${shouldShow}`);

            if (shouldShow) {
                item.style.display = '';
                if (item.classList.contains('admin-item')) {
                    adminSectionHasVisibleItems = true;
                }
            } else {
                item.style.display = 'none';
            }
        });

        // Handle items that are shown only for specific user levels
        // Important: do not override permission-based visibility. Combine both checks.
        allNavItems.forEach(item => {
            const requiredLevel = item.getAttribute('data-visible-level') || item.getAttribute('data-visible-levels');
            if (!requiredLevel) return;

            // Support both single level and comma-separated levels
            const levels = requiredLevel.split(',').map(l => parseInt(l.trim(), 10));
            const levelAllows = levels.includes(userLevel);

            // Determine current visibility (permission loop set this earlier)
            const currentlyVisible = item.style.display !== 'none';

            const shouldShow = levelAllows && currentlyVisible;

            console.log(`Level-restricted item: ${item.textContent.trim()} | RequiredLevels: ${requiredLevel} | UserLevel: ${userLevel} | LevelAllows: ${levelAllows} | CurrentlyVisible: ${currentlyVisible} | Show: ${shouldShow}`);

            item.style.display = shouldShow ? '' : 'none';
        });

        // Show/hide superadmin section divider based on whether any superadmin items are visible
        const superadminSection = document.querySelector('.superadmin-section');
        if (superadminSection) {
            let hasVisibleSuperadmin = false;
            document.querySelectorAll('.superadmin-item').forEach(item => {
                if (item.style.display !== 'none') {
                    hasVisibleSuperadmin = true;
                }
            });
            superadminSection.style.display = hasVisibleSuperadmin ? '' : 'none';
        }

        // Show/hide admin section divider based on whether any admin items are visible
        const adminSection = document.querySelector('.admin-section');
        if (adminSection) {
            adminSection.style.display = adminSectionHasVisibleItems ? '' : 'none';
        }

        // Show/hide stocker section divider based on whether any stocker items are visible
        const stockerSection = document.querySelector('.stocker-section');
        if (stockerSection) {
            let hasVisibleStocker = false;
            document.querySelectorAll('.stocker-item').forEach(item => {
                if (item.style.display !== 'none') {
                    hasVisibleStocker = true;
                }
            });
            stockerSection.style.display = hasVisibleStocker ? '' : 'none';
        }

        // Show/hide pharmacist section divider based on whether any pharmacist items are visible
        const pharmacistSection = document.querySelector('.pharmacist-section');
        if (pharmacistSection) {
            let hasVisiblePharmacist = false;
            document.querySelectorAll('.pharmacist-item').forEach(item => {
                if (item.style.display !== 'none') {
                    hasVisiblePharmacist = true;
                }
            });
            pharmacistSection.style.display = hasVisiblePharmacist ? '' : 'none';
        }

        // Show/hide cashier section divider based on whether any cashier items are visible
        const cashierSection = document.querySelector('.cashier-section');
        if (cashierSection) {
            let hasVisibleCashier = false;
            document.querySelectorAll('.cashier-item').forEach(item => {
                if (item.style.display !== 'none') {
                    hasVisibleCashier = true;
                }
            });
            cashierSection.style.display = hasVisibleCashier ? '' : 'none';
        }
        
        console.log('=== FILTERING COMPLETE ===');
    }

    function getCurrentPage() {
        const path = window.location.pathname;
        if (path.includes('/superadmin/pending-changes')) return 'superadmin-pending';
        if (path.includes('/superadmin/audit-log')) return 'superadmin-audit';
        if (path.includes('/superadmin/hidden-users')) return 'superadmin-hidden';
        if (path.includes('/superadmin/backup-reset')) return 'superadmin-backup';
        if (path.includes('/cashier/dashboard')) return 'cashier-dashboard';
        if (path.includes('/stocker/stock-management')) return 'stocker-stock';
        if (path.includes('/prescription/pending')) return 'pharmacist-prescriptions';
        if (path.includes('/admin')) return 'admin-dashboard';
        if (path.includes('/yao')) return 'pos';
        if (path.includes('/medicines')) return 'medicines';
        if (path.includes('/transactions') || path.includes('/medtransactions')) return 'transactions';
        if (path.includes('/medicine-history')) return 'medicine-history';
        if (path.includes('/users')) return 'users';
        if (path.includes('/reports')) return 'reports';
        if (path.includes('/permissions')) return 'permissions';
        if (path.includes('/settings')) return 'settings';
        if (path.includes('/notifications')) return 'notifications';
        return 'home';
    }

    function loadNotificationCount() {
        fetch('/notifications/list')
            .then(r => r.json())
            .then(data => {
                const unreadCount = data.unread_count || 0;
                const badge = document.getElementById('notificationBadge');
                if (unreadCount > 0) {
                    badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(() => {});
    }

    // Refresh notification count every 10 seconds for real-time updates
    setInterval(loadNotificationCount, 10000);
</script>
