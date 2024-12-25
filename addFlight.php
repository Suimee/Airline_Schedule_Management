<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "airline";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $departure_time = $_POST['Departure_time'];
    $arrival_time = $_POST['Arrival_time'];
    $route = $_POST['Route'];
    $airline_id = $_POST['Airline_id'];
    $aircraft_id = $_POST['Aircraft_id'];
    $status = $_POST['Status'];
    $fare = $_POST['Fare'];

    // Insert flight details
    $stmt = $conn->prepare("INSERT INTO flight (Departure_time, Arrival_time, Route, Airline_id, Aircraft_id, Status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $departure_time, $arrival_time, $route, $airline_id, $aircraft_id, $status);

    if ($stmt->execute()) {
        $flight_id = $stmt->insert_id; // Get the last inserted Flight ID

        // Insert flight ticket prices
        if (!empty($fare)) {
            $ticket_stmt = $conn->prepare("INSERT INTO flight_tickets (Flight_id, Class, Fare) VALUES (?, ?, ?)");
            $ticket_stmt->bind_param("isd", $flight_id, $class, $fare);

            if ($ticket_stmt->execute()) {
                echo "<script>
                alert('Flight and ticket details added successfully!');
                window.location.href = 'flightDetails.php';
            </script>";
            } else {
                echo "<script>
                alert('Error adding ticket price: " . $ticket_stmt->error . "');
                window.location.href = 'flightDetails.php';
            </script>";
            }

            $ticket_stmt->close();
        } else {
            echo "<script>
            alert('Please enter a ticket price.');
            window.location.href = 'flightDetails.php';
        </script>";
        }
    } else {
        echo "<script>
        alert('Error adding flight: " . $stmt->error . "');
        window.location.href = 'flightDetails.php';
    </script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>
        alert('Invalid request method.');
        window.location.href = 'flightDetails.php';
    </script>";
}
?>