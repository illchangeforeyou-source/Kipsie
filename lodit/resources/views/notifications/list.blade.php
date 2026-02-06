@extends('layouts.app')

@section('title', 'Notifications')

@section('styles')
<style>
    /* Notification box - Consultation style */
    .notification-item {
        background-color: #3a3a3a;
        border-left: 4px solid #b0b0b0;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        display: block;
        box-sizing: border-box;
        transition: all 0.3s;
    }

    body.dark-mode .notification-item {
        background-color: #3a3a3a;
        border-left-color: #b0b0b0;
    }

    .notification-item.unread {
        border-left-color: #1e3a8a;
        background-color: #1a2a4a;
    }

    .notification-item.consultation {
        border-left-color: #16a34a;
        background-color: #1a3a2a;
    }

    .notification-row { display: flex; gap: 16px; align-items: flex-start; justify-content: space-between; }
    .notification-left { flex: 1; min-width: 0; }
    .notification-right {
        display: flex; flex-direction: column; gap: 8px; align-items: flex-end; justify-content: center;
    }

    @media (max-width: 760px) {
        .notification-row { flex-direction: column; }
        .notification-right { width: 100%; align-items: flex-start; }
    }
    
    .btn-clear {
        background: var(--primary);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
    }

    .btn-clear:hover {
        background: #1e40af;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    }

    .notification-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .notification-info {
        flex: 1;
    }

    .notification-title {
        font-weight: 700;
        color: #f0f0f0;
        margin: 0 0 6px 0;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    body.dark-mode .notification-title {
        color: #f0f0f0;
    }

    .notification-time {
        font-size: 12px;
        color: #999;
        margin: 0;
    }

    body.dark-mode .notification-time {
        color: #999;
    }

    .notification-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        text-transform: none;
        letter-spacing: 0.4px;
        min-width: 60px;
        text-align: center;
    }

    .badge-delivered {
        background: #dcfce7;
        color: #166534;
    }

    .badge-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-delivery {
        background: #dbeafe;
        color: #0c4a6e;
    }

    body.dark-mode .badge-delivered {
        background: rgba(16, 185, 129, 0.2);
        color: #86efac;
    }

    body.dark-mode .badge-cancelled {
        background: rgba(239, 68, 68, 0.2);
        color: #fca5a5;
    }

    body.dark-mode .badge-delivery {
        background: rgba(59, 130, 246, 0.2);
        color: #93c5fd;
    }

    .notification-message {
        color: #d0d0d0;
        margin: 12px 0;
        font-size: 14px;
        line-height: 1.5;
    }

    body.dark-mode .notification-message {
        color: #d0d0d0;
    }

    .notification-details {
        background: transparent;
        padding: 0;
        border-radius: 0;
        margin: 12px 0;
        border-left: none;
        font-size: 13px;
    }

    body.dark-mode .notification-details {
        background: #374151;
        border-left-color: #60a5fa;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        gap: 15px;
    }

    .detail-label {
        font-weight: 600;
        color: #4b5563;
        min-width: 100px;
    }

    body.dark-mode .detail-label {
        color: #d1d5db;
    }

    .detail-value {
        color: #6b7280;
        word-break: break-word;
        flex: 1;
        text-align: right;
    }

    body.dark-mode .detail-value {
        color: #e5e7eb;
    }

    .notification-actions {
        display: flex;
        gap: 8px;
        margin-top: 14px;
        justify-content: flex-end;
        align-items: center;
        flex-wrap: wrap-reverse;
    }

    .gap-2 {
        gap: 8px !important;
    }

    .btn-action {
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
        border: none;
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    .btn-view {
        background: #1e3a8a;
        color: #ffffff;
        border: none;
    }

    .btn-view:hover {
        background: #1e40af;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    }

    body.dark-mode .btn-view {
        background: #1e3a8a;
        color: #ffffff;
    }

    body.dark-mode .btn-view:hover {
        background: #1e40af;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    }

    .btn-read {
        background: #16a34a;
        color: #ffffff;
        border: none;
    }

    .btn-read:hover {
        background: #15803d;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
    }

    body.dark-mode .btn-read {
        background: #16a34a;
        color: #ffffff;
    }

    body.dark-mode .btn-read:hover {
        background: #15803d;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
    }

    .btn-delete {
        background: #dc2626;
        color: #ffffff;
        border: none;
    }

    .btn-delete:hover {
        background: #991b1b;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    body.dark-mode .btn-delete {
    body.dark-mode .btn-delete {
        background: #dc2626;
        color: #ffffff;
    }

    body.dark-mode .btn-delete:hover {
        background: #991b1b;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .notification-details-expanded {
        background: transparent;
        padding: 0;
        border-radius: 0;
        margin-top: 14px;
        border: none;
        display: none;
    }

    body.dark-mode .notification-details-expanded {
        background: #1f2937;
        border-color: #4b5563;
    }

    .notification-details-expanded.show {
        display: block;
    }

    .details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    @media (max-width: 600px) {
        .details-grid {
            grid-template-columns: 1fr;
        }
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .detail-item-label {
        font-weight: 600;
        color: #6b7280;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    body.dark-mode .detail-item-label {
        color: #9ca3af;
    }

    .detail-item-value {
        color: #1f2937;
        font-size: 14px;
        font-weight: 500;
    }

    body.dark-mode .detail-item-value {
        color: #f9fafb;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 56px;
        margin-bottom: 15px;
        opacity: 0.4;
    }

    .empty-state p {
        font-size: 16px;
        margin: 10px 0 0 0;
    }

    .filter-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
        border-bottom: 2px solid #e5e7eb;
    }

    body.dark-mode .filter-tabs {
        border-bottom-color: #4b5563;
    }

    .tab-btn {
        background: none;
        border: none;
        padding: 12px 20px;
        cursor: pointer;
        font-weight: 600;
        color: #6b7280;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
        margin-bottom: -2px;
    }

    .tab-btn.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }

    .tab-btn:hover {
        color: #1f2937;
    }

    body.dark-mode .tab-btn {
        color: #9ca3af;
    }

    body.dark-mode .tab-btn:hover {
        color: #e5e7eb;
    }
</style>
@endsection

@section('content')
    <div class="notifications-container">
        <!-- Header -->
        <div class="notifications-header">
            <h1><i class="bi bi-bell"></i> Notifications</h1>
            <button class="btn-clear" onclick="markAllAsRead()">Mark All as Read</button>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <button class="tab-btn active" onclick="filterNotifications('all')">All</button>
            <button class="tab-btn" onclick="filterNotifications('unread')">Unread</button>
            <button class="tab-btn" onclick="filterNotifications('order')">Orders</button>
        </div>

        <!-- Notifications List -->
        <div id="notificationsList">
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p>Loading notifications...</p>
            </div>
        </div>
    </div>

    <script>
        let allNotifications = [];
        let currentFilter = 'all';
        const userLevel = {{ session('level') ? session('level') : 0 }};

        document.addEventListener('DOMContentLoaded', () => {
            console.log('Notifications page loaded, user level:', userLevel);
            loadNotifications();
            setInterval(loadNotifications, 30000);
        });

        function filterNotifications(filter) {
            currentFilter = filter;
            console.log('Filter changed to:', filter);
            
            // Update active tab
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            renderNotifications();
        }

        function loadNotifications() {
            fetch('/notifications/list', { 
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(r => {
                    console.log('Notifications response status:', r.status);
                    if (!r.ok) {
                        throw new Error('HTTP ' + r.status);
                    }
                    return r.json();
                })
                .then(data => {
                    console.log('Notifications data:', data);
                    allNotifications = data.notifications || [];
                    renderNotifications();
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                    const container = document.getElementById('notificationsList');
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-exclamation-circle"></i>
                            <p>Error loading notifications</p>
                            <small>${error.message}</small>
                        </div>
                    `;
                });
        }

        function renderNotifications() {
            const container = document.getElementById('notificationsList');
            
            if (allNotifications.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No notifications yet</p>
                    </div>
                `;
                return;
            }

            let filtered = allNotifications.filter(notif => filterByUserLevel(notif));

            // Apply current filter
            if (currentFilter === 'unread') {
                filtered = filtered.filter(n => !n.read);
            } else if (currentFilter === 'order') {
                filtered = filtered.filter(n => n.type && n.type.toLowerCase().includes('order'));
            }

            if (filtered.length === 0) {
                const emptyMsg = currentFilter === 'unread' ? 'No unread notifications' : 
                                 currentFilter === 'order' ? 'No order notifications' :
                                 'No relevant notifications for your role';
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>${emptyMsg}</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = filtered.map(notif => {
                const typeInfo = getNotificationTypeInfo(notif);
                const timeAgo = getTimeAgo(new Date(notif.created_at));
                
                return `
                    <div class="notification-item ${notif.read ? '' : 'unread'} ${typeInfo.type}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h5 class="notification-title mb-1">
                                    <i class="bi ${typeInfo.icon}"></i> ${notif.title}
                                </h5>
                                <div class="notification-time">
                                    ${timeAgo}
                                </div>
                            </div>
                            <span class="notification-badge badge-${typeInfo.type}">${typeInfo.label}</span>
                        </div>

                        <div class="notification-message">
                            ${notif.message}
                        </div>

                        <div class="mt-3 d-flex justify-content-end gap-2">
                            ${!notif.read ? `<button class="btn-action btn-read" onclick="event.stopPropagation(); markAsRead(${notif.id})">
                                <i class="bi bi-check-circle"></i> Mark as Read
                            </button>` : ''}
                            <button class="btn-action btn-delete" onclick="event.stopPropagation(); deleteNotification(${notif.id})">
                                <i class="bi bi-trash2"></i> Delete
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function filterByUserLevel(notif) {
            // Level 2 = Super Admin, sees admin activity
            if (userLevel === 2) {
                return notif.type.includes('admin') || notif.type.includes('user') || notif.type.includes('system');
            }
            // Level 3 = Pharmacist, sees orders and stock alerts
            if (userLevel === 3) {
                return notif.type.includes('order') || notif.type.includes('stock') || notif.type.includes('inventory');
            }
            // Level 4 = Customer/Staff, sees delivery and order updates
            if (userLevel === 4) {
                return notif.type.includes('delivery') || notif.type.includes('order') || notif.type.includes('status') || notif.type.includes('arrival');
            }
            return true; // Show all by default
        }

        function getNotificationTypeInfo(notif) {
            const type = notif.type.toLowerCase();
            
            if (type.includes('order')) {
                return { icon: 'bi-box-seam', label: 'Order', class: '', type: 'order' };
            } else if (type.includes('delivery') || type.includes('status')) {
                return { icon: 'bi-truck', label: 'Delivery', class: 'success', type: 'delivery' };
            } else if (type.includes('arrival')) {
                return { icon: 'bi-check-circle', label: 'Arrival', class: 'success', type: 'arrival' };
            } else if (type.includes('stock') || type.includes('inventory')) {
                return { icon: 'bi-exclamation-triangle', label: 'Stock Alert', class: 'warning', type: 'stock' };
            } else if (type.includes('error') || type.includes('failed')) {
                return { icon: 'bi-x-circle', label: 'Error', class: 'error', type: 'error' };
            } else if (type.includes('admin') || type.includes('user')) {
                return { icon: 'bi-shield-check', label: 'Admin', class: '', type: 'admin' };
            } else if (type.includes('system')) {
                return { icon: 'bi-gear', label: 'System', class: '', type: 'system' };
            }
            return { icon: 'bi-bell', label: 'Notification', class: '', type: 'info' };
        }

        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            })
            .then(r => r.json())
            .then(() => loadNotifications())
            .catch(error => console.error('Error marking as read:', error));
        }

        function toggleDetails(notificationId) {
            const detailsElement = document.getElementById(`details-${notificationId}`);
            if (detailsElement) {
                detailsElement.classList.toggle('show');
            }
        }

        function markAllAsRead() {
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            })
            .then(r => r.json())
            .then(() => loadNotifications())
            .catch(error => console.error('Error marking all as read:', error));
        }

        function deleteNotification(notificationId) {
            if (confirm('Are you sure you want to delete this notification?')) {
                fetch(`/notifications/${notificationId}/delete`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(r => {
                    if (!r.ok) throw new Error('Delete failed');
                    return r.json();
                })
                .then(() => loadNotifications())
                .catch(error => {
                    console.error('Error deleting notification:', error);
                    alert('Failed to delete notification');
                });
            }
        }

        function getTimeAgo(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            if (seconds < 60) return 'just now';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return `${minutes}m ago`;
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return `${hours}h ago`;
            const days = Math.floor(hours / 24);
            return `${days}d ago`;
        }
    </script>
@endsection
