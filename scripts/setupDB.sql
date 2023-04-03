-------- Drop any remaining tables --------

DROP TABLE Station_Has_Amenities;
DROP TABLE Line_Has_Station;
DROP TABLE Timing;
DROP TABLE StationLine_Scheduled_For_Timing;
DROP TABLE Supervises_Line;
DROP TABLE Operates_In;
DROP TABLE Services;
DROP TABLE Service_Cost;
DROP TABLE Employee_Works_Under_OCC;
DROP TABLE Card_Links_To;
DROP TABLE Pass_Loads_To;
DROP TABLE Card_Used_At_Station;
DROP TABLE Card;
DROP TABLE UserAccount_Registers;
DROP TABLE UserAccount;
DROP TABLE Drives;
DROP TABLE Bus;
DROP TABLE Train;
DROP TABLE Ferry;
DROP TABLE Vehicle;
DROP TABLE Vehicle_Capacity;
DROP TABLE Driver;
DROP TABLE Engineer;
DROP TABLE Technician;
DROP TABLE Administrator;
DROP TABLE Employee;
DROP TABLE OperationControlCenter;
DROP TABLE Line;
DROP TABLE Station;
DROP TABLE Station_Location;


-------- Create tables --------

CREATE TABLE Station_Location
(
    geoLocX     FLOAT,
    geoLocY     FLOAT,
    stationName VARCHAR(40),
    zoneNumber  NUMBER(2),
    PRIMARY KEY (geoLocX, geoLocY)
);

CREATE TABLE Station
(
    stationID NUMBER(5) PRIMARY KEY,
    geoLocX   FLOAT NOT NULL,
    geoLocY   FLOAT NOT NULL,
    FOREIGN KEY (geoLocX, geoLocY) REFERENCES Station_Location ON DELETE CASCADE
);

CREATE TABLE Line
(
    lineName                   VARCHAR(20) PRIMARY KEY,
    avgSpeed                   NUMBER(5,2),
    mapColour                  CHAR(6),
    avgDistanceBetweenStations NUMBER(6,2)
);

CREATE TABLE Timing
(
    arrivalTime TIMESTAMP PRIMARY KEY
);

CREATE TABLE Station_Has_Amenities
(
    amenityName     VARCHAR(40),
    stationID       NUMBER(5),
    count           NUMBER(2) DEFAULT 1,
    description     VARCHAR(50),
    leaseRate       FLOAT,
    dateEstablished DATE,
    PRIMARY KEY (amenityName, stationID),
    FOREIGN KEY (stationID) REFERENCES Station ON DELETE CASCADE
);

CREATE TABLE Line_Has_Station
(
    stationID    NUMBER(5),
    lineName     VARCHAR(20),
    direction    CHAR(1),
    stationOrder NUMBER(3),
    PRIMARY KEY (stationID, lineName),
    FOREIGN KEY (stationID) REFERENCES Station ON DELETE CASCADE,
    FOREIGN KEY (lineName) REFERENCES Line ON DELETE CASCADE
);

CREATE TABLE StationLine_Scheduled_For_Timing
(
    stationID        NUMBER(5),
    lineName         VARCHAR(20),
    arrivalTime      TIMESTAMP,
    activeOnHolidays NUMBER(1) DEFAULT 1,
    activeOnWeekends NUMBER(1) DEFAULT 1,
    congestion       NUMBER(2) DEFAULT 0,
    PRIMARY KEY (stationID, lineName, arrivalTime),
    FOREIGN KEY (stationID, lineName) REFERENCES Line_Has_Station (stationID, lineName) ON DELETE CASCADE,
    FOREIGN KEY (arrivalTime) REFERENCES Timing ON DELETE SET NULL
);

CREATE TABLE OperationControlCenter
(
    centreID CHAR(10),
    location VARCHAR(40) NOT NULL,
    name     VARCHAR(40) NOT NULL,
    PRIMARY KEY (centreID),
    UNIQUE (name)
);

CREATE TABLE Supervises_Line
(
    lineName      VARCHAR(20),
    centreID      CHAR(10) NOT NULL,
    cameraNumbers NUMBER(5),
    PRIMARY KEY (lineName),
    FOREIGN KEY (lineName) REFERENCES Line ON DELETE CASCADE,
    FOREIGN KEY (centreID) REFERENCES OperationControlCenter ON DELETE CASCADE
);

CREATE TABLE Employee
(
    employeeID  VARCHAR(20),
    name        VARCHAR(50),
    ssn         NUMBER(9),
    salary      NUMBER(8,2),
    email       VARCHAR(50),
    dateOfBirth DATE,
    PRIMARY KEY (employeeID),
    UNIQUE (ssn, email)
);

CREATE TABLE Driver
(
    employeeID  VARCHAR(20),
    PRIMARY KEY (employeeID),
    FOREIGN KEY (employeeID) REFERENCES Employee ON DELETE CASCADE
);

CREATE TABLE Engineer
(
    employeeID  VARCHAR(20),
    PRIMARY KEY (employeeID),
    FOREIGN KEY (employeeID) REFERENCES Employee ON DELETE CASCADE
);

CREATE TABLE Technician
(
    employeeID  VARCHAR(20),
    PRIMARY KEY (employeeID),
    FOREIGN KEY (employeeID) REFERENCES Employee ON DELETE CASCADE
);

CREATE TABLE Administrator
(
    employeeID  VARCHAR(20),
    PRIMARY KEY (employeeID),
    FOREIGN KEY (employeeID) REFERENCES Employee ON DELETE CASCADE
);

CREATE TABLE Employee_Works_Under_OCC
(
    employeeID  VARCHAR(20),
    centreID    CHAR(10),
    hoursWorked INTEGER,
    daysOff    NUMBER(4),
    schedule   VARCHAR(20),
    PRIMARY KEY (employeeID, centreID),
    FOREIGN KEY (centreID) REFERENCES OperationControlCenter,
    FOREIGN KEY (employeeID) REFERENCES Employee
);

CREATE TABLE UserAccount
(
    username VARCHAR(50),
    password VARCHAR(100) NOT NULL,
    PRIMARY KEY (username)
);

CREATE TABLE UserAccount_Registers
(
    username           VARCHAR(50),
    centreID           CHAR(10) NOT NULL,
    registrationDate   DATE,
    registrationNumber NUMBER(10),
    PRIMARY KEY (username),
    FOREIGN KEY (username) REFERENCES UserAccount,
    FOREIGN KEY (centreID) REFERENCES OperationControlCenter
);

CREATE TABLE Card
(
    cardID      CHAR(10),
    storedValue DECIMAL DEFAULT 0,
    PRIMARY KEY (cardID)
);

CREATE TABLE Card_Used_At_Station
(
    cardID    CHAR(10),
    stationID NUMBER(5),
    time      TIMESTAMP,
    isExit    NUMBER(1) DEFAULT 0,
    PRIMARY KEY (cardID, stationID, time),
    FOREIGN KEY (cardID) REFERENCES Card,
    FOREIGN KEY (stationID) REFERENCES Station
);

CREATE TABLE Card_Links_To
(
    cardID         CHAR(10),
    username       VARCHAR(50),
    activationDate DATE,
    PRIMARY KEY (cardID),
    UNIQUE (username),
    FOREIGN KEY (cardID) REFERENCES Card ON DELETE CASCADE,
    FOREIGN KEY (username) REFERENCES UserAccount
);

CREATE TABLE Pass_Loads_To
(
    type          CHAR(1),
    transactionID CHAR(10),
    purchaseDate  DATE,
    expiryDate    DATE,
    cardID        CHAR(10),
    PRIMARY KEY (type),
    UNIQUE (transactionID, cardID),
    FOREIGN KEY (cardID) REFERENCES Card ON DELETE CASCADE
);

CREATE TABLE Vehicle_Capacity
(
    model             VARCHAR(50),
    yearMade          NUMBER(4),
    passengerCapacity NUMBER(3),
    PRIMARY KEY (model, yearMade)
);

CREATE TABLE Vehicle
(
    vehicleID       VARCHAR(20),
    model           VARCHAR(50) NOT NULL,
    yearMade        NUMBER(4)   NOT NULL,
    lastServiceDate DATE,
    PRIMARY KEY (vehicleID),
    FOREIGN KEY (model, yearMade) REFERENCES Vehicle_Capacity (model, yearMade)
);

CREATE TABLE Bus
(
    vehicleID VARCHAR(20),
    PRIMARY KEY (vehicleID),
    FOREIGN KEY (vehicleID) REFERENCES Vehicle
);

CREATE TABLE Train
(
    vehicleID VARCHAR(20),
    PRIMARY KEY (vehicleID),
    FOREIGN KEY (vehicleID) REFERENCES Vehicle
);

CREATE TABLE Ferry
(
    vehicleID VARCHAR(20),
    PRIMARY KEY (vehicleID),
    FOREIGN KEY (vehicleID) REFERENCES Vehicle
);

CREATE TABLE Operates_In
(
    vehicleID VARCHAR(20),
    lineName  VARCHAR(20),
    startDate DATE NOT NULL,
    endDate   DATE,
    PRIMARY KEY (vehicleID, lineName),
    CONSTRAINT op_in_fk FOREIGN KEY (vehicleID) REFERENCES Bus (vehicleID) ON DELETE CASCADE,
    CONSTRAINT op_in_fk_2 FOREIGN KEY (lineName) REFERENCES Line (lineName) ON DELETE CASCADE
);

CREATE TABLE Service_Cost
(
    workDone VARCHAR(255),
    cost     NUMBER(8,2),
    PRIMARY KEY (workDone)
);

CREATE TABLE Services
(
    employeeID  VARCHAR(20),
    vehicleID   VARCHAR(20),
    workDone    VARCHAR(255) NOT NULL,
    serviceDate DATE,
    PRIMARY KEY (employeeID, vehicleID),
    FOREIGN KEY (employeeID) REFERENCES Technician ON DELETE CASCADE,
    FOREIGN KEY (vehicleID) REFERENCES Vehicle,
    FOREIGN KEY (workDone) REFERENCES Service_Cost
);

CREATE TABLE Drives
(
    employeeID   VARCHAR(20),
    vehicleID    VARCHAR(20),
    name         VARCHAR(50),
    startStation VARCHAR(100) NOT NULL,
    endStation   VARCHAR(100) NOT NULL,
    startDate    DATE         NOT NULL,
    endDate      DATE,
    PRIMARY KEY (employeeID, vehicleID, name),
    FOREIGN KEY (employeeID) REFERENCES Driver ON DELETE CASCADE,
    FOREIGN KEY (vehicleID) REFERENCES Vehicle ON DELETE CASCADE,
    FOREIGN KEY (name) REFERENCES Line
);
