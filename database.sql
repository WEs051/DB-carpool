-- Areas (zones)
CREATE TABLE `Area` (
  `ZoneID` int(11) NOT NULL,
  `ZoneName` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`ZoneID`),
  UNIQUE KEY `ZoneName` (`ZoneName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Address table (each address has a Zone_ID referencing Area)
CREATE TABLE `Address` (
  `AddressID` int(11) NOT NULL AUTO_INCREMENT,
  `StreetName` varchar(50) DEFAULT NULL,
  `StreetNumber` int(11) DEFAULT NULL,
  `PostalCode` varchar(20) DEFAULT NULL,
  `Zone_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`AddressID`),
  KEY `idx_address_zone` (`Zone_ID`),
  CONSTRAINT `address_ibfk_1` FOREIGN KEY (`Zone_ID`) REFERENCES `Area` (`ZoneID`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- StudentUser (keeps the extra attributes per your request)
CREATE TABLE `StudentUser` (
  `StudentID` int(11) NOT NULL,
  `StudentName` varchar(50) DEFAULT NULL,
  `Gender` varchar(20) DEFAULT NULL,
  `AddressID` int(11) DEFAULT NULL,   -- optional main address
  `StreetName` varchar(30) DEFAULT NULL,
  `StreetNumber` int(11) DEFAULT NULL,
  `PostalCode` varchar(20) DEFAULT NULL,
  `Zone_ID` int(11) DEFAULT NULL,     -- zone reference
  `Height` double DEFAULT NULL,
  PRIMARY KEY (`StudentID`),
  KEY `idx_student_zone` (`Zone_ID`),
  KEY `idx_student_address` (`AddressID`),
  CONSTRAINT `studentuser_ibfk_1` FOREIGN KEY (`Zone_ID`) REFERENCES `Area` (`ZoneID`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,
  CONSTRAINT `studentuser_ibfk_2` FOREIGN KEY (`AddressID`) REFERENCES `Address` (`AddressID`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Schedules: schedule master
CREATE TABLE `Schedules` (
  `ScheduleID` int(11) NOT NULL AUTO_INCREMENT,
  `Time` char(7) DEFAULT NULL,   -- e.g. "08:30AM"
  `Date` date DEFAULT NULL,
  PRIMARY KEY (`ScheduleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Associative: Students have schedules (Has_a)
CREATE TABLE `SchedulesHas_a` (
  `StudentID` int(11) NOT NULL,
  `ScheduleID` int(11) NOT NULL,
  PRIMARY KEY (`StudentID`,`ScheduleID`),
  KEY `idx_sh_student` (`StudentID`),
  KEY `idx_sh_schedule` (`ScheduleID`),
  CONSTRAINT `scheduleshas_a_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `StudentUser` (`StudentID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `scheduleshas_a_ibfk_2` FOREIGN KEY (`ScheduleID`) REFERENCES `Schedules` (`ScheduleID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Has_an: link between StudentUser and Address (student has address(es))
CREATE TABLE `Has_an` (
  `StudentID` int(11) NOT NULL,
  `AddressID` int(11) NOT NULL,
  `StreetName` varchar(50) DEFAULT NULL,
  `StreetNumber` int(11) DEFAULT NULL,
  `PostalCode` varchar(20) DEFAULT NULL,
  `Zone_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`StudentID`,`AddressID`),
  KEY `idx_has_an_zone` (`Zone_ID`),
  CONSTRAINT `has_an_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `StudentUser` (`StudentID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `has_an_ibfk_2` FOREIGN KEY (`AddressID`) REFERENCES `Address` (`AddressID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `has_an_ibfk_3` FOREIGN KEY (`Zone_ID`) REFERENCES `Area` (`ZoneID`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Subtype: Riders (ISA StudentUser)
CREATE TABLE `Riders` (
  `StudentID` int(11) NOT NULL,
  PRIMARY KEY (`StudentID`),
  CONSTRAINT `riders_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `StudentUser` (`StudentID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Subtype: Providers (ISA StudentUser)
CREATE TABLE `Providers` (
  `StudentID` int(11) NOT NULL,
  -- add provider-specific attributes here if needed (e.g., license number)
  PRIMARY KEY (`StudentID`),
  CONSTRAINT `providers_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `StudentUser` (`StudentID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Vehicle table: each vehicle is owned by a provider
CREATE TABLE `Vehicle` (
  `CarPlateID` char(20) NOT NULL,
  `CarModel` varchar(50) DEFAULT NULL,
  `OwnerStudentID` int(11) DEFAULT NULL,  -- FK to Providers (a provider owns the vehicle)
  PRIMARY KEY (`CarPlateID`),
  KEY `idx_vehicle_owner` (`OwnerStudentID`),
  CONSTRAINT `vehicle_ibfk_1` FOREIGN KEY (`OwnerStudentID`) REFERENCES `Providers` (`StudentID`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Destination: uses Address (AddressID is PK & FK to Address)
CREATE TABLE `Destination` (
  `AddressID` int(11) NOT NULL,
  `TimeTillArrival` int(11) DEFAULT NULL,
  PRIMARY KEY (`AddressID`),
  CONSTRAINT `destination_ibfk_1` FOREIGN KEY (`AddressID`) REFERENCES `Address` (`AddressID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- PickUp: uses Address (AddressID is PK & FK to Address)
CREATE TABLE `PickUp` (
  `AddressID` int(11) NOT NULL,
  `TimeTillPickUp` int(11) DEFAULT NULL,
  PRIMARY KEY (`AddressID`),
  CONSTRAINT `pickup_ibfk_1` FOREIGN KEY (`AddressID`) REFERENCES `Address` (`AddressID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- IsRidingWith: associative relationship between Riders (organizer & comesWith)
CREATE TABLE `IsRidingWith` (
  `OrganizesRide_SID` int(11) NOT NULL,
  `ComesWith_SID` int(11) NOT NULL,
  PRIMARY KEY (`OrganizesRide_SID`,`ComesWith_SID`),
  KEY `idx_isrw_organizer` (`OrganizesRide_SID`),
  KEY `idx_isrw_comeswith` (`ComesWith_SID`),
  CONSTRAINT `isridingwith_ibfk_1` FOREIGN KEY (`OrganizesRide_SID`) REFERENCES `Riders` (`StudentID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `isridingwith_ibfk_2` FOREIGN KEY (`ComesWith_SID`) REFERENCES `Riders` (`StudentID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
