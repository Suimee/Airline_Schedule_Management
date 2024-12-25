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

// Update passenger data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["Passenger_id"];  // Passenger ID
    $name = $_POST["Name"];      // Passenger Name
    $contact = $_POST["Contact"]; // Contact
    $email = $_POST["Email"];      // Email
    $address = $_POST["Address"];  // Address
    $ticket_id = $_POST["Ticket_id"]; // Ticket ID
    $flight_id = $_POST["Flight_id"]; // New Flight ID (if changing flight)

    // Update passenger info
    $updatePassengerSql = "UPDATE Passenger SET 
                                Name='$name', 
                                Contact='$contact', 
                                Email='$email', 
                                Address='$address'
                            WHERE Passenger_id=$id";

    if ($conn->query($updatePassengerSql) !== TRUE) {
        echo "Error updating passenger record: " . $conn->error;
        exit;
    }

    // Update ticket if Ticket_id and Flight_id are provided
    if (!empty($ticket_id) && !empty($flight_id)) {
        // Validate the new flight
        $validateFlightSql = "SELECT * FROM Flight WHERE Flight_id = $flight_id";
        $flightResult = $conn->query($validateFlightSql);

        if ($flightResult->num_rows > 0) {
            $updateTicketSql = "UPDATE Ticket SET Flight_id = $flight_id WHERE Ticket_id = $ticket_id";

            if ($conn->query($updateTicketSql) !== TRUE) {
                echo "Error updating ticket record: " . $conn->error;
                exit;
            }
        } else {
            echo "Invalid Flight ID provided.";
            exit;
        }
    }

    // Redirect back to the Passenger Info page
    header("Location: passengerInfo.php");
    exit;
}

$conn->close();
?>
