@extends('layouts.app')

@section('title', 'Database Backup & Reset - LODIT')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">

        @if(session('success'))
            <div style="background: #2a5f2a; color: #a0ffa0; padding: 12px 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #4db34d;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #5f2a2a; color: #ffa0a0; padding: 12px 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #b34d4d;">
                {{ session('error') }}
            </div>
        @endif

        <!-- Backup Section -->
        <div style="background: #2a2a2a; padding: 25px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #3a3a3a;">
            <h3 style="color: #ffffff; margin-top: 0; margin-bottom: 15px;">📥 Create Backup</h3>
            <p style="color: #b0b0b0; margin-bottom: 15px;">Create a backup of the entire database. This will save all data to a file.</p>
            
            <form method="POST" action="/superadmin/backup-reset/backup">
                @csrf
                <button type="submit" style="background: #4a7fd9; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 14px;">
                    <i class="bi bi-cloud-arrow-down"></i> Create Backup Now
                </button>
            </form>
        </div>

        <!-- Backups List -->
        @if(count($backups) > 0)
            <div style="background: #2a2a2a; padding: 25px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #3a3a3a;">
                <h3 style="color: #ffffff; margin-top: 0; margin-bottom: 15px;">📋 Existing Backups</h3>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; background-color: #1e1e1e; border: 1px solid #444; border-radius: 8px;">
                        <thead>
                            <tr style="background-color: #1a1a1a;">
                                <th style="padding: 15px; text-align: left; color: #ccc; font-weight: 600; border-bottom: 2px solid #444;">Filename</th>
                                <th style="padding: 15px; text-align: left; color: #ccc; font-weight: 600; border-bottom: 2px solid #444;">Size</th>
                                <th style="padding: 15px; text-align: left; color: #ccc; font-weight: 600; border-bottom: 2px solid #444;">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                                <tr style="border-bottom: 1px solid #3a3a3a;">
                                    <td style="padding: 12px 15px; color: #ddd;">{{ $backup['filename'] }}</td>
                                    <td style="padding: 12px 15px; color: #ddd;">{{ number_format($backup['size'] / 1024, 2) }} KB</td>
                                    <td style="padding: 12px 15px; color: #999; font-size: 12px;">{{ date('M d, Y H:i:s', $backup['created']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div style="background: #2a2a2a; padding: 40px; border-radius: 8px; margin-bottom: 30px; text-align: center; border: 1px solid #3a3a3a;">
                <p style="color: #b0b0b0; margin: 0;">No backups found. Create one to get started.</p>
            </div>
        @endif

        <!-- Reset Section -->
        <div style="background: #5f2a2a; padding: 25px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #8a4a4a;">
            <h3 style="color: #ffcccc; margin-top: 0; margin-bottom: 15px;">⚠️ Reset Database</h3>
            <p style="color: #b0b0b0; margin-bottom: 10px;">This will completely reset the database and delete all data. A backup will be automatically created before reset.</p>
            <p style="color: #ffa0a0; font-weight: 600; margin-bottom: 15px;">This action cannot be undone!</p>
            
            <form method="POST" action="/superadmin/backup-reset/reset" onsubmit="return confirm('Are you absolutely sure? This will delete ALL data. A backup will be created, but you cannot undo this action.');">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label style="display: block; color: #b0b0b0; margin-bottom: 8px; font-weight: 600;">
                        <input type="checkbox" name="confirm" value="yes" style="margin-right: 8px;" required>
                        I understand this will delete all data and cannot be undone
                    </label>
                </div>
                <button type="submit" style="background: #c94c4c; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 14px;">
                    <i class="bi bi-exclamation-triangle"></i> Reset Database
                </button>
            </form>
        </div>

        <!-- Info Section -->
        <div style="background: #2a3a4a; padding: 20px; border-radius: 8px; border: 1px solid #3a4a5a;">
            <h4 style="color: #7cb9ff; margin-top: 0; margin-bottom: 15px;">ℹ️ Information</h4>
            <ul style="color: #b0b0b0; margin: 0; padding-left: 20px; line-height: 1.8;">
                <li>Backups are stored in the <code style="background: #1e1e1e; padding: 2px 6px; border-radius: 3px; color: #a0ffa0;">storage/backups</code> directory</li>
                <li>Each backup includes all database tables and data</li>
                <li>Before any reset, a backup is automatically created with the prefix <code style="background: #1e1e1e; padding: 2px 6px; border-radius: 3px; color: #a0ffa0;">pre-reset-backup_</code></li>
                <li>Manual backups have the prefix <code style="background: #1e1e1e; padding: 2px 6px; border-radius: 3px; color: #a0ffa0;">backup_</code></li>
            </ul>
        </div>

        <!-- Import / Export Section -->
        <div style="background: #2a2a2a; padding: 25px; border-radius: 8px; margin-top: 20px; border: 1px solid #3a3a3a;">
            <h3 style="color: #ffffff; margin-top: 0; margin-bottom: 15px;">🔁 Import / Export CSV</h3>
            <div style="display:flex; gap:20px; flex-wrap:wrap;">
                <div style="flex:1; min-width:260px;">
                    <h4 style="color:#cfe8ff;">Users</h4>
                    <p style="color:#b0b0b0;">Export or import user accounts. Imported users will be upserted by `id` if present.</p>
                    <a href="/superadmin/export/users" class="btn btn-primary" style="display:inline-block; margin-bottom:8px;">Export Users (.csv)</a>
                    <form method="POST" action="/superadmin/import/users" enctype="multipart/form-data" style="margin-top:8px;">
                        @csrf
                        <input type="file" name="file" accept="text/csv" required>
                        <button type="submit" class="btn btn-success" style="margin-left:8px;">Import Users</button>
                    </form>
                </div>

                <div style="flex:1; min-width:260px;">
                    <h4 style="color:#cfe8ff;">Medicines</h4>
                    <p style="color:#b0b0b0;">Export or import medicines. Imported rows will upsert by `id` if present.</p>
                    <a href="/superadmin/export/medicines" class="btn btn-primary" style="display:inline-block; margin-bottom:8px;">Export Medicines (.csv)</a>
                    <form method="POST" action="/superadmin/import/medicines" enctype="multipart/form-data" style="margin-top:8px;">
                        @csrf
                        <input type="file" name="file" accept="text/csv" required>
                        <button type="submit" class="btn btn-success" style="margin-left:8px;">Import Medicines</button>
                    </form>
                </div>
            </div>
        </div>
</div>
@endsection
