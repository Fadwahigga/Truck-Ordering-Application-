// public/js/admin-notifications.js

document.addEventListener("DOMContentLoaded", function () {
    const pusher = new Pusher("413aa121bafc5c57ced0", {
        cluster: "ap2",
        encrypted: true,
    });

    const channel = pusher.subscribe("private-App.Models.User." + adminId);

    channel.bind(
        "Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        function (data) {
            // Update the UI to show the new notification
            updateNotificationUI(data);
            // Fetch all unread notifications
            fetchUnreadNotifications();
        }
    );

    function updateNotificationUI(data) {
        const notificationList = document.getElementById("notification-list");
        const newNotification = document.createElement("li");
        newNotification.textContent = `New order placed: Order ID ${data.order_id}, Status: ${data.status}`;
        notificationList.prepend(newNotification);
    }

    function fetchUnreadNotifications() {
        fetch("/admin/notifications")
            .then((response) => response.json())
            .then((notifications) => {
                // Update the notification count
                updateNotificationCount(notifications.length);
            });
    }

    function updateNotificationCount(count) {
        const notificationBadge = document.getElementById("notification-badge");
        notificationBadge.textContent = count;
        notificationBadge.style.display = count > 0 ? "inline" : "none";
    }
});
