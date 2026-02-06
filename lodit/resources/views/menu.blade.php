<style>
body {
    background-color: #1e1e1e;
    background-size: cover;
    color: #000;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0; 
}

table {
    background-color: #2d2d2dff;
    width: 80%;
    border-collapse: collapse;
    max-width: 900px;
    margin: 100px auto 0 auto;
    box-shadow: 0 8px 20px rgba(0,0,0,0.4);
}

html {
    scroll-behavior: smooth;
}

th, td {
    padding: 14px;
    text-align: left;
    border-bottom: 1px solid #333;
    color: white;
}

th {
    background-color: #3d3d3dff;
}

tr:hover {
    background-color: #262626;
    transition: 0.3s;
}

a.btn-edit {
    background-color: #007bff;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    text-decoration: none;
}

a.btn-delete {
    background-color: #dc3545;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    text-decoration: none;
}

.navbar-custom {
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(10px);
    border-bottom: 2px solid #888;
}

.navbar-custom .navbar-brand {
    color: #f8f9fa;
    font-weight: bold;
    font-size: 1.5rem;
    transition: color 0.3s;
}

.navbar-custom .navbar-brand:hover {
    color: #00bfff;
}

.navbar-custom .nav-link {
    color: #ddd;
    margin-right: 15px;
    transition: color 0.3s;
}

.navbar-custom .nav-link:hover {
    color: #00bfff;
}

.navbar-custom .nav-link.active {
    color: #00bfff;
    font-weight: 600;
}

.table-dark td, .table-dark th {
    border-color: #444 !important;
}

.table-secondary th {
    background-color: #d1d1d1 !important;
}

.btn {
    border-radius: 10px !important;
}

/* FIXED print styles */
@media print {

    a.btn, button.btn, form, .collapse, hr {
        display: none !important;
    }

    body {
        background: #fff !important;
        color: #000 !important;
    }

    table, th, td {
        background: transparent !important;
        color: black !important;
        border: none !important;
        box-shadow: none !important;
    }

    tr:hover, thead {
        background: transparent !important;
    }

    .text-success,
    .text-danger,
    .text-info {
        color: black !important;
    }

    .table,
    .table-bordered,
    .table-striped,
    .table-active {
        border: none !important;
    }
}
</style>

</head>

<body>
<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container-fluid">
        @php
            $appName = \Illuminate\Support\Facades\DB::table('app_settings')->where('setting_key', 'app_name')->value('setting_value') ?? 'KIPS';
            $appLogo = \Illuminate\Support\Facades\DB::table('app_settings')->where('setting_key', 'app_logo_path')->value('setting_value') ?? 'foto/logo.jpg';
        @endphp

        <a class="navbar-brand d-flex align-items-center" href="/kli">
            <img src="{{ asset($appLogo) }}" alt="Logo"
                 style="height: 40px; width: auto; margin-right: 10px;">
            {{ $appName }}
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>


        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link" href="/kli">Home</a>
                </li>

                <!-- @if(session('level') == 3)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownData"
                       role="button" data-bs-toggle="dropdown">
                        Data
                    </a>
                    <ul class="dropdown-menu bg-secondary text-white">
                        <li><a class="dropdown-item text-white" href="/dok">Medicine</a></li>
                        <li><a class="dropdown-item text-white" href="/users">Users</a></li>
                    </ul>
                </li>
                @endif -->


                <!-- @if(session('level') == 3)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAdd"
                       role="button" data-bs-toggle="dropdown">
                        Add Data
                    </a>
                    <ul class="dropdown-menu bg-secondary text-white">
                        <li><a class="dropdown-item text-white" href="/tambah">Add Medication</a></li>

                        <li><a class="dropdown-item text-white" href="/addEmployee">Add User</a></li>
                    </ul>
                </li>
                @endif -->


                <li class="nav-item"><a class="nav-link" href="/yao">Medicine</a></li>
                
                @if(session('level') == 3)
                <li class="nav-item"><a class="nav-link" href="/admin/dashboard">Admin Panel</a></li>
                @endif
                
                @if(session('level') == 3 || session('level') == 4 || session('level') == 5 || session('level') == 6)
                <li class="nav-item"><a class="nav-link" href="/stock-management">Stock Management</a></li>
                <li class="nav-item"><a class="nav-link" href="/medicine-report">Medicine Report</a></li>
                @endif

                @if(session('level') == 4)
                <li class="nav-item"><a class="nav-link" href="/deleted-transactions">Deleted Transactions</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownSuperAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Admin Panel
                    </a>
                    <ul class="dropdown-menu bg-secondary text-white" aria-labelledby="navbarDropdownSuperAdmin">
                        <li><a class="dropdown-item text-white" href="/superadmin/pending-changes">Pending Changes</a></li>
                        <li><a class="dropdown-item text-white" href="/superadmin/audit-log">Audit Log</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-white" href="/superadmin/hidden-users">Removed Users</a></li>
                    </ul>
                </li>
                @endif

                @if(session('level') == 2)
                <li class="nav-item"><a class="nav-link" href="/transactions">Transactions</a></li>
                <li class="nav-item"><a class="nav-link" href="/medtransactions">Medicine Transactions</a></li>
                @endif


                <li class="nav-item"><a class="nav-link" href="/settings">Settings</a></li>
                <li class="nav-item"><a class="nav-link" href="/logout">Log Out</a></li>

            </ul>
        </div>

    </div>
</nav>

  
</body>
