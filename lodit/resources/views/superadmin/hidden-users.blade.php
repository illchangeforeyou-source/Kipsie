@extends('layouts.app')

@section('title', 'Recently Deleted')

@section('content')
    <div class="container">
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

        <h2 style="color: #ffffff; margin-bottom: 12px;">Recently Deleted — Users</h2>
        @if($users->count() == 0)
            <div style="background: #2a2a2a; padding: 20px; border-radius: 8px; border: 1px solid #3a3a3a; color: #b0b0b0;">No deleted users.</div>
        @else
            <div style="display: grid; gap: 12px;">
                @foreach($users as $user)
                    <div style="background: #2a2a2a; border: 1px solid #3a3a3a; border-radius: 8px; padding: 12px; display:flex; align-items:center; justify-content:space-between;">
                        <div style="display:flex; gap:12px; align-items:center;">
                            <div style="width:48px; height:48px; border-radius:6px; background:#1e1e1e; display:flex; align-items:center; justify-content:center; color:#cfe8ff; font-weight:bold;">#{{ $user->id }}</div>
                            <div>
                                <div style="color:#ffffff; font-weight:700;">{{ $user->username }}</div>
                                <div style="color:#b0b0b0; font-size:13px;">{{ $user->employeename }} • {{ $user->level_name }}</div>
                                <div style="color:#888; font-size:12px; margin-top:4px;">Removed: {{ date('M d, Y H:i', strtotime($user->hidden_at)) }}</div>
                            </div>
                        </div>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <form method="POST" action="/superadmin/user/{{ $user->id }}/unhide" style="display:inline">
                                @csrf
                                <button class="btn" style="background:#4cb970; color:white; border:none; padding:8px 12px; border-radius:6px; font-weight:600;">Restore</button>
                            </form>
                            <form method="POST" action="/superadmin/user/{{ $user->id }}/permanent-delete" style="display:inline">
                                @csrf
                                <button class="btn" style="background:#c94c4c; color:white; border:none; padding:8px 12px; border-radius:6px; font-weight:600;" onclick="return confirm('Permanently delete this user? This cannot be undone.');">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <h2 style="color: #ffffff; margin: 28px 0 12px 0;">Recently Deleted — Medicines</h2>
        @if(empty($deletedMedicines) || count($deletedMedicines) == 0)
            <div style="background: #2a2a2a; padding: 20px; border-radius: 8px; border: 1px solid #3a3a3a; color: #b0b0b0;">No deleted medicines.</div>
        @else
            <div style="display: grid; gap: 12px;">
                @foreach($deletedMedicines as $m)
                    <div style="background: #2a2a2a; border: 1px solid #3a3a3a; border-radius: 8px; padding: 12px; display:flex; align-items:center; justify-content:space-between;">
                        <div style="display:flex; gap:12px; align-items:center;">
                            <div style="width:48px; height:48px; border-radius:6px; background:#1e1e1e; display:flex; align-items:center; justify-content:center; color:#ffd3a0; font-weight:bold;">M</div>
                            <div>
                                <div style="color:#ffffff; font-weight:700;">{{ $m->name }}</div>
                                <div style="color:#888; font-size:12px; margin-top:4px;">Deleted: {{ date('M d, Y H:i', strtotime($m->deleted_at)) }}</div>
                            </div>
                        </div>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <form method="POST" action="/superadmin/medicine/{{ $m->id }}/restore" style="display:inline">
                                @csrf
                                <button class="btn" style="background:#4cb970; color:white; border:none; padding:8px 12px; border-radius:6px; font-weight:600;">Restore</button>
                            </form>
                            <form method="POST" action="/superadmin/medicine/{{ $m->id }}/permanent-delete" style="display:inline">
                                @csrf
                                <button class="btn" style="background:#c94c4c; color:white; border:none; padding:8px 12px; border-radius:6px; font-weight:600;" onclick="return confirm('Permanently delete this medicine? This cannot be undone.');">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection