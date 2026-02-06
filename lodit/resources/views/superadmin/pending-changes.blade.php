@extends('layouts.app')

@section('title', 'Pending Admin Changes - LODIT')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h2 style="color: #ffffff; margin-bottom: 10px;">Pending Admin Changes</h2>
        <p style="color: #b0b0b0; margin: 0;">Review and approve/reject admin actions</p>
    </div>

    @if (session('success'))
        <div style="background: #2a5f2a; color: #a0ffa0; padding: 12px 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #4db34d;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="background: #5f2a2a; color: #ffa0a0; padding: 12px 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #b34d4d;">
            {{ session('error') }}
        </div>
    @endif

    @if ($pendingChanges->count() == 0)
        <div style="background: #2a2a2a; padding: 40px; border-radius: 8px; text-align: center; border: 1px solid #3a3a3a;">
            <p style="color: #b0b0b0; margin: 0;">No pending changes to review.</p>
        </div>
    @else
        <div style="display: grid; gap: 20px;">
            @foreach ($pendingChanges as $change)
                <div style="background: #2a2a2a; border: 1px solid #3a3a3a; border-radius: 8px; padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                        <div>
                            <span style="display: inline-block; background: @if ($change->action_type === 'DELETE') #c94c4c @elseif ($change->action_type === 'CREATE') #4cb970 @elseif ($change->action_type === 'UPDATE') #4c9cc9 @else #8b7d47 @endif; color: white; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                                {{ $change->action_type }}
                            </span>
                            <h4 style="color: #ffffff; margin: 10px 0 5px 0;">{{ $change->target_username }}</h4>
                            <p style="color: #b0b0b0; margin: 0; font-size: 13px;">ID: {{ $change->target_user_id }}</p>
                        </div>
                        <div style="text-align: right;">
                            <p style="color: #b0b0b0; margin: 0; font-size: 12px;">{{ date('M d, Y H:i', strtotime($change->created_at)) }}</p>
                            <p style="margin: 0; color: #b0b0b0; font-size: 13px;"><strong style="color: #ffffff;">Admin:</strong> {{ $change->admin_username }} ({{ $change->admin_email }})</p>
                        </div>
                    </div>

                    @if ($change->action_type === 'DELETE')
                        <div style="background: #1e1e1e; padding: 12px; border-radius: 5px; margin-bottom: 15px; border-left: 3px solid #c94c4c;">
                            <p style="color: #b0b0b0; margin: 0 0 8px 0; font-size: 12px;"><strong style="color: #ffffff;">Item removed:</strong></p>
                            @if ($change->old_data)
                                <ul style="margin: 0; padding-left: 20px; color: #ffa0a0;">
                                    @foreach ($change->old_data as $key => $value)
                                        @if ($key !== 'password' && !is_array($value) && !is_object($value))
                                            <li style="margin: 3px 0;">{{ ucfirst($key) }}: <span style="color: #e0e0e0;">{{ $value }}</span></li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @elseif ($change->action_type === 'CREATE')
                        <div style="background: #1e1e1e; padding: 12px; border-radius: 5px; margin-bottom: 15px; border-left: 3px solid #4cb970;">
                            <p style="color: #b0b0b0; margin: 0 0 8px 0; font-size: 12px;"><strong style="color: #ffffff;">New item created with:</strong></p>
                            @if ($change->new_data)
                                <ul style="margin: 0; padding-left: 20px; color: #a0ffa0;">
                                    @foreach ($change->new_data as $key => $value)
                                        @if ($key !== 'password' && !is_array($value) && !is_object($value))
                                            <li style="margin: 3px 0;">{{ ucfirst($key) }}: <span style="color: #e0e0e0;">{{ $value }}</span></li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @elseif ($change->action_type === 'UPDATE' || $change->action_type === 'PASSWORD_CHANGE')
                        <div style="background: #1e1e1e; padding: 12px; border-radius: 5px; margin-bottom: 15px; border-left: 3px solid #4c9cc9;">
                            <p style="color: #b0b0b0; margin: 0 0 8px 0; font-size: 12px;"><strong style="color: #ffffff;">Changes:</strong></p>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <p style="color: #ff9999; margin: 0 0 5px 0; font-size: 12px; font-weight: bold;">Old Value:</p>
                                    @if ($change->old_data)
                                        <ul style="margin: 0; padding-left: 15px; color: #ffa0a0;">
                                            @foreach ($change->old_data as $key => $value)
                                                @if ($key !== 'password' && !is_array($value) && !is_object($value))
                                                    <li style="margin: 3px 0; font-size: 12px;">{{ ucfirst($key) }}: {{ $value }}</li>
                                                @elseif ($key === 'password')
                                                    <li style="margin: 3px 0; font-size: 12px;">password: [hidden]</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                                <div>
                                    <p style="color: #99ff99; margin: 0 0 5px 0; font-size: 12px; font-weight: bold;">New Value:</p>
                                    @if ($change->new_data)
                                        <ul style="margin: 0; padding-left: 15px; color: #a0ffa0;">
                                            @foreach ($change->new_data as $key => $value)
                                                @if ($key !== 'password' && !is_array($value) && !is_object($value))
                                                    <li style="margin: 3px 0; font-size: 12px;">{{ ucfirst($key) }}: {{ $value }}</li>
                                                @elseif ($key === 'password')
                                                    <li style="margin: 3px 0; font-size: 12px;">password: [hidden]</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <form method="POST" action="/superadmin/pending-changes/{{ $change->id }}/approve" style="margin: 0;">
                            @csrf
                            <button type="submit" style="background: #4cb970; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; font-size: 13px; font-weight: bold;">
                                ✓ Approve
                            </button>
                        </form>
                        <form method="POST" action="/superadmin/pending-changes/{{ $change->id }}/reject" style="margin: 0;">
                            @csrf
                            <button type="submit" style="background: #c94c4c; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; font-size: 13px; font-weight: bold;">
                                ✗ Reject & Revert
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
