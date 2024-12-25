-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2024 at 08:17 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `airline`
--

-- --------------------------------------------------------

--
-- Table structure for table `aircraft`
--

CREATE TABLE `aircraft` (
  `Aircraft_id` int(11) NOT NULL,
  `Model` varchar(50) NOT NULL,
  `Capacity` int(11) DEFAULT NULL,
  `Airline_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aircraft`
--

INSERT INTO `aircraft` (`Aircraft_id`, `Model`, `Capacity`, `Airline_id`) VALUES
(1, 'Boeing 737', 200, 1),
(2, 'Airbus A320', 180, 2),
(3, 'Boeing 777', 300, 3);

-- --------------------------------------------------------

--
-- Table structure for table `airline`
--

CREATE TABLE `airline` (
  `Airline_id` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Established_year` year(4) DEFAULT NULL,
  `Location` varchar(100) DEFAULT NULL,
  `ICAD_Code` varchar(10) DEFAULT NULL,
  `IATA_Code` varchar(10) DEFAULT NULL,
  `Contact` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `airline`
--

INSERT INTO `airline` (`Airline_id`, `Name`, `Established_year`, `Location`, `ICAD_Code`, `IATA_Code`, `Contact`) VALUES
(1, 'SkyHigh Airlines', '1980', 'New York', 'SH001', 'SKH', '111-222-3333'),
(2, 'Oceanic Air', '1995', 'Los Angeles', 'OA123', 'OCA', '222-333-4444'),
(3, 'Blue Horizon', '2010', 'San Francisco', 'BH345', 'BLH', '333-444-5555');

-- --------------------------------------------------------

--
-- Table structure for table `crew`
--

CREATE TABLE `crew` (
  `Crew_id` int(11) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Role` enum('Co-pilot','Air Host','Maintenance') DEFAULT NULL,
  `Experience_years` int(11) DEFAULT NULL,
  `Contact` varchar(50) DEFAULT NULL,
  `Salary` decimal(10,2) DEFAULT NULL,
  `Skills` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crew`
--

INSERT INTO `crew` (`Crew_id`, `Name`, `Role`, `Experience_years`, `Contact`, `Salary`, `Skills`) VALUES
(1, 'John Doe', 'Co-pilot', 5, '555-666-7777', 60000.00, 'Pilot certification'),
(2, 'Jane Roe', 'Air Host', 3, '666-777-8888', 45000.00, 'Customer service, Multilingual'),
(3, 'Mark Smith', 'Maintenance', 8, '777-888-9999', 50000.00, 'Engine maintenance');

-- --------------------------------------------------------

--
-- Table structure for table `flight`
--

CREATE TABLE `flight` (
  `Flight_id` int(11) NOT NULL,
  `Departure_time` datetime NOT NULL,
  `Arrival_time` datetime NOT NULL,
  `Route` varchar(255) DEFAULT NULL,
  `Aircraft_id` int(11) DEFAULT NULL,
  `Airline_id` int(11) DEFAULT NULL,
  `Status` enum('Scheduled','Delayed','Cancelled') DEFAULT 'Scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flight`
--

INSERT INTO `flight` (`Flight_id`, `Departure_time`, `Arrival_time`, `Route`, `Aircraft_id`, `Airline_id`, `Status`) VALUES
(9, '2024-12-10 00:53:00', '2024-12-12 00:53:00', 'Bangladesh - Canada', 3, 1, 'Scheduled'),
(10, '2024-12-05 00:59:00', '2024-12-07 00:59:00', 'New York to Los Angeles', 2, 2, 'Scheduled'),
(11, '2024-12-07 01:09:00', '2024-12-08 01:09:00', 'San Francisco to Chicago', 1, 2, 'Scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `flight_crew`
--

CREATE TABLE `flight_crew` (
  `Flight_id` int(11) NOT NULL,
  `Crew_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flight_tickets`
--

CREATE TABLE `flight_tickets` (
  `Ticket_id` int(11) NOT NULL,
  `Flight_id` int(11) DEFAULT NULL,
  `Class` varchar(50) DEFAULT NULL,
  `Fare` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flight_tickets`
--

INSERT INTO `flight_tickets` (`Ticket_id`, `Flight_id`, `Class`, `Fare`) VALUES
(1, 9, NULL, 1000.00),
(2, 10, NULL, 500.00),
(3, 11, NULL, 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `passenger`
--

CREATE TABLE `passenger` (
  `Passenger_id` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Contact` varchar(15) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passenger`
--

INSERT INTO `passenger` (`Passenger_id`, `Name`, `Contact`, `Email`, `Address`) VALUES
(10, 'Alice Ahmed Johnson', '1234567890', 'alice@example.com', NULL),
(11, 'Roktim', '1234567890', 'roktim@gmail.com', NULL),
(12, 'Shafkat', '1234567890', 'shafkat@gmail.com', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `Payment_id` int(11) NOT NULL,
  `Ticket_id` int(11) DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `Payment_method` enum('Credit Card','Debit Card','Cash') DEFAULT NULL,
  `Payment_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pilot`
--

CREATE TABLE `pilot` (
  `Pilot_id` int(11) NOT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `Experience_years` int(11) DEFAULT NULL,
  `Salary` decimal(10,2) DEFAULT NULL,
  `Contact` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pilot`
--

INSERT INTO `pilot` (`Pilot_id`, `Name`, `Experience_years`, `Salary`, `Contact`) VALUES
(1, 'Captain Joe', 15, 120000.00, '555-111-2222'),
(2, 'Captain Sarah', 10, 100000.00, '555-333-4444'),
(3, 'Captain Mike', 20, 150000.00, '555-555-6666');

-- --------------------------------------------------------

--
-- Table structure for table `route`
--

CREATE TABLE `route` (
  `Route_id` int(11) NOT NULL,
  `Departure_city` varchar(100) DEFAULT NULL,
  `Arrival_city` varchar(100) DEFAULT NULL,
  `Duration` time DEFAULT NULL,
  `Airline_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `route`
--

INSERT INTO `route` (`Route_id`, `Departure_city`, `Arrival_city`, `Duration`, `Airline_id`) VALUES
(1, 'New York', 'Los Angeles', '06:00:00', 1),
(2, 'San Francisco', 'Chicago', '04:00:00', 2),
(3, 'Los Angeles', 'Seattle', '03:00:00', 3);

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `Schedule_id` int(11) NOT NULL,
  `Flight_id` int(11) DEFAULT NULL,
  `Season` enum('Summer','Winter','Spring','Fall') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `Ticket_id` int(11) NOT NULL,
  `Passenger_id` int(11) DEFAULT NULL,
  `Flight_id` int(11) DEFAULT NULL,
  `Class` enum('Economy','Business','First Class') DEFAULT NULL,
  `Fare` decimal(10,2) DEFAULT NULL,
  `Booking_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket`
--

INSERT INTO `ticket` (`Ticket_id`, `Passenger_id`, `Flight_id`, `Class`, `Fare`, `Booking_time`) VALUES
(11, 10, 9, 'Economy', 1000.00, '2024-12-02 01:01:19'),
(12, 11, 10, 'Economy', 500.00, '2024-12-02 01:06:01'),
(13, 12, 11, 'Economy', 500.00, '2024-12-02 01:10:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aircraft`
--
ALTER TABLE `aircraft`
  ADD PRIMARY KEY (`Aircraft_id`),
  ADD KEY `Airline_id` (`Airline_id`);

--
-- Indexes for table `airline`
--
ALTER TABLE `airline`
  ADD PRIMARY KEY (`Airline_id`),
  ADD UNIQUE KEY `ICAD_Code` (`ICAD_Code`),
  ADD UNIQUE KEY `IATA_Code` (`IATA_Code`);

--
-- Indexes for table `crew`
--
ALTER TABLE `crew`
  ADD PRIMARY KEY (`Crew_id`);

--
-- Indexes for table `flight`
--
ALTER TABLE `flight`
  ADD PRIMARY KEY (`Flight_id`),
  ADD KEY `Aircraft_id` (`Aircraft_id`),
  ADD KEY `Airline_id` (`Airline_id`);

--
-- Indexes for table `flight_crew`
--
ALTER TABLE `flight_crew`
  ADD PRIMARY KEY (`Flight_id`,`Crew_id`),
  ADD KEY `Crew_id` (`Crew_id`);

--
-- Indexes for table `flight_tickets`
--
ALTER TABLE `flight_tickets`
  ADD PRIMARY KEY (`Ticket_id`),
  ADD KEY `Flight_id` (`Flight_id`);

--
-- Indexes for table `passenger`
--
ALTER TABLE `passenger`
  ADD PRIMARY KEY (`Passenger_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`Payment_id`),
  ADD KEY `Ticket_id` (`Ticket_id`);

--
-- Indexes for table `pilot`
--
ALTER TABLE `pilot`
  ADD PRIMARY KEY (`Pilot_id`);

--
-- Indexes for table `route`
--
ALTER TABLE `route`
  ADD PRIMARY KEY (`Route_id`),
  ADD KEY `Airline_id` (`Airline_id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`Schedule_id`),
  ADD KEY `Flight_id` (`Flight_id`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`Ticket_id`),
  ADD KEY `Passenger_id` (`Passenger_id`),
  ADD KEY `Flight_id` (`Flight_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aircraft`
--
ALTER TABLE `aircraft`
  MODIFY `Aircraft_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `airline`
--
ALTER TABLE `airline`
  MODIFY `Airline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `crew`
--
ALTER TABLE `crew`
  MODIFY `Crew_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `flight`
--
ALTER TABLE `flight`
  MODIFY `Flight_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `flight_tickets`
--
ALTER TABLE `flight_tickets`
  MODIFY `Ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `passenger`
--
ALTER TABLE `passenger`
  MODIFY `Passenger_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `Payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pilot`
--
ALTER TABLE `pilot`
  MODIFY `Pilot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `route`
--
ALTER TABLE `route`
  MODIFY `Route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `Schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `Ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aircraft`
--
ALTER TABLE `aircraft`
  ADD CONSTRAINT `aircraft_ibfk_1` FOREIGN KEY (`Airline_id`) REFERENCES `airline` (`Airline_id`);

--
-- Constraints for table `flight`
--
ALTER TABLE `flight`
  ADD CONSTRAINT `flight_ibfk_1` FOREIGN KEY (`Aircraft_id`) REFERENCES `aircraft` (`Aircraft_id`),
  ADD CONSTRAINT `flight_ibfk_2` FOREIGN KEY (`Airline_id`) REFERENCES `airline` (`Airline_id`);

--
-- Constraints for table `flight_crew`
--
ALTER TABLE `flight_crew`
  ADD CONSTRAINT `flight_crew_ibfk_1` FOREIGN KEY (`Flight_id`) REFERENCES `flight` (`Flight_id`),
  ADD CONSTRAINT `flight_crew_ibfk_2` FOREIGN KEY (`Crew_id`) REFERENCES `crew` (`Crew_id`);

--
-- Constraints for table `flight_tickets`
--
ALTER TABLE `flight_tickets`
  ADD CONSTRAINT `flight_tickets_ibfk_1` FOREIGN KEY (`Flight_id`) REFERENCES `flight` (`Flight_id`);

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`Ticket_id`) REFERENCES `ticket` (`Ticket_id`);

--
-- Constraints for table `route`
--
ALTER TABLE `route`
  ADD CONSTRAINT `route_ibfk_1` FOREIGN KEY (`Airline_id`) REFERENCES `airline` (`Airline_id`);

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`Flight_id`) REFERENCES `flight` (`Flight_id`);

--
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`Passenger_id`) REFERENCES `passenger` (`Passenger_id`),
  ADD CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`Flight_id`) REFERENCES `flight` (`Flight_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
