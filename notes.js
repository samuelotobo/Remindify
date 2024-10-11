// notes.js

document.addEventListener('DOMContentLoaded', () => {
    const notesForm = document.getElementById('notes-form');
    const weekStartInput = document.getElementById('week_start_date');
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    const weekNumberElement = document.getElementById('week-number');
    const dayDateElements = days.map(day => document.getElementById(`${day}-date`));
    const successMessage = document.getElementById('success-message');

    // Function to get Monday of the week for a given date
    const getMonday = (date) => {
        const d = new Date(date);
        const day = d.getDay();
        const diff = d.getDate() - day + (day === 0 ? -6 : 1); // adjust when day is Sunday
        return new Date(d.setDate(diff));
    };

    // Function to format date as YYYY-MM-DD
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = (`0${date.getMonth() + 1}`).slice(-2);
        const day = (`0${date.getDate()}`).slice(-2);
        return `${year}-${month}-${day}`;
    };

    // Function to add ordinal suffix to a number
    const addOrdinalSuffix = (n) => {
        const s = ["th", "st", "nd", "rd"],
            v = n % 100;
        return n + (s[(v - 20) % 10] || s[v] || s[0]);
    };

    // Function to calculate the week number of a given date
    const getWeekNumber = (date) => {
        const startOfYear = new Date(date.getFullYear(), 0, 1);
        const pastDaysOfYear = (date - startOfYear) / 86400000; // 86400000 ms = 1 day
        return Math.ceil((pastDaysOfYear + startOfYear.getDay() + 1) / 7);
    };

    // Function to load notes and display week info
    const loadNotes = (selectedDate) => {
        const monday = getMonday(selectedDate);
        const weekStart = formatDate(monday);
        weekStartInput.value = weekStart;

        // Display the week number in the <span> element
        const weekNumber = getWeekNumber(selectedDate);
        weekNumberElement.textContent = weekNumber;

        // Display dates next to each day with ordinal suffix
        days.forEach((day, index) => {
            const dayDate = new Date(monday);
            dayDate.setDate(monday.getDate() + index);
            const dayNumberWithSuffix = addOrdinalSuffix(dayDate.getDate());
            dayDateElements[index].textContent = dayNumberWithSuffix; // e.g., "14th"
        });

        // Fetch the notes for the selected week
        fetch('notes.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_notes&week_start=${weekStart}`,
        })
        .then(response => response.json())
        .then(data => {
            days.forEach(day => {
                document.getElementById(day).value = data ? data[day] : '';
            });
        })
        .catch(error => console.error('Error fetching notes:', error));
    };

    // Function to save notes
    const saveNotes = (e) => {
        e.preventDefault();
        const weekStart = weekStartInput.value;
        const payload = `action=save_notes&week_start=${weekStart}`;
        const notesData = days.map(day => `${day}=${encodeURIComponent(document.getElementById(day).value)}`).join('&');

        fetch('notes.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `${payload}&${notesData}`,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Show the success message
                successMessage.classList.remove('hidden');
                // Optionally, hide the message after a few seconds
                setTimeout(() => {
                    successMessage.classList.add('hidden');
                }, 3000);
            } else {
                // Handle failure (optional: display an error message)
                console.error('Failed to save notes.');
            }
        })
        .catch(error => console.error('Error saving notes:', error));
    };

    notesForm.addEventListener('submit', saveNotes);

    // Modify the existing calendar.js to trigger loadNotes when a date is clicked
    const calendarDays = document.querySelector('.calendar-days');
    const month_picker = document.querySelector('#month-picker');
    const year_element = document.querySelector('#year');
    const month_names = [
        'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',
        'September', 'October', 'November', 'December',
    ];

    calendarDays.addEventListener('click', (e) => {
        if (e.target.tagName.toLowerCase() === 'div' && e.target.textContent) {
            const clickedDay = parseInt(e.target.textContent);
            const currentMonth = month_names.indexOf(month_picker.textContent);
            const currentYear = parseInt(year_element.textContent);
            const clickedDate = new Date(currentYear, currentMonth, clickedDay);
            loadNotes(clickedDate);
        }
    });

    // Optionally, load notes for the current week on page load
    const today = new Date();
    loadNotes(today);
});
