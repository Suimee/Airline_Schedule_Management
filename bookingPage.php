<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Page</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .table-container {
            margin-top: 30px;
        }

        .search-form {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/airline">Infinity Airline - Booking</a>
        </div>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="bookingPage.php">Booking</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Contact.html">Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="Experience.html">Experience</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Search Flights -->
    <div class="container search-form">
        <h2 class="text-center my-4">Search Flights</h2>
        <form method="GET" action="">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <input type="text" class="form-control" name="route" placeholder="From - To">
                </div>
                <div class="col-md-3 mb-3">
                    <input type="number" class="form-control" name="min_price" placeholder="Min Price">
                </div>
                <div class="col-md-3 mb-3">
                    <input type="number" class="form-control" name="max_price" placeholder="Max Price">
                </div>
                <div class="col-md-3 mb-3">
                    <input type="text" class="form-control" name="airline" placeholder="Airline Name">
                </div>
                <div class="col-md-3 mb-3">
                    <input type="text" class="form-control" name="aircraft" placeholder="Aircraft Model">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Available Flights -->
    <div class="container table-container">
        <h2 class="text-center mb-4">Available Flights</h2>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Route</th>
                    <th>Departure Time</th>
                    <th>Arrival Time</th>
                    <th>Airline</th>
                    <th>Aircraft</th>
                    <th>Status</th>
                    <th>Price (Economy)</th>
                    <th>Action</th>
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

                // Build search query
                $search_query = "SELECT 
                                    flight.Flight_id, 
                                    flight.Route, 
                                    flight.Departure_time, 
                                    flight.Arrival_time, 
                                    flight.Status, 
                                    airline.Name AS Airline_Name, 
                                    aircraft.Model AS Aircraft_Model, 
                                    flight_tickets.Fare 
                                FROM flight
                                LEFT JOIN airline ON flight.Airline_id = airline.Airline_id
                                LEFT JOIN aircraft ON flight.Aircraft_id = aircraft.Aircraft_id
                                LEFT JOIN flight_tickets ON flight.Flight_id = flight_tickets.Flight_id
                                WHERE flight.Status = 'Scheduled' 
                                  AND flight.Departure_time >= NOW()";

                // Add filters based on user input
                if (!empty($_GET['route'])) {
                    $route = $_GET['route'];
                    $search_query .= " AND flight.Route LIKE '%$route%'";
                }
                if (!empty($_GET['min_price'])) {
                    $min_price = $_GET['min_price'];
                    $search_query .= " AND flight_tickets.Fare >= $min_price";
                }
                if (!empty($_GET['max_price'])) {
                    $max_price = $_GET['max_price'];
                    $search_query .= " AND flight_tickets.Fare <= $max_price";
                }
                if (!empty($_GET['airline'])) {
                    $airline = $_GET['airline'];
                    $search_query .= " AND airline.Name LIKE '%$airline%'";
                }
                if (!empty($_GET['aircraft'])) {
                    $aircraft = $_GET['aircraft'];
                    $search_query .= " AND aircraft.Model LIKE '%$aircraft%'";
                }

                // Execute the query
                $result = $conn->query($search_query);

                if ($result->num_rows > 0) {
                    $index = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . $index++ . "</td>
                            <td>" . $row["Route"] . "</td>
                            <td>" . $row["Departure_time"] . "</td>
                            <td>" . $row["Arrival_time"] . "</td>
                            <td>" . $row["Airline_Name"] . "</td>
                            <td>" . $row["Aircraft_Model"] . "</td>
                            <td>" . $row["Status"] . "</td>
                            <td>" . ($row["Fare"] ?: "Not Set") . "</td>
                            <td>
                                <button class='btn btn-success btn-sm' onclick='openBookingModal(" . $row["Flight_id"] . ", `" . $row["Route"] . "`, `" . $row["Fare"] . "`)'>Book Now</button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>No flights available</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="bookingForm" method="POST" action="bookTicket.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bookingModalLabel">Book a Flight</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="flightId" name="Flight_id">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact</label>
                            <input type="text" class="form-control" id="contact" name="Contact" required>
                        </div>
                        <div class="mb-3">
                            <label for="route" class="form-label">Route</label>
                            <input type="text" class="form-control" id="route" name="Route" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="fare" class="form-label">Price</label>
                            <input type="text" class="form-control" id="fare" name="Fare" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="class" class="form-label">Class</label>
                            <select class="form-control" id="class" name="Class" required>
                                <option value="Economy">Economy</option>
                                <option value="Business">Business</option>
                                <option value="First Class">First Class</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Book Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openBookingModal(flightId, route, fare) {
            document.getElementById("flightId").value = flightId;
            document.getElementById("route").value = route;
            document.getElementById("fare").value = fare || "Not Set";

            const modal = new bootstrap.Modal(document.getElementById("bookingModal"));
            modal.show();
        }
    </script>
</body>

</html>