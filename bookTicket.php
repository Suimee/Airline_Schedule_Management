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
    $flight_id = $_POST['Flight_id'];
    $name = $_POST['Name'];
    $email = $_POST['Email'];
    $contact = $_POST['Contact'];
    $fare = $_POST['Fare'];
    $class = $_POST['Class'];

    // Insert passenger record
    $stmt = $conn->prepare("INSERT INTO passenger (Name, Contact, Email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $contact, $email);

    if ($stmt->execute()) {
        $passenger_id = $stmt->insert_id; // Get the last inserted passenger ID

        // Insert ticket record
        $ticket_stmt = $conn->prepare("INSERT INTO ticket (Passenger_id, Flight_id, Class, Fare, Booking_time) VALUES (?, ?, ?, ?, NOW())");
        $ticket_stmt->bind_param("iisd", $passenger_id, $flight_id, $class, $fare);

        if ($ticket_stmt->execute()) {
            echo "<script>
                alert('Ticket booked successfully!');
                window.location.href = 'bookingPage.php';
            </script>";
        } else {
            echo "<script>
                alert('Error booking ticket: " . $ticket_stmt->error . "');
                window.location.href = 'bookingPage.php';
            </script>";
        }

        $ticket_stmt->close();
    } else {
        echo "<script>
            alert('Error adding passenger: " . $stmt->error . "');
            window.location.href = 'bookingPage.php';
        </script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>
        alert('Invalid request method.');
        window.location.href = 'bookingPage.php';
    </script>";
}

