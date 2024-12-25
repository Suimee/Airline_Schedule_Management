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
    $flight_id = $_POST['Flight_id'];
    $departure_time = $_POST['Departure_time'];
    $arrival_time = $_POST['Arrival_time'];
    $route = $_POST['Route'];
    $airline_id = $_POST['Airline_id'];
    $aircraft_id = $_POST['Aircraft_id'];
    $status = $_POST['Status'];
    $fare = $_POST['Fare'];

    // Validate input (optional but recommended)
    if (empty($flight_id) || empty($departure_time) || empty($arrival_time) || empty($route) || empty($airline_id) || empty($aircraft_id) || empty($status) || empty($fare)) {
        echo "<script>
            alert('All fields are required.');
            window.location.href = 'flightDetails.php';
        </script>";
        exit;
    }

    // Update flight details
    $stmt = $conn->prepare("UPDATE flight SET Departure_time = ?, Arrival_time = ?, Route = ?, Airline_id = ?, Aircraft_id = ?, Status = ? WHERE Flight_id = ?");
    $stmt->bind_param("sssissi", $departure_time, $arrival_time, $route, $airline_id, $aircraft_id, $status, $flight_id);

    if ($stmt->execute()) {
        // Update ticket price
        $ticket_stmt = $conn->prepare("UPDATE ticket SET Fare = ? WHERE Flight_id = ?");
        $ticket_stmt->bind_param("di", $fare, $flight_id);

        if ($ticket_stmt->execute()) {
            echo "<script>
                alert('Flight updated successfully!');
                window.location.href = 'flightDetails.php';
            </script>";
        } else {
            echo "<script>
                alert('Error updating ticket price: " . $ticket_stmt->error . "');
                window.location.href = 'flightDetails.php';
            </script>";
        }

        $ticket_stmt->close();
    } else {
        echo "<script>
            alert('Error updating flight: " . $stmt->error . "');
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
