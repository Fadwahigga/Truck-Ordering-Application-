<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }
        h2 {
            margin-left: 20px;
            color: #666;
            display: flex;
            align-items: center;
        }
        .notification-badge {
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            margin-left: 10px;
            font-size: 12px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 15px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background-color: #45a049;
        }
        select {
            padding: 5px;
            font-size: 14px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .notification-dropdown {
            margin: 20px;
            padding: 5px;
            font-size: 14px;
            width: calc(80% - 40px);
            border-radius: 5px;
            border: 1px solid #ddd;
            display: block;
        }
        .notification-dropdown option.new-notification {
            background-color: #e7f3fe;
            color: #31708f;
        }
        .notification-dropdown option.most-recent {
            background-color: #f2dede;
            color: #a94442;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <h1>Orders</h1>

    <!-- Display Notifications as Dropdown -->
    <h2>Notifications <span id="notificationBadge" class="notification-badge">0</span></h2>
    <select class="notification-dropdown" id="notificationDropdown">
        <option value="">Select Order</option>
        @foreach($notifications as $index => $notification)
            <option value="{{ $notification->data['order_id'] }}" class="{{ $index === 0 ? 'most-recent' : ($notification->unread() ? 'new-notification' : '') }}">
                New Order with ID #{{ $notification->data['order_id'] }}
            </option>
        @endforeach
    </select>

    <!-- Orders Table -->
    <table id="ordersTable">
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Pickup Location</th>
            <th>Delivery Location</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        @foreach($orders as $order)
        <tr class="order-row" data-order-id="{{ $order->id }}">
            <td>{{ $order->id }}</td>
            <td>{{ $order->user->name }}</td>
            <td>{{ $order->pickup_location }}</td>
            <td>{{ $order->delivery_location }}</td>
            <td>{{ $order->status }}</td>
            <td>
                <form action="/admin/orders/{{ $order->id }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <select name="status">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in progress" {{ $order->status == 'in progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>
        // Calculate the number of unread notifications
        const notificationBadge = document.getElementById('notificationBadge');
        const unreadNotifications = document.querySelectorAll('.notification-dropdown option.new-notification');
        notificationBadge.textContent = unreadNotifications.length;

        document.getElementById('notificationDropdown').addEventListener('change', function() {
            var selectedOrderId = this.value;
            var rows = document.querySelectorAll('.order-row');

            rows.forEach(function(row) {
                if (selectedOrderId === "" || row.getAttribute('data-order-id') === selectedOrderId) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });

            if (selectedOrderId) {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.classList.contains('new-notification')) {
                    selectedOption.classList.remove('new-notification');
                    notificationBadge.textContent = parseInt(notificationBadge.textContent) - 1;
                    
                    // Mark notification as read in the backend
                    fetch(`/admin/notifications/${selectedOrderId}/mark-as-read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                }
            }
        });

        // Pusher setup
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true
        });

        const channel = pusher.subscribe('private-App.Models.User.{{ Auth::id() }}');

        channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function(data) {
            // Update the UI to show the new notification
            addNewNotification(data);
        });

        function addNewNotification(data) {
            const dropdown = document.getElementById('notificationDropdown');
            const option = document.createElement('option');
            option.value = data.order_id;
            option.textContent = `New Order with ID #${data.order_id}`;
            option.classList.add('new-notification', 'most-recent');
            
            // Move 'most-recent' class from the previous first option
            const previousFirstOption = dropdown.querySelector('option.most-recent');
            if (previousFirstOption) {
                previousFirstOption.classList.remove('most-recent');
            }

            dropdown.insertBefore(option, dropdown.firstChild);
            
            // Update the notification badge
            const badge = document.getElementById('notificationBadge');
            badge.textContent = parseInt(badge.textContent) + 1;
        }
    </script>
</body>
</html>