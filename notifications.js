document.getElementById('reminder-form').addEventListener('submit', (event) => {
    event.preventDefault(); // Prevent the form from submitting the traditional way

    const description = document.getElementById('description').value;
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;
    const location = document.getElementById('location').value;

    // Show popup notification
    const popup = document.getElementById('popup');
    const popupMessage = document.getElementById('popup-message');
    popupMessage.textContent = `Reminder Added: ${description} on ${date} at ${time} in ${location}`;
    popup.classList.add('show');

    // Hide the popup after 6 seconds
    setTimeout(() => {
        popup.classList.remove('show');
    }, 60000);

    // Show desktop notification
    if (Notification.permission === 'granted') {
        new Notification('New Reminder', {
            body: `Description: ${description}\nDate: ${date}\nTime: ${time}\nLocation: ${location}`,
            icon: 'logo.png' // Replace with your icon URL
        });
    } else if (Notification.permission !== 'denied') {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                new Notification('New Reminder', {
                    body: `Description: ${description}\nDate: ${date}\nTime: ${time}\nLocation: ${location}`,
                    icon: 'logo.png' // Replace with your icon URL
                });
            }
        });
    }

    // Optionally, you might want to submit the form data to the server
    // fetch('add_reminder.php', {
    //     method: 'POST',
    //     headers: {
    //         'Content-Type': 'application/x-www-form-urlencoded'
    //     },
    //     body: new URLSearchParams(new FormData(document.getElementById('reminder-form')))
    // }).then(response => response.text())
    //   .then(result => console.log(result))
    //   .catch(error => console.error('Error:', error));
});
