<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Info</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table-container {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Infinity Airline - Passenger Info</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="adminDashboard.html">Back to Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Passenger Info Table -->
    <div class="container table-container">
        <h2 class="text-center mb-4">Passenger Details</h2>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Ticket ID(s)</th>
                    <th>Departure - Arrival</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="passengerTableBody">
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

                // Fetch passenger details with tickets and schedules
                $sql = "SELECT 
                            Passenger.Passenger_id, 
                            Passenger.Name, 
                            Passenger.Contact, 
                            Passenger.Email, 
                            GROUP_CONCAT(Ticket.Ticket_id SEPARATOR ', ') AS Tickets,
                            GROUP_CONCAT(CONCAT(Flight.Departure_time, ' to ', Flight.Arrival_time) SEPARATOR '<br>') AS Schedule
                        FROM Passenger
                        LEFT JOIN Ticket ON Passenger.Passenger_id = Ticket.Passenger_id
                        LEFT JOIN Flight ON Ticket.Flight_id = Flight.Flight_id
                        LEFT JOIN Schedule ON Flight.Flight_id = Schedule.Flight_id
                        GROUP BY Passenger.Passenger_id";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $index = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . $index++ . "</td>
                            <td>" . $row["Name"] . "</td>
                            <td>" . $row["Email"] . "</td>
                            <td>" . $row["Contact"] . "</td>
                            <td>" . ($row["Tickets"] ?: "No Tickets") . "</td>
                            <td>" . ($row["Schedule"] ?: "No Schedule") . "</td>
                            <td>
                                <button class='btn btn-warning btn-sm' onclick='openEditPassengerModal(" . $row["Passenger_id"] . ", `" . $row["Name"] . "`, `" . $row["Email"] . "`, `" . $row["Contact"] . "`)'>Edit Info</button>
                                <button class='btn btn-info btn-sm' onclick='openEditFlightModal(" . $row["Passenger_id"] . ", `" . $row["Tickets"] . "`)'>Edit Flight</button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No passengers found</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Passenger Modal -->
    <div class="modal fade" id="editPassengerModal" tabindex="-1" aria-labelledby="editPassengerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editPassengerForm" method="POST" action="updatePassenger.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPassengerModalLabel">Edit Passenger Info</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editPassengerId" name="Passenger_id">
                        <div class="mb-3">
                            <label for="editPassengerName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editPassengerName" name="Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPassengerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editPassengerEmail" name="Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPassengerContact" class="form-label">Contact</label>
                            <input type="text" class="form-control" id="editPassengerContact" name="Contact" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Flight Modal -->
    <div class="modal fade" id="editFlightModal" tabindex="-1" aria-labelledby="editFlightModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editFlightForm" method="POST" action="updateFlight.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFlightModalLabel">Edit Passenger's Flight</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editPassengerIdFlight" name="Passenger_id">
                        <div class="mb-3">
                            <label for="editTicketId" class="form-label">Ticket ID</label>
                            <input type="text" class="form-control" id="editTicketId" name="Ticket_id" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="editFlightId" class="form-label">New Flight ID</label>
                            <input type="text" class="form-control" id="editFlightId" name="Flight_id" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Flight</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openEditPassengerModal(passengerId, name, email, contact) {
            document.getElementById("editPassengerId").value = passengerId;
            document.getElementById("editPassengerName").value = name;
            document.getElementById("editPassengerEmail").value = email;
            document.getElementById("editPassengerContact").value = contact;

            const modal = new bootstrap.Modal(document.getElementById("editPassengerModal"));
            modal.show();
        }

        function openEditFlightModal(passengerId, ticketIds) {
            document.getElementById("editPassengerIdFlight").value = passengerId;
            document.getElementById("editTicketId").value = ticketIds.split(", ")[0]; // Use the first ticket by default

            const modal = new bootstrap.Modal(document.getElementById("editFlightModal"));
            modal.show();
        }
    </script>
</body>
</html>
