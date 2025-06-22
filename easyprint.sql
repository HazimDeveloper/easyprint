-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2025 at 02:03 PM
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
-- Database: `easyprint`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `custID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `studentID` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `verificationStatus` varchar(100) NOT NULL,
  `membershipPoints` int(11) NOT NULL,
  `easyPayBalance` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`custID`, `userID`, `studentID`, `name`, `verificationStatus`, `membershipPoints`, `easyPayBalance`) VALUES
(1, 4, 'STU5067', 'test2', 'Pending', 2250, 0.00),
(2, 1, '', 'test1', 'Pending', 0, 0.00),
(3, 5, 'STU7507', 'test3', 'Pending', 0, 0.00),
(4, 7, 'STU7319', 'student', 'Pending', 0, 123.00);

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoiceID` int(11) NOT NULL,
  `orderID` int(11) NOT NULL,
  `invoiceDate` date NOT NULL,
  `totalInvoice` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `membershipcard`
--

CREATE TABLE `membershipcard` (
  `cardID` int(11) NOT NULL,
  `custID` int(11) NOT NULL,
  `cardBalance` decimal(10,2) NOT NULL,
  `balanceDate` date NOT NULL,
  `applyDate` date NOT NULL,
  `totalPoint` int(11) NOT NULL,
  `status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `orderID` int(11) NOT NULL,
  `staffID` int(11) NOT NULL,
  `custID` int(11) NOT NULL,
  `orderDate` date NOT NULL,
  `orderType` varchar(100) NOT NULL,
  `orderQuantity` int(11) NOT NULL,
  `pickupDate` date NOT NULL,
  `pickupTime` time NOT NULL,
  `uploadFileName` varchar(100) NOT NULL,
  `totalAmount` decimal(10,2) NOT NULL,
  `paymentID` int(11) NOT NULL,
  `orderStatus` varchar(50) NOT NULL,
  `packageID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orderpackage`
--

CREATE TABLE `orderpackage` (
  `orderPackageID` int(11) NOT NULL,
  `orderID` int(11) NOT NULL,
  `packageID` int(11) NOT NULL,
  `orderPackageQuantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderpackage`
--

INSERT INTO `orderpackage` (`orderPackageID`, `orderID`, `packageID`, `orderPackageQuantity`) VALUES
(1, 14, 3, 8),
(2, 0, 6, 8),
(3, 25, 5, 0);

-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE `package` (
  `packageID` int(11) NOT NULL,
  `packageName` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `colorOption` varchar(100) NOT NULL,
  `availabilityStatus` varchar(50) NOT NULL,
  `availabilityDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package`
--

INSERT INTO `package` (`packageID`, `packageName`, `price`, `colorOption`, `availabilityStatus`, `availabilityDate`) VALUES
(1, 'Size A4 (one-sided)', 0.20, 'Black & White', 'Available', '2025-06-15'),
(2, 'Size A4 (two-sided)', 0.40, 'Black & White', 'Available', '2025-06-15'),
(3, 'Size A4 (one-sided)', 0.50, 'Color', 'Available', '2025-06-16'),
(4, 'Size A4 (two-sided)', 1.00, 'Color', 'Available', '2025-06-16'),
(5, '-SPECIAL- Size A3', 2.00, 'Black & White', 'Available', '2025-06-16'),
(6, '-SPECIAL- Size A3', 5.00, 'Color', 'Available', '2025-06-16');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `paymentID` int(11) NOT NULL,
  `orderID` int(11) NOT NULL,
  `custID` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paymentDate` date NOT NULL,
  `paymentMethod` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`paymentID`, `orderID`, `custID`, `amount`, `paymentDate`, `paymentMethod`) VALUES
(1, 10, 1, 0.00, '2025-06-16', 'online_banking'),
(2, 22, 1, 450.00, '2025-06-16', 'card');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staffID` int(11) NOT NULL,
  `userID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `statushistory`
--

CREATE TABLE `statushistory` (
  `historyID` int(11) NOT NULL,
  `orderID` int(11) NOT NULL,
  `orderStatus` varchar(50) NOT NULL,
  `statusDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `statushistory`
--

INSERT INTO `statushistory` (`historyID`, `orderID`, `orderStatus`, `statusDate`) VALUES
(1, 10, 'Paid', '2025-06-16'),
(2, 22, 'Paid', '2025-06-16'),
(3, 0, 'Paid', '2025-06-16');

-- --------------------------------------------------------

--
-- Table structure for table `topup`
--

CREATE TABLE `topup` (
  `topupID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(50) NOT NULL,
  `topupDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` int(11) NOT NULL,
  `password` varchar(32) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contactNum` varchar(20) NOT NULL,
  `studentID` varchar(10) NOT NULL,
  `role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `password`, `username`, `email`, `contactNum`, `studentID`, `role`) VALUES
(1, '202cb962ac59075b964b07152d234b70', 'test1', 'test1@gmail.com', '987654321', '', 'Student'),
(2, '202cb962ac59075b964b07152d234b70', 'staff1', 'staff1@gmail.com', '987654321', '', 'Staff'),
(4, '202cb962ac59075b964b07152d234b70', 'test2', 'test2@gmail.com', '123456789', '', 'Student'),
(5, '202cb962ac59075b964b07152d234b70', 'test3', 'test3@gmail.com', '1111111111', '', 'Student'),
(6, '202cb962ac59075b964b07152d234b70', 'staff', 'staff@gmail.com', '0139604899', '', 'Staff'),
(7, '202cb962ac59075b964b07152d234b70', 'student', 'student@gmail.com', '0139704556', '', 'Student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`custID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoiceID`),
  ADD UNIQUE KEY `orderID` (`orderID`);

--
-- Indexes for table `membershipcard`
--
ALTER TABLE `membershipcard`
  ADD PRIMARY KEY (`cardID`),
  ADD UNIQUE KEY `custID` (`custID`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`orderID`),
  ADD UNIQUE KEY `staffID` (`staffID`,`custID`),
  ADD UNIQUE KEY `paymentID` (`paymentID`),
  ADD UNIQUE KEY `packageID` (`packageID`);

--
-- Indexes for table `orderpackage`
--
ALTER TABLE `orderpackage`
  ADD PRIMARY KEY (`orderPackageID`),
  ADD UNIQUE KEY `orderID` (`orderID`,`packageID`);

--
-- Indexes for table `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`packageID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`paymentID`),
  ADD UNIQUE KEY `orderID` (`orderID`,`custID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staffID`),
  ADD UNIQUE KEY `userID` (`userID`);

--
-- Indexes for table `statushistory`
--
ALTER TABLE `statushistory`
  ADD PRIMARY KEY (`historyID`),
  ADD UNIQUE KEY `orderID` (`orderID`);

--
-- Indexes for table `topup`
--
ALTER TABLE `topup`
  ADD PRIMARY KEY (`topupID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `custID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoiceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `membershipcard`
--
ALTER TABLE `membershipcard`
  MODIFY `cardID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `orderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `orderpackage`
--
ALTER TABLE `orderpackage`
  MODIFY `orderPackageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `package`
--
ALTER TABLE `package`
  MODIFY `packageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `paymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staffID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `statushistory`
--
ALTER TABLE `statushistory`
  MODIFY `historyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `topup`
--
ALTER TABLE `topup`
  MODIFY `topupID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `fk_customer_user` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `fk_staff_user` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`);

--
-- Constraints for table `topup`
--
ALTER TABLE `topup`
  ADD CONSTRAINT `topup_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
