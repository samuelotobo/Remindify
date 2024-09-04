const sideMenu =  document.querySelector("aside");
const menuBtn =  document.querySelector("#menu-btn");
const closeBtn = document.querySelector("#close-btn");
const themeToggler = document.querySelector(".theme-toggler");

// show side bar
menuBtn.addEventListener('click', () => {
    sideMenu.style.display = 'block';
})

// close sidebar
closeBtn.addEventListener('click', () => {
    sideMenu.style.display = 'none';
})

//  date
document.addEventListener('DOMContentLoaded', function() {
    const dateDisplay = document.getElementById('current-date');

    if (!dateDisplay) {
        console.error('Element with ID "current-date" not found.');
        return;
    }

    // Get today's date formatted as YYYY-MM-DD
    const today = new Date();
    const formattedToday = today.toISOString().split('T')[0];

    // Check if a date is already stored in local storage
    const savedDate = localStorage.getItem('selectedDate');

    if (savedDate === formattedToday) {
        // If the saved date is today, use it
        dateDisplay.textContent = formatDate(savedDate);
    } else {
        // If no saved date or the saved date is not today, set to today's date
        dateDisplay.textContent = formatDate(today);
        localStorage.setItem('selectedDate', formattedToday); // Update local storage to today's date
    }
});

// Function to format date as desired
function formatDate(dateInput) {
    const date = new Date(dateInput);
    const options = { year: 'numeric', month: 'long', day: 'numeric' }; // Example: January 1, 2023
    return date.toLocaleDateString(undefined, options);
}
// change theme
document.addEventListener('DOMContentLoaded', function() {
    const themeToggler = document.querySelector('.theme-toggler');
    
    // Check for saved theme in local storage
    const savedTheme = localStorage.getItem('theme');

    // Apply the saved theme if it exists
    if (savedTheme) {
        document.body.classList.add(savedTheme);
        if (savedTheme === 'dark-theme-variables') {
            // Make sure the correct icon is active
            themeToggler.querySelector('span:nth-child(1)').classList.remove('active');
            themeToggler.querySelector('span:nth-child(2)').classList.add('active');
        }
    }

    // Event listener to toggle theme
    themeToggler.addEventListener('click', () => {
        document.body.classList.toggle('dark-theme-variables');

        // Update icon active state
        themeToggler.querySelector('span:nth-child(1)').classList.toggle('active');
        themeToggler.querySelector('span:nth-child(2)').classList.toggle('active');

        // Save the theme to local storage
        if (document.body.classList.contains('dark-theme-variables')) {
            localStorage.setItem('theme', 'dark-theme-variables');
        } else {
            localStorage.setItem('theme', '');
        }
    });
});


// recycle bin js

// Restore and Delete Functionality
document.querySelector('.restore-btn').addEventListener('click', () => {
    alert('Selected items restored!');
});

document.querySelector('.delete-btn').addEventListener('click', () => {
    document.getElementById('confirmation-modal').style.display = 'block';
});

// Confirmation Modal Functionality
document.querySelector('.confirm-delete').addEventListener('click', () => {
    alert('Items permanently deleted!');
    document.getElementById('confirmation-modal').style.display = 'none';
});

document.querySelector('.cancel-delete').addEventListener('click', () => {
    document.getElementById('confirmation-modal').style.display = 'none';
});

document.querySelector('.close-modal').addEventListener('click', () => {
    document.getElementById('confirmation-modal').style.display = 'none';
});

// Auto Empty Recycle Bin Settings
document.getElementById('auto-empty-form').addEventListener('submit', (e) => {
    e.preventDefault();
    const period = document.getElementById('empty-period').value;
    alert(`Auto empty set to ${period} days!`);
});


// =========== balance ==============

document.getElementById('expense-form').addEventListener('submit', function(e) {
    e.preventDefault();

    let currentBalance = parseFloat(document.getElementById('current-balance').value);
    const expenseAmount = parseFloat(document.getElementById('expense-amount').value);
    const spendingLimit = parseFloat(document.getElementById('spending-limit').value);
    const feedbackSection = document.getElementById('feedback-section');
    const remainingBalanceElement = document.getElementById('remaining-balance');

    // Ensure current balance is set
    if (isNaN(currentBalance) || currentBalance <= 0) {
        feedbackSection.textContent = "Please enter a valid current balance.";
        feedbackSection.className = "warning";
        return;
    }

    // Update the balance
    currentBalance -= expenseAmount;
    remainingBalanceElement.textContent = currentBalance.toFixed(2);

    // Check if spending limit is exceeded
    if (spendingLimit && currentBalance < spendingLimit) {
        feedbackSection.textContent = "Oops! Looks like you've exceeded your spending limit. Be mindful of your expenses to stay on track.";
        feedbackSection.className = "warning";
    } else {
        feedbackSection.textContent = "Great job! You're staying within your spending limit. Keep it up!";
        feedbackSection.className = "success";
    }
});



document.addEventListener("DOMContentLoaded", function() {
    if (Notification.permission !== "granted") {
        Notification.requestPermission();
    }
});
document.addEventListener("DOMContentLoaded", function() {
    if (Notification.permission === "granted") {
        // Function to check reminders
        function checkReminders() {
            fetch('get_reminders.php')
                .then(response => response.json())
                .then(reminders => {
                    const now = new Date();
                    reminders.forEach(reminder => {
                        const reminderTime = new Date(reminder.date + ' ' + reminder.time);
                        if (reminderTime <= now) {
                            // Show notification
                            new Notification("Reminder Due", {
                                body: reminder.description,
                                icon: 'logo.png' // Optional: Add a path to an icon
                            });

                            // Optionally, remove the reminder from the database or update its status
                            fetch('remove_reminder.php', {
                                method: 'POST',
                                body: JSON.stringify({ id: reminder.id }),
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            });
                        }
                    });
                });
        }

        // Check reminders every minute
        setInterval(checkReminders, 2000);
    }
});

// shared calendar
document.getElementById('monthly-view').addEventListener('click', () => {
    // Switch to monthly view
});

document.getElementById('weekly-view').addEventListener('click', () => {
    // Switch to weekly view
});

document.getElementById('daily-view').addEventListener('click', () => {
    // Switch to daily view
});

document.getElementById('search').addEventListener('input', (event) => {
    // Filter events based on search input
});

document.getElementById('close-modal').addEventListener('click', () => {
    document.getElementById('event-details-modal').style.display = 'none';
});

document.getElementById('edit-event').addEventListener('click', () => {
    // Handle event editing
});

document.getElementById('delete-event').addEventListener('click', () => {
    // Handle event deletion
});

