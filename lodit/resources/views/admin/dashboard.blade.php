<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LODIT</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        :root {
            --sidebar-width: 260px;
            --page-bg: #1e1e1e;
            --text-primary: #f5f5f5;
            --text-secondary: #b0b0b0;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--page-bg);
            color: var(--text-primary);
        }

        main {
            margin-left: var(--sidebar-width, 260px);
            padding: 30px 20px;
            transition: margin-left 0.3s ease;
            background-color: var(--page-bg);
            min-height: 100vh;
        }

        @media (max-width: 768px) {
            main {
                margin-left: 70px;
            }
        }

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .card {
            background-color: #2a2a2a;
            border: 1px solid #444;
            color: #f5f5f5;
        }

        .card-header {
            background-color: #3d3d3d !important;
            border-bottom: 1px solid #555 !important;
        }

        .card-header h5 {
            color: #f0f0f0;
        }

        .stat-card {
            background: linear-gradient(135deg, #2a2a2a 0%, #333 100%);
            border: 1px solid #444;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: #555;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .stat-card .card-body {
            padding: 1.5rem;
        }

        .stat-card .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: #b0b0b0;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #b0b0b0;
            margin: 0;
        }

        .btn-primary {
            background-color: #b0b0b0;
            border: none;
            color: #1e1e1e;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #d0d0d0;
            color: #000;
        }

        .btn-info {
            background-color: #789;
            border: none;
            color: white;
        }

        .btn-info:hover {
            background-color: #89a;
        }

        .btn-warning {
            background-color: #a08;
            border: none;
            color: white;
        }

        .btn-warning:hover {
            background-color: #b19;
        }

        .btn-danger {
            background-color: #a44;
            border: none;
            color: white;
        }

        .btn-danger:hover {
            background-color: #b55;
        }

        .btn-secondary {
            background-color: #555;
            border: none;
            color: #f5f5f5;
        }

        .btn-secondary:hover {
            background-color: #666;
        }

        .modal-content {
            background-color: #2a2a2a;
            color: #f5f5f5;
            border: 1px solid #444;
        }

        .modal-header {
            background-color: #3d3d3d;
            border-bottom: 1px solid #555;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .form-control, .form-select {
            background-color: #2f2f2f;
            border-color: #555;
            color: #f5f5f5;
        }

        .form-control:focus, .form-select:focus {
            background-color: #333;
            border-color: #777;
            color: #f5f5f5;
        }

        .form-label {
            color: #e0e0e0;
        }

        .alert {
            background-color: #2a2a2a;
            border-color: #444;
            color: #f5f5f5;
        }

        .alert-success {
            background-color: #1a3a2a;
            border-color: #3a6a5a;
        }

        .alert-danger {
            background-color: #3a1a1a;
            border-color: #6a3a3a;
        }

        .table {
            color: #f5f5f5;
            background-color: #2a2a2a;
        }

        .table thead {
            background-color: #3d3d3d;
        }

        .table thead th {
            color: #f0f0f0;
            border-color: #555;
        }

        .table tbody td {
            border-color: #444;
            color: #e0e0e0;
            background-color: #2a2a2a;
        }

        .table tbody tr:hover {
            background-color: #2f2f2f;
        }

        .badge {
            padding: 0.4em 0.6em;
            font-size: 0.85em;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .filter-section {
            background-color: #2a2a2a;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #444;
        }

        .page-link {
            background-color: #2f2f2f;
            color: #f5f5f5;
            border-color: #555;
        }

        .page-link:hover {
            background-color: #3d3d3d;
            color: #f5f5f5;
            border-color: #666;
        }

        .page-link.active {
            background-color: #b0b0b0;
            border-color: #b0b0b0;
            color: #1e1e1e;
        }
    </style>
</head>
<body>
    @include('components.sidebar')
    @include('components.top-navbar')

    <main>
        <div class="container-fluid admin-container">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Total Users</h6>
                                    <h2 class="stat-number">{{ $totalUsers }}</h2>
                                </div>
                                <div>
                                    <i class="bi bi-people" style="font-size: 2.5rem; color: #789;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Orders Today</h6>
                                    <h2 class="stat-number">24</h2>
                                </div>
                                <div>
                                    <i class="bi bi-cart-check" style="font-size: 2.5rem; color: #8a9;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Revenue</h6>
                                    <h2 class="stat-number">$2.4K</h2>
                                </div>
                                <div>
                                    <i class="bi bi-cash-coin" style="font-size: 2.5rem; color: #a8a;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title text-muted mb-2">Pending</h6>
                                    <h2 class="stat-number">5</h2>
                                </div>
                                <div>
                                    <i class="bi bi-exclamation-circle" style="font-size: 2.5rem; color: #a88;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                    <i class="bi bi-person-plus"></i> Add User
                                </button>
                                <a href="/admin/purchase-history" class="btn btn-info">
                                    <i class="bi bi-graph-up"></i> Purchase History
                                </a>
                                <a href="/admin/payment-confirmations" class="btn btn-info">
                                    <i class="bi bi-credit-card"></i> Payments
                                </a>
                                <a href="/admin/pending-consultations" class="btn btn-info">
                                    <i class="bi bi-chat-dots"></i> Consultations
                                </a>
                                <a href="/delivery/all" class="btn btn-info">
                                    <i class="bi bi-box-seam"></i> Deliveries
                                </a>
                                <a href="/prescription/pending" class="btn btn-info">
                                    <i class="bi bi-capsule"></i> Prescriptions
                                </a>
                                @if(session('level') == 4)
                                    <a href="/superadmin/pending-changes" class="btn btn-warning">
                                        <i class="bi bi-exclamation-triangle"></i> Pending Changes
                                    </a>
                                    <a href="/superadmin/audit-log" class="btn btn-info">
                                        <i class="bi bi-clipboard-check"></i> Audit Log
                                    </a>
                                @endif
                                @if(session('level') >= 6)
                                    <a href="/manager/dashboard" class="btn btn-info">
                                        <i class="bi bi-graph-up-arrow"></i> Manager Dashboard
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Filters & Search -->
            <div class="filter-section">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="d-flex gap-2 flex-wrap">
                    <input type="text" name="search" class="form-control" placeholder="Search username or ID..." 
                        value="{{ $searchTerm }}" style="max-width: 200px;">
                    
                    <select name="filter_level" class="form-select" style="max-width: 150px;">
                        <option value="">All Levels</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->lvlnumber }}" {{ $filterLevel == $level->lvlnumber ? 'selected' : '' }}>
                                {{ $level->beingas }}
                            </option>
                        @endforeach
                    </select>

                    <select name="sort_by" class="form-select" style="max-width: 150px;">
                        <option value="id" {{ $sortBy == 'id' ? 'selected' : '' }}>Sort by ID</option>
                        <option value="username" {{ $sortBy == 'username' ? 'selected' : '' }}>Sort by Name</option>
                        <option value="level" {{ $sortBy == 'level' ? 'selected' : '' }}>Sort by Level</option>
                        <option value="created_at" {{ $sortBy == 'created_at' ? 'selected' : '' }}>Sort by Date</option>
                    </select>

                    <select name="sort_order" class="form-select" style="max-width: 120px;">
                        <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Descending</option>
                    </select>

                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Clear</a>
                </form>
            </div>

            <!-- Users Table -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">User Management</h5>
                        </div>
                        <div class="card-body p-0">
                            @if(count($users) > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Username</th>
                                                <th>Level</th>
                                                <th>Created</th>
                                                <th style="width: 150px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($users as $user)
                                                <tr>
                                                    <td>{{ $user->id }}</td>
                                                    <td><strong>{{ $user->username }}</strong></td>
                                                    <td>
                                                        <span class="badge" style="background-color: #789;">{{ $user->level_name }}</span>
                                                    </td>
                                                    <td>{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('M d, Y') : 'N/A' }}</td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                                            Edit
                                                        </button>
                                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal{{ $user->id }}">
                                                            PWD
                                                        </button>
                                                        @if(session('id') != $user->id)
                                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                                                                Del
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>

                                                <!-- Edit User Modal -->
                                                <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit User: {{ $user->username }}</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('admin.update-user', $user->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Username</label>
                                                                        <input type="text" class="form-control" name="username" value="{{ $user->username }}" required>
                                                                    </div>
                                                                    @if(session('level') == 4)
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Level</label>
                                                                        <select class="form-select" name="level" required>
                                                                            @foreach($levels as $level)
                                                                                <option value="{{ $level->lvlnumber }}" {{ $user->level == $level->lvlnumber ? 'selected' : '' }}>
                                                                                    {{ $level->beingas }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    @else
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Level</label>
                                                                        <input type="text" class="form-control" value="{{ $user->level_name }}" disabled>
                                                                        <input type="hidden" name="level" value="{{ $user->level }}">
                                                                        <small class="text-muted">Only super admin can change levels</small>
                                                                    </div>
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Change Password Modal -->
                                                <div class="modal fade" id="changePasswordModal{{ $user->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Change Password: {{ $user->username }}</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form action="{{ route('admin.update-password', $user->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">New Password</label>
                                                                        <input type="password" class="form-control" name="password" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Confirm Password</label>
                                                                        <input type="password" class="form-control" name="password_confirmation" required>
                                                                    </div>
                                                                    <small class="text-muted">Minimum 6 characters</small>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-warning">Update Password</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Delete User Modal -->
                                                <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header" style="background-color: #5a2a2a;">
                                                                <h5 class="modal-title">Remove User: {{ $user->username }}</h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="alert alert-warning">
                                                                    <strong>⚠️ Confirmation Required:</strong> This user will be removed from the system.
                                                                </div>
                                                                <p>Are you sure you want to remove <strong>{{ $user->username }}</strong>?</p>
                                                                <label class="form-label">Type username to confirm:</label>
                                                                <input type="text" class="form-control confirm-delete-input" placeholder="Type username" data-username="{{ $user->username }}" data-modal-id="deleteUserModal{{ $user->id }}">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <form action="{{ route('admin.delete-user', $user->id) }}" method="POST" style="display: inline;">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-danger delete-confirm-btn" data-modal-id="deleteUserModal{{ $user->id }}" disabled>
                                                                        Remove User
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-warning m-3 mb-0">
                                    No users found.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if($totalUsers > $perPage)
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            @for($i = 1; $i <= ceil($totalUsers / $perPage); $i++)
                                <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                    <a class="page-link" href="{{ route('admin.dashboard', array_merge(request()->query(), ['page' => $i])) }}">
                                        {{ $i }}
                                    </a>
                                </li>
                            @endfor
                        </ul>
                    </nav>
                </div>
            @endif
        </div>

        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New User</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.store-user') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Level</label>
                                <select class="form-select" name="level" required>
                                    <option value="">-- Select Level --</option>
                                    @if(session('level') == 4)
                                        @foreach($levels as $level)
                                            <option value="{{ $level->lvlnumber }}">{{ $level->beingas }}</option>
                                        @endforeach
                                    @else
                                        @foreach($levels as $level)
                                            @if($level->lvlnumber != 4)
                                                <option value="{{ $level->lvlnumber }}">{{ $level->beingas }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <small class="text-muted">Password must be at least 6 characters</small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        document.querySelectorAll('.confirm-delete-input').forEach(input => {
            input.addEventListener('input', function() {
                const modalId = this.dataset.modalId;
                const username = this.dataset.username;
                const deleteBtn = document.querySelector(`#${modalId} .delete-confirm-btn`);
                deleteBtn.disabled = this.value !== username;
            });
        });
    </script>
</body>
</html>
