@extends('layouts.app')

@section('title', 'Database Management Menu')

@section('styles')
<style>
    .menu-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .menu-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        padding: 50px;
        max-width: 600px;
        width: 100%;
    }

    .menu-title {
        text-align: center;
        margin-bottom: 40px;
        font-size: 2.5rem;
        font-weight: bold;
        color: #333;
    }

    .menu-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .menu-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 30px 20px;
        border-radius: 12px;
        text-decoration: none;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        color: white;
        min-height: 150px;
    }

    .menu-btn i {
        font-size: 2.5rem;
        margin-bottom: 10px;
    }

    .menu-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    .menu-btn.medicines {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .menu-btn.medicines:hover {
        border-color: #667eea;
        background: linear-gradient(135deg, #5568d3 0%, #6a4593 100%);
    }

    .menu-btn.reports {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .menu-btn.reports:hover {
        border-color: #f5576c;
        background: linear-gradient(135deg, #e87fea 0%, #e34359 100%);
    }

    .menu-btn.users {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .menu-btn.users:hover {
        border-color: #00f2fe;
        background: linear-gradient(135deg, #3a98eb 0%, #00d9f0 100%);
    }

    .menu-btn.backup {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .menu-btn.backup:hover {
        border-color: #38f9d7;
        background: linear-gradient(135deg, #2ed46f 0%, #25e5c8 100%);
    }

    .menu-btn.restart {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .menu-btn.restart:hover {
        border-color: #fee140;
        background: linear-gradient(135deg, #f05080 0%, #fdd830 100%);
    }

    .danger-zone {
        background: #fff3cd;
        border: 2px solid #ffc107;
        border-radius: 12px;
        padding: 20px;
        margin-top: 30px;
        text-align: center;
    }

    .danger-zone p {
        color: #856404;
        margin: 0;
        font-size: 0.95rem;
    }

    @media (max-width: 600px) {
        .menu-card {
            padding: 30px 20px;
        }

        .menu-title {
            font-size: 1.8rem;
        }

        .menu-options {
            grid-template-columns: 1fr;
        }

        .menu-btn {
            min-height: 120px;
            padding: 20px;
        }

        .menu-btn i {
            font-size: 2rem;
        }
    }
</style>
@endsection

@section('content')
<div class="menu-container">
    <div class="menu-card">
        <h1 class="menu-title">
            <i class="bi bi-database"></i> Database Menu
        </h1>

        <div class="menu-options">
            {{-- Medicines --}}
            <a href="{{ route('medicines.index') }}" class="menu-btn medicines">
                <i class="bi bi-capsule"></i>
                <span>Medicines</span>
            </a>

            {{-- Reports --}}
            <a href="{{ route('reports.index') }}" class="menu-btn reports">
                <i class="bi bi-bar-chart"></i>
                <span>Reports</span>
            </a>

            {{-- Users --}}
            <a href="{{ route('users.index') }}" class="menu-btn users">
                <i class="bi bi-people"></i>
                <span>Users</span>
            </a>

            {{-- Backup --}}
            <a href="{{ route('database.backup') }}" class="menu-btn backup">
                <i class="bi bi-cloud-download"></i>
                <span>Backup DB</span>
            </a>
        </div>

        {{-- Restart Database (Danger Zone) --}}
        <div class="danger-zone">
            <p><strong>⚠️ Danger Zone</strong></p>
            <p style="margin-top: 10px;">Restarting the database will clear all data. Use with caution!</p>
            <a href="{{ route('database.restart') }}" 
               class="menu-btn restart" 
               style="margin-top: 15px; max-width: 200px; margin-left: auto; margin-right: auto;"
               onclick="return confirm('Are you sure? This will restart the database!');">
                <i class="bi bi-arrow-clockwise"></i>
                <span>Restart DB</span>
            </a>
        </div>
    </div>
</div>
@endsection
