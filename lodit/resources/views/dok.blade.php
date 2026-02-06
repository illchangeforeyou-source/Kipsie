@extends('layouts.app')

@section('title', 'LODIT - Medicine Database')

@section('head')
    <style>
        /* Dark mode support */
        html.dark-mode .table-responsive {
            background-color: #0d0d0d;
            border-radius: 8px;
        }

        html.dark-mode .table {
            background-color: #0d0d0d !important;
            color: #e8e8e8 !important;
            border-color: #1a1a1a !important;
        }

        html.dark-mode .table thead {
            background-color: #000000 !important;
            border-color: #1a1a1a !important;
        }

        html.dark-mode .table thead th {
            background-color: #000000 !important;
            color: #e8e8e8 !important;
            border-color: #1a1a1a !important;
            font-weight: 600;
        }

        html.dark-mode .table tbody {
            background-color: #0d0d0d !important;
        }

        html.dark-mode .table tbody tr {
            background-color: #0d0d0d !important;
            border-color: #1a1a1a !important;
        }

        html.dark-mode .table tbody tr:hover {
            background-color: #1a1a1a !important;
        }

        html.dark-mode .table tbody td {
            background-color: #0d0d0d !important;
            border-color: #1a1a1a !important;
            color: #e8e8e8 !important;
        }

        html.dark-mode .table td {
            border-color: #1a1a1a !important;
            color: #e8e8e8 !important;
            background-color: #0d0d0d !important;
        }

        html.dark-mode .badge {
            color: #000 !important;
        }

        html.dark-mode .modal-content {
            background-color: #2a2a2a;
            border-color: #333;
            color: #f5f5f5;
        }

        html.dark-mode .modal-header {
            background-color: #1f1f1f;
            border-color: #333;
        }

        html.dark-mode .form-control,
        html.dark-mode .form-select,
        html.dark-mode textarea {
            background-color: #1f1f1f;
            border-color: #333;
            color: #f5f5f5;
        }

        html.dark-mode .form-control:focus,
        html.dark-mode .form-select:focus,
        html.dark-mode textarea:focus {
            background-color: #252525;
            border-color: #555;
            color: #f5f5f5;
        }

        .medicine-image {
            max-width: 50px;
            max-height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Medicine Inventory</h2>
            @if(session('level') == 3 || session('level') == 4)
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-lg"></i> Add Medicine
            </button>
            @endif
        </div>

        <!-- Search and Filter -->
        <div class="mb-3 d-flex gap-2">
            <input 
                type="text" 
                id="searchInput" 
                class="form-control"
                placeholder="Search medicine name..."
                style="max-width: 300px;"
            >
            <select id="categoryFilter" class="form-select" style="max-width: 250px;">
                <option value="">All Categories</option>
            </select>
        </div>

        <!-- Medicines Table -->
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 60px;">Image</th>
                        <th>Name</th>
                        <th style="width: 120px;">Category</th>
                        <th style="width: 100px;">Price</th>
                        <th style="width: 80px;">Stock</th>
                        <th style="width: 100px;">Age Restriction</th>
                        <th style="width: 120px;">Expiry Date</th>
                        <th style="width: 150px;">Description</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="medicinesTableBody">
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">Loading medicines...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="viewName">Medicine Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img id="viewImage" src="" alt="Medicine Image" style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 8px;">
                        </div>
                        <div class="col-md-8">
                            <p><strong>Name:</strong> <span id="viewNameText"></span></p>
                            <p><strong>Category:</strong> <span id="viewCategory"></span></p>
                            <p><strong>Price:</strong> $<span id="viewPrice"></span></p>
                            <p><strong>Stock:</strong> <span id="viewStock"></span></p>
                            <p><strong>Age Restriction:</strong> <span id="viewAgeRestriction">-</span></p>
                            <p><strong>Expiry Date:</strong> <span id="viewExpiryDate">-</span></p>
                            <p><strong>Description:</strong></p>
                            <p id="viewDescription" style="background: #f8f9fa; padding: 10px; border-radius: 4px;"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    @if(session('level') == 3 || session('level') == 4)
                    <button class="btn btn-warning" id="openEditBtn">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="btn btn-danger" id="deleteBtn">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                    @endif
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Edit Medicine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-id">
                    
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input id="edit-name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select id="edit-category" class="form-select"></select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Price ($)</label>
                        <input id="edit-price" type="number" class="form-control" min="0" step="0.01" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input id="edit-stock" type="number" class="form-control" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Age Restriction (e.g., 18+, 21+)</label>
                        <input id="edit-age-restriction" class="form-control" placeholder="Leave blank if none">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input id="edit-expiry-date" type="date" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea id="edit-description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input id="edit-image" type="file" class="form-control" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning" id="saveEditBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Add New Medicine</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input id="add-name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select id="add-category" class="form-select"></select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Price ($)</label>
                        <input id="add-price" type="number" class="form-control" min="0" step="0.01" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input id="add-stock" type="number" class="form-control" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Age Restriction (e.g., 18+, 21+)</label>
                        <input id="add-age-restriction" class="form-control" placeholder="Leave blank if none">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input id="add-expiry-date" type="date" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea id="add-description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input id="add-image" type="file" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" id="saveAddBtn">Add Medicine</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        let medicines = [];
        let categories = [];
        let currentViewingId = null;

        // Fetch medicines and categories on page load
        async function fetchMedicines() {
            try {
                const response = await fetch('/medicines', { credentials: "same-origin" });
                if (!response.ok) throw new Error("Failed to fetch medicines");
                const data = await response.json();
                medicines = data.data || [];
                renderTable();
            } catch (error) {
                console.error(error);
                alert("Error loading medicines.");
            }
        }

        async function fetchCategories() {
            try {
                const response = await fetch('/categories', { credentials: "same-origin" });
                if (!response.ok) throw new Error("Failed to fetch categories");
                const data = await response.json();
                categories = data.data || [];
                populateCategorySelects();
                renderCategoryFilter();
            } catch (error) {
                console.error(error);
            }
        }

        function populateCategorySelects() {
            const buildOptions = () => {
                let html = '<option value="">Select a category...</option>';
                categories.forEach(cat => {
                    if (!cat.parent_id) {
                        html += `<option value="${cat.id}">${cat.name}</option>`;
                        if (cat.subcategories && cat.subcategories.length > 0) {
                            cat.subcategories.forEach(sub => {
                                html += `<option value="${sub.id}">  → ${sub.name}</option>`;
                            });
                        }
                    }
                });
                return html;
            };
            
            const html = buildOptions();
            document.getElementById('add-category').innerHTML = html;
            document.getElementById('edit-category').innerHTML = html;
        }

        function renderCategoryFilter() {
            const select = document.getElementById('categoryFilter');
            let html = '<option value="">All Categories</option>';
            
            categories.forEach(cat => {
                if (!cat.parent_id) {
                    html += `<option value="${cat.id}">${cat.name}</option>`;
                    if (cat.subcategories && cat.subcategories.length > 0) {
                        cat.subcategories.forEach(sub => {
                            html += `<option value="${sub.id}">  → ${sub.name}</option>`;
                        });
                    }
                }
            });
            
            select.innerHTML = html;
        }

        function renderTable() {
            const tbody = document.getElementById('medicinesTableBody');
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categoryId = document.getElementById('categoryFilter').value;

            let filtered = medicines.filter(med => {
                const matchesSearch = med.name.toLowerCase().includes(searchTerm) || 
                                    (med.description && med.description.toLowerCase().includes(searchTerm));
                const matchesCategory = !categoryId || med.category_id == categoryId;
                return matchesSearch && matchesCategory;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No medicines found</td></tr>';
                return;
            }

            tbody.innerHTML = filtered.map(med => `
                <tr>
                    <td>
                        ${med.image || med.images?.[0] ? `<img src="/storage/${med.image || med.images[0]}" class="medicine-image" alt="${med.name}">` : '<span class="text-muted">No image</span>'}
                    </td>
                    <td><strong>${med.name}</strong></td>
                    <td>${med.category?.name || '-'}</td>
                    <td>$${Number(med.price).toFixed(2)}</td>
                    <td><span class="badge ${med.stock > 10 ? 'bg-success' : med.stock > 0 ? 'bg-warning' : 'bg-danger'}">${med.stock}</span></td>
                    <td>${med.age_restriction || '-'}</td>
                    <td>${med.expiry_date ? new Date(med.expiry_date).toLocaleDateString() : '-'}</td>
                    <td><small>${med.description ? med.description.substring(0, 50) + '...' : '-'}</small></td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-info" onclick="openViewModal(${med.id})" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            @if(session('level') == 3 || session('level') == 4)
                            <button class="btn btn-sm btn-warning" onclick="openEditFromTable(${med.id})" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteFromTable(${med.id})" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function openViewModal(id) {
            currentViewingId = id;
            const medicine = medicines.find(m => m.id === id);
            if (!medicine) return;

            document.getElementById('viewName').textContent = medicine.name;
            document.getElementById('viewNameText').textContent = medicine.name;
            document.getElementById('viewCategory').textContent = medicine.category?.name || '-';
            document.getElementById('viewPrice').textContent = Number(medicine.price).toFixed(2);
            document.getElementById('viewStock').textContent = medicine.stock;
            document.getElementById('viewAgeRestriction').textContent = medicine.age_restriction || '-';
            document.getElementById('viewExpiryDate').textContent = medicine.expiry_date ? new Date(medicine.expiry_date).toLocaleDateString() : '-';
            document.getElementById('viewDescription').textContent = medicine.description || 'No description';
            
            const imageUrl = medicine.image || medicine.images?.[0];
            if (imageUrl) {
                document.getElementById('viewImage').src = '/storage/' + imageUrl;
            } else {
                document.getElementById('viewImage').src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23f0f0f0" width="200" height="200"/%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="14" fill="%23999"%3ENo Image%3C/text%3E%3C/svg%3E';
            }

            const viewModal = new bootstrap.Modal(document.getElementById('viewModal'));
            viewModal.show();
        }

        function openEditFromTable(id) {
            const medicine = medicines.find(m => m.id === id);
            if (!medicine) return;

            document.getElementById('edit-id').value = medicine.id;
            document.getElementById('edit-name').value = medicine.name;
            document.getElementById('edit-category').value = medicine.category_id || '';
            document.getElementById('edit-price').value = medicine.price;
            document.getElementById('edit-stock').value = medicine.stock;
            document.getElementById('edit-age-restriction').value = medicine.age_restriction || '';
            document.getElementById('edit-expiry-date').value = medicine.expiry_date || '';
            document.getElementById('edit-description').value = medicine.description || '';

            new bootstrap.Modal(document.getElementById('editModal')).show();
        }

        document.getElementById('openEditBtn').addEventListener('click', () => {
            const medicine = medicines.find(m => m.id === currentViewingId);
            if (!medicine) return;

            document.getElementById('edit-id').value = medicine.id;
            document.getElementById('edit-name').value = medicine.name;
            document.getElementById('edit-category').value = medicine.category_id || '';
            document.getElementById('edit-price').value = medicine.price;
            document.getElementById('edit-stock').value = medicine.stock;
            document.getElementById('edit-age-restriction').value = medicine.age_restriction || '';
            document.getElementById('edit-expiry-date').value = medicine.expiry_date || '';
            document.getElementById('edit-description').value = medicine.description || '';

            bootstrap.Modal.getInstance(document.getElementById('viewModal')).hide();
            new bootstrap.Modal(document.getElementById('editModal')).show();
        });

        async function deleteFromTable(id) {
            if (!confirm('Are you sure you want to delete this medicine?')) return;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                const response = await fetch(`/medicines/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    credentials: 'same-origin'
                });
                if (response.ok) {
                    alert('Medicine deleted successfully');
                    fetchMedicines();
                } else {
                    alert('Failed to delete medicine');
                }
            } catch (error) {
                console.error(error);
                alert('Error deleting medicine');
            }
        }

        document.getElementById('deleteBtn').addEventListener('click', async () => {
            if (!confirm('Are you sure you want to delete this medicine?')) return;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                const response = await fetch(`/medicines/${currentViewingId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    credentials: 'same-origin'
                });
                if (response.ok) {
                    alert('Medicine deleted successfully');
                    fetchMedicines();
                    bootstrap.Modal.getInstance(document.getElementById('viewModal')).hide();
                } else {
                    alert('Failed to delete medicine');
                }
            } catch (error) {
                console.error(error);
                alert('Error deleting medicine');
            }
        });

        document.getElementById('saveEditBtn').addEventListener('click', async () => {
            const id = document.getElementById('edit-id').value;
            const name = document.getElementById('edit-name').value;
            const category_id = document.getElementById('edit-category').value || null;
            const price = document.getElementById('edit-price').value;
            const stock = document.getElementById('edit-stock').value;
            const age_restriction = document.getElementById('edit-age-restriction').value || null;
            const expiry_date = document.getElementById('edit-expiry-date').value || null;
            const description = document.getElementById('edit-description').value;
            const imageInput = document.getElementById('edit-image');

            const formData = new FormData();
            formData.append('name', name);
            formData.append('category_id', category_id);
            formData.append('price', price);
            formData.append('stock', stock);
            formData.append('age_restriction', age_restriction);
            formData.append('expiry_date', expiry_date);
            formData.append('description', description);
            if (imageInput.files.length > 0) {
                formData.append('images[]', imageInput.files[0]);
            }
            formData.append('_method', 'PUT');

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                const response = await fetch(`/medicines/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData,
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    alert('Medicine updated successfully');
                    fetchMedicines();
                    bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                } else {
                    alert('Failed to update medicine');
                }
            } catch (error) {
                console.error(error);
                alert('Error updating medicine');
            }
        });

        document.getElementById('saveAddBtn').addEventListener('click', async () => {
            const name = document.getElementById('add-name').value;
            const category_id = document.getElementById('add-category').value || null;
            const price = document.getElementById('add-price').value;
            const stock = document.getElementById('add-stock').value;
            const age_restriction = document.getElementById('add-age-restriction').value || null;
            const expiry_date = document.getElementById('add-expiry-date').value || null;
            const description = document.getElementById('add-description').value;
            const imageInput = document.getElementById('add-image');

            if (!name || !price || !stock || imageInput.files.length === 0) {
                alert('Please fill all required fields');
                return;
            }

            const formData = new FormData();
            formData.append('name', name);
            formData.append('category_id', category_id);
            formData.append('price', price);
            formData.append('stock', stock);
            formData.append('age_restriction', age_restriction);
            formData.append('expiry_date', expiry_date);
            formData.append('description', description);
            formData.append('images[]', imageInput.files[0]);

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                const response = await fetch('/medicines/add', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    body: formData,
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    alert('Medicine added successfully');
                    document.getElementById('add-name').value = '';
                    document.getElementById('add-category').value = '';
                    document.getElementById('add-price').value = '';
                    document.getElementById('add-stock').value = '';
                    document.getElementById('add-age-restriction').value = '';
                    document.getElementById('add-expiry-date').value = '';
                    document.getElementById('add-description').value = '';
                    document.getElementById('add-image').value = '';
                    
                    fetchMedicines();
                    bootstrap.Modal.getInstance(document.getElementById('addModal')).hide();
                } else {
                    alert('Failed to add medicine');
                }
            } catch (error) {
                console.error(error);
                alert('Error adding medicine');
            }
        });

        document.getElementById('searchInput').addEventListener('input', renderTable);
        document.getElementById('categoryFilter').addEventListener('change', renderTable);

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            fetchCategories();
            fetchMedicines();
        });
    </script>
@endsection
