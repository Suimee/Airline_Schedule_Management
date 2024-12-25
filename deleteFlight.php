<?php
// deleteFlight.php

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

// Check if Flight_id is set in the URL
if (isset($_GET['Flight_id'])) {
    $flightId = $_GET['Flight_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // First, delete related records from flight_tickets table
        $sql_flight_tickets = "DELETE FROM flight_tickets WHERE Flight_id = ?";
        if ($stmt = $conn->prepare($sql_flight_tickets)) {
            $stmt->bind_param("i", $flightId);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception("Failed to prepare flight_tickets delete statement");
        }

        // Delete related records from schedule table
        $sql_schedule = "DELETE FROM schedule WHERE Flight_id = ?";
        if ($stmt = $conn->prepare($sql_schedule)) {
            $stmt->bind_param("i", $flightId);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception("Failed to prepare schedule delete statement");
        }
        
        // Delete related records from flight_crew table
        $sql_flight_crew = "DELETE FROM flight_crew WHERE Flight_id = ?";
        if ($stmt = $conn->prepare($sql_flight_crew)) {
            $stmt->bind_param("i", $flightId);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception("Failed to prepare flight_crew delete statement");
        }
        
        // Delete related records from payment table that are linked to tickets of this flight
        $sql_payment = "DELETE FROM payment WHERE Ticket_id IN (SELECT Ticket_id FROM ticket WHERE Flight_id = ?)";
        if ($stmt = $conn->prepare($sql_payment)) {
            $stmt->bind_param("i", $flightId);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception("Failed to prepare payment delete statement");
        }
        
        // Delete related records from ticket table
        $sql_ticket = "DELETE FROM ticket WHERE Flight_id = ?";
        if ($stmt = $conn->prepare($sql_ticket)) {
            $stmt->bind_param("i", $flightId);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception("Failed to prepare ticket delete statement");
        }

        // Finally, delete the flight itself
        $sql_flight = "DELETE FROM flight WHERE Flight_id = ?";
        if ($stmt = $conn->prepare($sql_flight)) {
            $stmt->bind_param("i", $flightId);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new Exception("Failed to prepare flight delete statement");
        }
        
        // If everything is successful, commit the transaction
        $conn->commit();
        
        // Redirect back to flight details page with success message
        header("Location: flightDetails.php?success=1");
        $conn->close();
        exit();
    } catch (Exception $e) {
        // If there's an error, rollback the transaction
        $conn->rollback();
        $conn->close();
        
        // Redirect back to flight details page with error message
        header("Location: flightDetails.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // If Flight_id is not set, redirect back with an error message
    $conn->close();
    header("Location: flightDetails.php?error=" . urlencode("Flight ID is missing"));
    exit();
}
?>
