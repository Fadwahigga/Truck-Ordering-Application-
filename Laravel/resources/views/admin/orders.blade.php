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
.hidden {
    display: none;
}
#emailModal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    width: 400px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
.close-button {
    float: right;
    font-size: 20px;
    cursor: pointer;
}
#emailForm {
    display: flex;
    flex-direction: column;
}
#emailForm input,
#emailForm textarea {
    margin-bottom: 10px;
    padding: 5px;
}
#emailForm button {
    align-self: flex-start;
}
</style>
</head>
<body>
<h1>Orders</h1>
<h2>Notifications <span id="notificationBadge" class="notification-badge">0</span></h2>
<select class="notification-dropdown" id="notificationDropdown">
    <option value="">Select Order</option>
    @foreach($notifications->sortByDesc(function ($notification) {
        return $notification->unread() ? 1 : 0;
    }) as $index => $notification)
    <option value="{{ $notification->data['order_id'] }}" class="{{ $notification->unread() ? 'new-notification' : '' }} {{ $index === 0 ? 'most-recent' : '' }}">
        New Order with ID #{{ $notification->data['order_id'] }}
    </option>
    @endforeach
</select>
<table id="ordersTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Pickup Location</th>
            <th>Delivery Location</th>
            <th>Status</th>
            <th>Action</th>
            <th>Send Email</th> 
        </tr>
    </thead>
    <tbody>
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
            <td>
                <button type="button" class="email-button" data-user-id="{{ $order->user_id }}" data-user-email="{{ $order->user->email }}">Send Email</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div id="emailModal" class="hidden">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Send Email to User</h2>
        <form id="emailForm">
            <input type="hidden" id="userId" name="user_id">
            <label for="email">To:</label>
            <input type="email" id="email" name="email" readonly>
            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>
            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="5" required></textarea>
            <button type="submit">Send Email</button>
        </form>
    </div>
</div>

<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationDropdown = document.getElementById('notificationDropdown');
    let unreadNotifications = document.querySelectorAll('.notification-dropdown option.new-notification');
    notificationBadge.textContent = unreadNotifications.length;
    notificationDropdown.addEventListener('change', function() {
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
    const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
        encrypted: true
    });
    const channel = pusher.subscribe('private-App.Models.User.{{ Auth::id() }}');

    channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', function(data) {
        addNewNotification(data);
    });
    function addNewNotification(data) {
        const option = document.createElement('option');
        option.value = data.order_id;
        option.textContent = `New Order with ID #${data.order_id}`;
        option.classList.add('new-notification', 'most-recent');
        const previousFirstOption = notificationDropdown.querySelector('option.most-recent');
        if (previousFirstOption) {
            previousFirstOption.classList.remove('most-recent');
        }
        notificationDropdown.insertBefore(option, notificationDropdown.firstChild);
        notificationBadge.textContent = parseInt(notificationBadge.textContent) + 1;
        addNewOrderRow(data);
    }

    const emailModal = document.getElementById('emailModal');
    const closeButton = emailModal.querySelector('.close-button');
    const emailButtons = document.querySelectorAll('.email-button');
    const emailForm = document.getElementById('emailForm');

    function openEmailModal(userId, userEmail) {
        document.getElementById('userId').value = userId;
        document.getElementById('email').value = userEmail;
        emailModal.style.display = 'flex';
    }

    function closeEmailModal() {
        emailModal.style.display = 'none';
        emailForm.reset();
    }

    emailButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userEmail = this.getAttribute('data-user-email');
            openEmailModal(userId, userEmail);
        });
    });

    closeButton.addEventListener('click', closeEmailModal);

    emailForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const userId = document.getElementById('userId').value;
        const email = document.getElementById('email').value;
        const subject = document.getElementById('subject').value;
        const message = document.getElementById('message').value;

        fetch('/admin/send-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ user_id: userId, email: email, subject: subject, message: message })
        }).then(response => {
            if (response.ok) {
                alert('Email sent successfully');
                closeEmailModal();
            } else {
                alert('Failed to send email');
            }
        });
    });
    window.addEventListener('click', function(event) {
        if (event.target === emailModal) {
            closeEmailModal();
        }
    });
    function addNewOrderRow(data) {
        const ordersTable = document.getElementById('ordersTable');
        const newRow = document.createElement('tr');
        newRow.classList.add('order-row');
        newRow.setAttribute('data-order-id', data.order_id);

        newRow.innerHTML = `
            <td>${data.order_id}</td>
            <td>${data.user_name}</td>
            <td>${data.pickup_location}</td>
            <td>${data.delivery_location}</td>
            <td>pending</td>
            <td>
                <form action="/admin/orders/${data.order_id}" method="POST">
                    @csrf
                    @method('PATCH')
                    <select name="status">
                        <option value="pending" selected>Pending</option>
                        <option value="in progress">In Progress</option>
                        <option value="delivered">Delivered</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
            <td>
                <button type="button" class="email-button" data-user-id="${data.user_id}" data-user-email="${data.user_email}">Send Email</button>
            </td>
        `;
        ordersTable.querySelector('tbody').insertBefore(newRow, ordersTable.querySelector('tbody').firstChild);
        const newEmailButton = newRow.querySelector('.email-button');
        newEmailButton.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userEmail = this.getAttribute('data-user-email');
            openEmailModal(userId, userEmail);
        });
    }
});
</script>
</body>
</html>