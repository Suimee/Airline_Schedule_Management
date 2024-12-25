<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Details</title>
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
            <a class="navbar-brand" href="#">Infinity Airline - Flight Details</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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

    <!-- Flight Info Table -->
    <div class="container table-container">
        <h2 class="text-center mb-4">Flight Details</h2>
        <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addFlightModal">Add Flight</button>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Departure Time</th>
                    <th>Arrival Time</th>
                    <th>Route</th>
                    <th>Airline</th>
                    <th>Aircraft</th>
                    <th>Status</th>
                    <th>Ticket Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="flightTableBody">
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

                // Fetch flight details
                $sql = "SELECT 
            flight.Flight_id, 
            flight.Departure_time, 
            flight.Arrival_time, 
            flight.Route, 
            flight.Status, 
            airline.Name AS Airline_Name, 
            aircraft.Model AS Aircraft_Model,
            COALESCE(flight_tickets.Fare, 'Not Set') AS Fare
        FROM flight
        LEFT JOIN airline ON flight.Airline_id = airline.Airline_id
        LEFT JOIN aircraft ON flight.Aircraft_id = aircraft.Aircraft_id
        LEFT JOIN flight_tickets ON flight.Flight_id = flight_tickets.Flight_id";  // Join with flight_tickets table


                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $index = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . $index++ . "</td>
                            <td>" . $row["Departure_time"] . "</td>
                            <td>" . $row["Arrival_time"] . "</td>
                            <td>" . $row["Route"] . "</td>
                            <td>" . $row["Airline_Name"] . "</td>
                            <td>" . $row["Aircraft_Model"] . "</td>
                            <td>" . $row["Status"] . "</td>
                            <td>" . $row["Fare"] . "</td>
                            <td>
                                <button class='btn btn-warning btn-sm' onclick='openEditFlightModal(" . $row["Flight_id"] . ")'>Edit</button>
                                <button class='btn btn-danger btn-sm' onclick='deleteFlight(" . $row["Flight_id"] . ")'>Delete</button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>No flights found</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add Flight Modal -->
    <div class="modal fade" id="addFlightModal" tabindex="-1" aria-labelledby="addFlightModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addFlightForm" method="POST" action="addFlight.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFlightModalLabel">Add Flight</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="departureTime" class="form-label">Departure Time</label>
                            <input type="datetime-local" class="form-control" id="departureTime" name="Departure_time"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="arrivalTime" class="form-label">Arrival Time</label>
                            <input type="datetime-local" class="form-control" id="arrivalTime" name="Arrival_time"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="route" class="form-label">Route</label>
                            <input type="text" class="form-control" id="route" name="Route" required>
                        </div>

                        <!-- Airline Select Dropdown -->
                        <div class="mb-3">
                            <label for="airlineId" class="form-label">Airline</label>
                            <select class="form-control" id="airlineId" name="Airline_id" required>
                                <option value="" disabled selected>Select Airline</option>
                                <?php
                                // Fetch airlines from the database
                                $conn = new mysqli($servername, $username, $password, $dbname);
                                $airline_sql = "SELECT Airline_id, Name FROM airline";
                                $airline_result = $conn->query($airline_sql);
                                if ($airline_result->num_rows > 0) {
                                    while ($row = $airline_result->fetch_assoc()) {
                                        echo "<option value='" . $row["Airline_id"] . "'>" . $row["Name"] . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>No airlines available</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Aircraft Select Dropdown -->
                        <div class="mb-3">
                            <label for="aircraftId" class="form-label">Aircraft</label>
                            <select class="form-control" id="aircraftId" name="Aircraft_id" required>
                                <option value="" disabled selected>Select Aircraft</option>
                                <?php
                                // Fetch aircraft models from the database
                                $aircraft_sql = "SELECT Aircraft_id, Model FROM aircraft";
                                $aircraft_result = $conn->query($aircraft_sql);
                                if ($aircraft_result->num_rows > 0) {
                                    while ($row = $aircraft_result->fetch_assoc()) {
                                        echo "<option value='" . $row["Aircraft_id"] . "'>" . $row["Model"] . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>No aircrafts available</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control" id="status" name="Status" required>
                                <option value="Scheduled">Scheduled</option>
                                <option value="Delayed">Delayed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="fare" class="form-label">Ticket Price</label>
                            <input type="number" class="form-control" id="fare" name="Fare" step="0.01" min="0"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Flight</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Flight Modal -->
    <div class="modal fade" id="editFlightModal" tabindex="-1" aria-labelledby="editFlightModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editFlightForm" method="POST" action="updateFlight.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFlightModalLabel">Edit Flight</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editFlightId" name="Flight_id">
                        <div class="mb-3">
                            <label for="editDepartureTime" class="form-label">Departure Time</label>
                            <input type="datetime-local" class="form-control" id="editDepartureTime"
                                name="Departure_time" required>
                        </div>
                        <div class="mb-3">
                            <label for="editArrivalTime" class="form-label">Arrival Time</label>
                            <input type="datetime-local" class="form-control" id="editArrivalTime" name="Arrival_time"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="editRoute" class="form-label">Route</label>
                            <input type="text" class="form-control" id="editRoute" name="Route" required>
                        </div>
                        <div class="mb-3">
                            <label for="editAirlineId" class="form-label">Airline</label>
                            <select class="form-control" id="editAirlineId" name="Airline_id" required>
                                <!-- Fetch and populate airlines dynamically -->
                                <?php
                                // Fetch airline options from the database
                                $conn = new mysqli($servername, $username, $password, $dbname);
                                $airlineQuery = "SELECT Airline_id, Name FROM airline";
                                $airlineResult = $conn->query($airlineQuery);
                                while ($airline = $airlineResult->fetch_assoc()) {
                                    echo "<option value='{$airline['Airline_id']}'>{$airline['Name']}</option>";
                                }
                                $conn->close();
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editAircraftId" class="form-label">Aircraft</label>
                            <select class="form-control" id="editAircraftId" name="Aircraft_id" required>
                                <!-- Fetch and populate aircraft options dynamically -->
                                <?php
                                // Fetch aircraft options from the database
                                $conn = new mysqli($servername, $username, $password, $dbname);
                                $aircraftQuery = "SELECT Aircraft_id, Model FROM aircraft";
                                $aircraftResult = $conn->query($aircraftQuery);
                                while ($aircraft = $aircraftResult->fetch_assoc()) {
                                    echo "<option value='{$aircraft['Aircraft_id']}'>{$aircraft['Model']}</option>";
                                }
                                $conn->close();
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-control" id="editStatus" name="Status" required>
                                <option value="Scheduled">Scheduled</option>
                                <option value="Delayed">Delayed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editFare" class="form-label">Ticket Price</label>
                            <input type="number" class="form-control" id="editFare" name="Fare" step="0.01" min="0"
                                required>
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


    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openEditFlightModal(flightId) {
            const row = document.querySelector(`button[onclick='openEditFlightModal(${flightId})']`).parentNode.parentNode;
            const departureTime = row.children[1].innerText;
            const arrivalTime = row.children[2].innerText;
            const route = row.children[3].innerText;
            const airline = row.children[4].innerText;
            const aircraft = row.children[5].innerText;
            const status = row.children[6].innerText;
            const fare = row.children[7].innerText;

            // Set the form values in the modal
            document.getElementById("editFlightId").value = flightId;
            document.getElementById("editDepartureTime").value = departureTime;
            document.getElementById("editArrivalTime").value = arrivalTime;
            document.getElementById("editRoute").value = route;
            document.getElementById("editStatus").value = status;
            document.getElementById("editFare").value = fare;

            // Set the airline and aircraft options in the dropdown
            document.getElementById("editAirlineId").value = airline; // This can be modified further if you want to map it
            document.getElementById("editAircraftId").value = aircraft; // Same for aircraft

            const modal = new bootstrap.Modal(document.getElementById("editFlightModal"));
            modal.show();
        }


        function deleteFlight(flightId) {
            if (confirm("Are you sure you want to delete this flight?")) {
                window.location.href = `deleteFlight.php?Flight_id=${flightId}`;
            }
        }
    </script>
</body>

</html>