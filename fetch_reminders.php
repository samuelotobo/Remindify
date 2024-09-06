    <?php
    header('Content-Type: application/json');

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "reminder_app";

    // Create connection
    $link = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection

    if (!$link) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Query to fetch reminders
    $query = "SELECT id, description, reminder_date, reminder_time, location FROM reminders";
    $result = mysqli_query($link, $query);

    $reminders = array();

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $reminders[] = $row;
        }
    } else {
        echo json_encode(["message" => "No reminders found"]);
        exit;
    }

    mysqli_close($link);

    echo json_encode($reminders);
    ?>