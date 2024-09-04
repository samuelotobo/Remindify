$(document).ready(function() {
    console.log('Document loaded');

    function fetchActivities() {
        $.ajax({
            url: 'fetch_reminders.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log('Fetched data:', data);
                const tableBody = $('#activity-table tbody');
                tableBody.empty(); // Clear existing rows

                data.forEach(function(activity) {
                    const row = `
                        <tr>
                            <td>${activity.description}</td>
                            <td>${activity.date} ${activity.time}</td>
                            <td>${activity.location}</td>
                            <td class="${getStatusClass(activity.status)}">${activity.status}</td>
                            <td class="primary">Details</td>
                        </tr>
                    `;
                    tableBody.append(row);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching activities:', error);
            }
        });
    }

    function getStatusClass(status) {
        switch(status) {
            case 'Completed':
                return 'success';
            case 'Pending':
                return 'warning';
            default:
                return '';
        }
    }

    // Fetch activities when page loads
    fetchActivities();

    // Handle "Show All" link click
    $('#show-all-link').on('click', function(event) {
        event.preventDefault(); // Prevent default link behavior
        console.log('Show All clicked');
        fetchActivities(); // Fetch and display all reminders
    });
});


