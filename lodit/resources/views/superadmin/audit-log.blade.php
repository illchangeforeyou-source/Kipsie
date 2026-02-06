@php
    echo view('header');
@endphp

<div style="background: #1e1e1e; min-height: 100vh; padding: 30px 20px; color: #e0e0e0; margin-top: 80px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        <!-- Header -->
        <div style="margin-bottom: 30px;">
            <h2 style="color: #ffffff; margin-bottom: 10px;">Admin Activity Log</h2>
@extends('layouts.app')

@section('title', 'Admin Activity Log - LODIT')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
                <div>
                    <label style="display: block; color: #b0b0b0; font-size: 13px; margin-bottom: 5px;">Filter by Admin</label>
                    <select name="filter_admin" style="width: 100%; padding: 8px; background: #1e1e1e; border: 1px solid #3a3a3a; color: #e0e0e0; border-radius: 5px;">
                        <option value="">All Admins</option>
                        @foreach ($admins as $admin)
                            <option value="{{ $admin->id }}" @if ($filterAdmin == $admin->id) selected @endif>{{ $admin->username }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; color: #b0b0b0; font-size: 13px; margin-bottom: 5px;">Filter by Action</label>
                    <select name="filter_action" style="width: 100%; padding: 8px; background: #1e1e1e; border: 1px solid #3a3a3a; color: #e0e0e0; border-radius: 5px;">
                        <option value="">All Actions</option>
                        <option value="CREATE" @if ($filterAction == 'CREATE') selected @endif>Create User</option>
                        <option value="UPDATE" @if ($filterAction == 'UPDATE') selected @endif>Update User</option>
                        <option value="DELETE" @if ($filterAction == 'DELETE') selected @endif>Delete User</option>
                        <option value="PASSWORD_CHANGE" @if ($filterAction == 'PASSWORD_CHANGE') selected @endif>Password Change</option>
                    </select>
                </div>

                <div style="color: #b0b0b0; font-size: 13px; padding: 8px;">
                    Total: <strong style="color: #ffffff;">{{ $totalChanges }}</strong> changes
                </div>

                <button type="submit" style="background: #4c9cc9; color: white; border: none; padding: 8px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">
                    Filter
                </button>
            </form>
        </div>

        <!-- Log Entries -->
        @if ($changes->count() == 0)
            <div style="background: #2a2a2a; padding: 40px; border-radius: 8px; text-align: center; border: 1px solid #3a3a3a;">
                <p style="color: #b0b0b0; margin: 0;">No activity logs found.</p>
            </div>
        @else
            <div style="display: grid; gap: 15px;">
                @foreach ($changes as $change)
                    <div style="background: #2a2a2a; border: 1px solid #3a3a3a; border-radius: 8px; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
                        <!-- Status Badge -->
                        <div style="margin-right: 20px;">
                            <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold;
                                @if ($change->status === 'approved') 
                                    background: #2a5f2a; color: #a0ffa0;
                                @elseif ($change->status === 'rejected')
                                    background: #5f2a2a; color: #ffa0a0;
                                @else
                                    background: #5f5f2a; color: #ffff99;
                                @endif
                            ">
                                {{ strtoupper($change->status) }}
                            </span>
                        </div>

                        <!-- Action Type Badge -->
                        <div style="margin-right: 20px;">
                            <span style="display: inline-block; background: 
                                @if ($change->action_type === 'DELETE') #c94c4c
                                @elseif ($change->action_type === 'CREATE') #4cb970
                                @elseif ($change->action_type === 'UPDATE') #4c9cc9
                                @else #8b7d47
                                @endif; 
                                color: white; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold;">
                                {{ $change->action_type }}
                            </span>
                        </div>

                        <!-- Details -->
                        <div style="flex: 1;">
                            <p style="margin: 0; color: #ffffff; font-size: 14px;">
                                <strong>{{ $change->admin_username }}</strong> 
                                <span style="color: #b0b0b0;">
                                    @if ($change->action_type === 'DELETE') deleted @elseif ($change->action_type === 'CREATE') created @elseif ($change->action_type === 'UPDATE') updated @else changed password for @endif
                                    <strong>{{ $change->target_username }}</strong>
                                </span>
                            </p>
                            <p style="margin: 5px 0 0 0; color: #888; font-size: 12px;">
                                {{ $change->admin_email }} • {{ date('M d, Y H:i', strtotime($change->created_at)) }}
                                @if ($change->approved_at)
                                    • Reviewed: {{ date('M d, Y H:i', strtotime($change->approved_at)) }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($totalChanges > $perPage)
                <div style="margin-top: 30px; text-align: center;">
                    <div style="display: inline-flex; gap: 5px;">
                        @php
                            $totalPages = ceil($totalChanges / $perPage);
                        @endphp
                        @for ($p = 1; $p <= $totalPages; $p++)
                            <a href="/superadmin/audit-log?page={{ $p }}&filter_admin={{ $filterAdmin }}&filter_action={{ $filterAction }}" style="padding: 8px 12px; background: @if ($p == $currentPage) #4c9cc9 @else #2a2a2a @endif; color: @if ($p == $currentPage) white @else #b0b0b0 @endif; border: 1px solid #3a3a3a; border-radius: 5px; text-decoration: none;">
                                {{ $p }}
                            </a>
                        @endfor
                    </div>
                </div>
            @endif
        @endif
</div>
@endsection