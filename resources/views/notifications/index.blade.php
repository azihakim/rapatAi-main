@extends('layouts.app')

@section('title', 'All Notifications')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">All Notifications</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" id="mark-all-read-page">
                    <i class="fas fa-check-double"></i> Mark All as Read
                </button>
            </div>
            <div class="card-body">
                <div id="notifications-list">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p class="text-muted mb-0">Loading notifications...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    loadAllNotifications();
    
    // Mark all as read
    $('#mark-all-read-page').on('click', function() {
        markAllAsReadPage();
    });
    
    // Mark individual notification as read
    $(document).on('click', '.notification-card', function() {
        const notificationId = $(this).data('id');
        const isRead = $(this).data('read');
        
        if (!isRead) {
            markAsReadPage(notificationId);
        }
    });
});

function loadAllNotifications() {
    $.ajax({
        url: '{{ route("notifications.index") }}',
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status) {
                displayAllNotifications(response.data);
            }
        },
        error: function(xhr) {
            console.error('Error loading notifications:', xhr);
            $('#notifications-list').html(`
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <h5>Failed to load notifications</h5>
                    <p class="text-muted">Please try refreshing the page</p>
                </div>
            `);
        }
    });
}

function displayAllNotifications(notifications) {
    let html = '';
    
    if (notifications.length === 0) {
        html = `
            <div class="text-center py-5">
                <i class="fas fa-bell-slash text-muted fa-4x mb-3"></i>
                <h4 class="text-muted">No Notifications</h4>
                <p class="text-muted">You don't have any notifications yet.</p>
            </div>
        `;
    } else {
        notifications.forEach(function(notification) {
            const iconClass = getNotificationIcon(notification.type);
            const iconColor = getNotificationColor(notification.type);
            const readClass = notification.is_read ? 'border-left-secondary' : 'border-left-primary';
            const bgClass = notification.is_read ? '' : 'bg-light';
            const newBadge = notification.is_read ? '' : '<span class="badge badge-primary badge-sm">New</span>';
            
            html += `
                <div class="card mb-2 notification-card ${readClass} ${bgClass}" 
                     data-id="${notification.id}" 
                     data-read="${notification.is_read}"
                     style="cursor: pointer; border-left-width: 4px;">
                    <div class="card-body py-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="icon-circle ${iconColor}">
                                    <i class="${iconClass} text-white"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 font-weight-bold">
                                            ${notification.title}
                                            ${newBadge}
                                        </h6>
                                        <p class="mb-1 text-gray-800">${notification.message}</p>
                                        <small class="text-muted">
                                            <i class="far fa-clock"></i> ${notification.time_ago}
                                            ${notification.is_read ? ' • <i class="fas fa-eye"></i> Read' : ''}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#notifications-list').html(html);
}

function getNotificationIcon(type) {
    switch(type) {
        case 'meeting': return 'fas fa-calendar';
        case 'approval': return 'fas fa-check-circle';
        case 'reminder': return 'fas fa-clock';
        case 'logistics': return 'fas fa-truck';
        case 'system': return 'fas fa-cog';
        default: return 'fas fa-bell';
    }
}

function getNotificationColor(type) {
    switch(type) {
        case 'meeting': return 'bg-primary';
        case 'approval': return 'bg-success';
        case 'reminder': return 'bg-warning';
        case 'logistics': return 'bg-info';
        case 'system': return 'bg-secondary';
        default: return 'bg-primary';
    }
}

function markAsReadPage(notificationId) {
    $.ajax({
        url: `/notifications/${notificationId}/mark-as-read`,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status) {
                loadAllNotifications(); // Reload to update UI
                toastr.success('Notification marked as read');
            }
        },
        error: function(xhr) {
            console.error('Error marking notification as read:', xhr);
            toastr.error('Failed to mark notification as read');
        }
    });
}

function markAllAsReadPage() {
    Swal.fire({
        title: 'Mark All as Read?',
        text: 'This will mark all notifications as read.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, mark all as read',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("notifications.mark-all-as-read") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        loadAllNotifications(); // Reload to update UI
                        toastr.success('All notifications marked as read');
                    }
                },
                error: function(xhr) {
                    console.error('Error marking all notifications as read:', xhr);
                    toastr.error('Failed to mark all notifications as read');
                }
            });
        }
    });
}
</script>
@endpush
@endsection