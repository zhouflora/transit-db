-------- Drop any remaining tables --------

DROP TABLE Station_Has_Amenities;
DROP TABLE StationLine_Scheduled_For_Timing;
DROP TABLE Timing;
DROP TABLE Line_Has_Station;
DROP TABLE Supervises_Line;
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
DROP TABLE Operates_In;
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
    geoLocX     NUMBER(9,6),
    geoLocY     NUMBER(9,6),
    stationName VARCHAR(40),
    zoneNumber  NUMBER(2),
    PRIMARY KEY (geoLocX, geoLocY)
);

CREATE TABLE Station
(
    stationID NUMBER(5) PRIMARY KEY,
    geoLocX   NUMBER(9,6) NOT NULL,
    geoLocY   NUMBER(9,6) NOT NULL,
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
    schedule   VARCHAR(255),
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
    storedValue NUMBER(6,2) DEFAULT 0,
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
    PRIMARY KEY (type, cardID),
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
    vehicleID       VARCHAR(10),
    model           VARCHAR(50) NOT NULL,
    yearMade        NUMBER(4)   NOT NULL,
    lastServiceDate DATE,
    PRIMARY KEY (vehicleID),
    FOREIGN KEY (model, yearMade) REFERENCES Vehicle_Capacity (model, yearMade)
);

CREATE TABLE Bus
(
    vehicleID VARCHAR(10),
    PRIMARY KEY (vehicleID),
    FOREIGN KEY (vehicleID) REFERENCES Vehicle
);

CREATE TABLE Train
(
    vehicleID VARCHAR(10),
    PRIMARY KEY (vehicleID),
    FOREIGN KEY (vehicleID) REFERENCES Vehicle
);

CREATE TABLE Ferry
(
    vehicleID VARCHAR(10),
    PRIMARY KEY (vehicleID),
    FOREIGN KEY (vehicleID) REFERENCES Vehicle
);

CREATE TABLE Operates_In
(
    vehicleID VARCHAR(10),
    lineName  VARCHAR(20),
    startDate DATE NOT NULL,
    endDate   DATE,
    PRIMARY KEY (vehicleID, lineName),
    CONSTRAINT op_in_fk FOREIGN KEY (vehicleID) REFERENCES Vehicle (vehicleID) ON DELETE CASCADE,
    CONSTRAINT op_in_fk_2 FOREIGN KEY (lineName) REFERENCES Line (lineName) ON DELETE CASCADE
);

CREATE TABLE Service_Cost
(
    serviceID   NUMBER(4),
    workDone    VARCHAR(255),
    cost        NUMBER(8,2),
    PRIMARY KEY (serviceID)
);

CREATE TABLE Services
(
    employeeID  VARCHAR(20),
    vehicleID   VARCHAR(10),
    serviceID   NUMBER(4) NOT NULL,
    serviceDate DATE,
    PRIMARY KEY (employeeID, vehicleID, serviceID),
    FOREIGN KEY (employeeID) REFERENCES Technician,
    FOREIGN KEY (vehicleID) REFERENCES Vehicle ON DELETE CASCADE,
    FOREIGN KEY (serviceID) REFERENCES Service_Cost
);

CREATE TABLE Drives
(
    employeeID   VARCHAR(20),
    vehicleID    VARCHAR(10),
    lineName     VARCHAR(20),
    startStation VARCHAR(100) NOT NULL,
    endStation   VARCHAR(100) NOT NULL,
    startDate    DATE         NOT NULL,
    endDate      DATE,
    PRIMARY KEY (employeeID, vehicleID, lineName),
    FOREIGN KEY (employeeID) REFERENCES Driver ON DELETE CASCADE,
    FOREIGN KEY (vehicleID, lineName) REFERENCES Operates_In ON DELETE CASCADE
);


-------- Insert data into tables --------

INSERT INTO Station_Location VALUES (49.323894, -123.111831, 'Marine Dr @ Capilano Rd', 2);
INSERT INTO Station_Location VALUES (49.258193, -123.214802, '16thAve @ Blanca St', 1);
INSERT INTO Station_Location VALUES (49.285641, -123.111956, 'Waterfront Stn', 1);
INSERT INTO Station_Location VALUES (49.262605, -123.069234, 'Commercial-Broadway Stn', 2);
INSERT INTO Station_Location VALUES (49.285566, -123.120318, 'Burrard Stn', 1);
INSERT INTO Station_Location VALUES (49.285791, -123.119479, 'Robson @ Granville St', 1);

INSERT INTO Station VALUES (123, 49.258193, -123.214802);
INSERT INTO Station VALUES (456, 49.285641, -123.111956);
INSERT INTO Station VALUES (789, 49.285791, -123.119479);
INSERT INTO Station VALUES (140, 49.323894, -123.111831);
INSERT INTO Station VALUES (240, 49.262605, -123.069234);
INSERT INTO Station VALUES (105, 49.285566, -123.120318);

INSERT INTO Line VALUES ('Bus44', 54.20, '0335FC', 300);
INSERT INTO Line VALUES ('SeaBus', 50.00, '4A433D', 2100.00);
INSERT INTO Line VALUES ('CanadaLine', 120.50, '0099E0', 1000.00);
INSERT INTO Line VALUES ('Bus14', 30.00, '0335FC', 100.00);
INSERT INTO Line VALUES ('MillenniumLine', 120.00, 'FFDD00', 1000.00);

INSERT INTO Timing VALUES (TO_TIMESTAMP('07:00:00', 'HH24:MI:SS'));
INSERT INTO Timing VALUES (TO_TIMESTAMP('07:10:00', 'HH24:MI:SS'));
INSERT INTO Timing VALUES (TO_TIMESTAMP('07:20:00', 'HH24:MI:SS'));
INSERT INTO Timing VALUES (TO_TIMESTAMP('23:10:00', 'HH24:MI:SS'));
INSERT INTO Timing VALUES (TO_TIMESTAMP('12:12:00', 'HH24:MI:SS'));
INSERT INTO Timing VALUES (TO_TIMESTAMP('12:45:00', 'HH24:MI:SS'));

INSERT INTO Station_Has_Amenities
VALUES ('Starbucks', 456, 1, 'Coffeeshop', 2000.00, TO_DATE('2010-03-15', 'YYYY-MM-DD'));
INSERT INTO Station_Has_Amenities
VALUES ('Subway', 456, 1, 'Fast Food', 2200.00, TO_DATE('2009-10-01', 'YYYY-MM-DD'));
INSERT INTO Station_Has_Amenities
VALUES ('Ticket Booth', 456, 2, 'Purchase transit tickets', 0.00, TO_DATE('2006-01-01', 'YYYY-MM-DD'));
INSERT INTO Station_Has_Amenities
VALUES ('Vending Machine', 105, 4, 'Fast Food', 2200.00, TO_DATE('2009-10-01', 'YYYY-MM-DD'));
INSERT INTO Station_Has_Amenities
VALUES ('Ticket Booth', 105, 2, 'Purchase transit tickets', 0.00, TO_DATE('2002-01-01', 'YYYY-MM-DD'));

INSERT INTO Line_Has_Station VALUES (456, 'CanadaLine', 'N', 10);
INSERT INTO Line_Has_Station VALUES (240, 'MillenniumLine', 'E', 4);
INSERT INTO Line_Has_Station VALUES (123, 'Bus14', 'E', 3);
INSERT INTO Line_Has_Station VALUES (456, 'SeaBus', 'N', 1);
INSERT INTO Line_Has_Station VALUES (140, 'Bus14', 'E', 48);

INSERT INTO StationLine_Scheduled_For_Timing
VALUES (456, 'CanadaLine', TO_TIMESTAMP('07:00:00', 'HH24:MI:SS'), 1, 1, 10);
INSERT INTO StationLine_Scheduled_For_Timing
VALUES (456, 'CanadaLine', TO_TIMESTAMP('23:10:00', 'HH24:MI:SS'), 1, 1, 10);
INSERT INTO StationLine_Scheduled_For_Timing
VALUES (123, 'Bus14', TO_TIMESTAMP('12:12:00', 'HH24:MI:SS'), 1, 1, NULL);
INSERT INTO StationLine_Scheduled_For_Timing
VALUES (456, 'SeaBus', TO_TIMESTAMP('23:10:00', 'HH24:MI:SS'), 0, 0, 5);
INSERT INTO StationLine_Scheduled_For_Timing
VALUES (240, 'MillenniumLine', TO_TIMESTAMP('07:20:00', 'HH24:MI:SS'), 1, 1, 8);

INSERT INTO OperationControlCenter
VALUES ('A8H6G2H837', 'Vancouver', 'Broadway-City-Hall-Control-Center');
INSERT INTO OperationControlCenter
VALUES ('HW7NK2YD8K', 'Vancouver', 'Mount-Pleasant-Center-of-Control');
INSERT INTO OperationControlCenter
VALUES ('H388HDLNAZ', 'Vancouver', 'UBC-Lands-Control-Center');
INSERT INTO OperationControlCenter
VALUES ('7H2OJDBZGA', 'Vancouver', 'River-District-Control-Center');
INSERT INTO OperationControlCenter
VALUES ('HE7BSKRUHF', 'Vancouver', 'Dunbar-Center-of-Control');

INSERT INTO Supervises_Line VALUES ('CanadaLine', '7H2OJDBZGA', 5);
INSERT INTO Supervises_Line VALUES ('MillenniumLine', '7H2OJDBZGA', 4);
INSERT INTO Supervises_Line VALUES ('Bus14', 'HW7NK2YD8K', 3);
INSERT INTO Supervises_Line VALUES ('SeaBus', 'A8H6G2H837', 1);
INSERT INTO Supervises_Line VALUES ('Bus44', 'HW7NK2YD8K', 12);

INSERT INTO Employee
VALUES ('OT4832946783', 'Sandeep Nata', 264368554, 43000, 'nata@gmail.com', TO_DATE('1986-02-20', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('OT6764274856', 'Florry Nasir', 807983843, 73500, 'nasir@gmail.com', TO_DATE('1991-09-27', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('OT6561815739', 'Evgenios Yoana', 241361031, 95000, 'yoana@gmail.com', TO_DATE('1984-04-11', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('OT8988005130', 'Patrizia Viktoryia', 173082338, 42000, 'viktoryia@gmail.com', TO_DATE('2000-04-20', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('OT3434791957', 'Tabitha Timeus', 356673301, 67000, 'timeus@gmail.com', TO_DATE('1961-05-20', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('DR5810205722', 'Karlo Lesego', 725717803, 74000, 'lesego@gmail.com', TO_DATE('1998-03-13', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('DR5303928084', 'Aurore Sigimund', 327221907, 82000, 'sigimund@gmail.com', TO_DATE('1997-03-06', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('DR7529032867', 'Wilky Ioseph', 358249419, 52000, 'ioseph@gmail.com', TO_DATE('1997-11-30', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('DR3850072567', 'Tresha Shanta', 600920298, 63000, 'shanta@gmail.com', TO_DATE('2000-11-29', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('DR0080015868', 'Romeo Ghjuvanni', 844672084, 81000, 'ghjuvanni@gmail.com', TO_DATE('1993-10-23', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('EN5612081378', 'Hodiyah Semele', 856716271, 112000, 'semele@gmail.com', TO_DATE('1964-08-23', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('EN1664253687', 'Rīta Cristiana', 671854520, 120000, 'cristiana@gmail.com', TO_DATE('1968-09-06', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('EN3772031155', 'Noemí Fraser', 634385809, 89000, 'fraser@gmail.com', TO_DATE('1984-04-03', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('EN3634046822', 'Jani Walerian', 984718442, 97000, 'walerian@gmail.com', TO_DATE('1992-03-19', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('EN3433393223', 'Musa Minty', 291357204, 102000, 'minty@gmail.com', TO_DATE('1998-10-09', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('TE2957869336', 'Anna Rembert', 219703456, 51000, 'rembert@gmail.com', TO_DATE('1982-04-21', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('TE9246381570', 'Rowina Nelu', 950763812, 48000, 'nelu@gmail.com', TO_DATE('2000-01-20', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('TE1408726359', 'Mani Ayumu', 602348719, 57000, 'ayumu@gmail.com', TO_DATE('1972-07-15', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('TE7653204981', 'Oscar Arthur', 318297064, 60000, 'arthur@gmail.com', TO_DATE('1996-09-20', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('TE8675012943', 'Augusta Marcela', 612934507, 52000, 'marcela@gmail.com', TO_DATE('1993-02-03', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('AD7356186759', 'Renia Bowie', 466112854, 57000, 'bowie@gmail.com', TO_DATE('1977-12-29', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('AD5251211307', 'Antiochos Tiwonge', 337005509, 62000, 'tiwonge@gmail.com',TO_DATE('1962-01-16', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('AD1375020921', 'Mirco Rosica', 095398894, 67000, 'rosica@gmail.com', TO_DATE('1981-04-16', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('AD4567109086', 'Leyla Blakely', 528305896, 56000, 'blakely@gmail.com', TO_DATE('1993-09-20', 'YYYY-MM-DD'));
INSERT INTO Employee
VALUES ('AD2750293038', 'Jenaro Ronan', 069734351, 61000, 'ronan@gmail.com', TO_DATE('1973-08-30', 'YYYY-MM-DD'));

INSERT INTO Driver VALUES ('DR5810205722');
INSERT INTO Driver VALUES ('DR5303928084');
INSERT INTO Driver VALUES ('DR7529032867');
INSERT INTO Driver VALUES ('DR3850072567');
INSERT INTO Driver VALUES ('DR0080015868');

INSERT INTO Engineer VALUES ('EN5612081378');
INSERT INTO Engineer VALUES ('EN1664253687');
INSERT INTO Engineer VALUES ('EN3772031155');
INSERT INTO Engineer VALUES ('EN3634046822');
INSERT INTO Engineer VALUES ('EN3433393223');

INSERT INTO Technician VALUES ('TE2957869336');
INSERT INTO Technician VALUES ('TE9246381570');
INSERT INTO Technician VALUES ('TE1408726359');
INSERT INTO Technician VALUES ('TE7653204981');
INSERT INTO Technician VALUES ('TE8675012943');

INSERT INTO Administrator VALUES ('AD7356186759');
INSERT INTO Administrator VALUES ('AD5251211307');
INSERT INTO Administrator VALUES ('AD1375020921');
INSERT INTO Administrator VALUES ('AD4567109086');
INSERT INTO Administrator VALUES ('AD2750293038');

INSERT INTO Employee_Works_Under_OCC
VALUES ('AD1375020921', 'A8H6G2H837', 2545, 240, 'April 2023: 1, 3, 5, 7, 10, 12');
INSERT INTO Employee_Works_Under_OCC
VALUES ('AD7356186759', 'HW7NK2YD8K', 4049, 151, 'April 2023: 3, 10, 12, 16, 24, 28');
INSERT INTO Employee_Works_Under_OCC
VALUES ('DR3850072567', 'H388HDLNAZ', 306, 0, 'April 2023: 1, 4, 7, 8, 10, 13, 16, 18, 22, 24, 27, 30');
INSERT INTO Employee_Works_Under_OCC
VALUES ('EN1664253687', '7H2OJDBZGA', 505, 10, 'April 2023: 3, 4, 7, 28');
INSERT INTO Employee_Works_Under_OCC
VALUES ('EN3772031155', '7H2OJDBZGA', 3125, 167, 'April 2023: 2, 4, 7, 8, 9, 12, 14, 17, 18, 21, 26, 27');

INSERT INTO UserAccount VALUES ('raymondng', 'bestprofever');
INSERT INTO UserAccount VALUES ('ansonchung', 'bestTAever');
INSERT INTO UserAccount VALUES ('flozhou', 'bbtenthusiast');
INSERT INTO UserAccount VALUES ('jsand01', 'bcnflegend');
INSERT INTO UserAccount VALUES ('payamfz', 'anythingEnthusiast1');

INSERT INTO UserAccount_Registers
VALUES ('flozhou', 'A8H6G2H837', TO_DATE('2023-02-01', 'YYYY-MM-DD'), 228158002);
INSERT INTO UserAccount_Registers
VALUES ('raymondng', 'HW7NK2YD8K', TO_DATE('2023-02-15', 'YYYY-MM-DD'), 228158146);
INSERT INTO UserAccount_Registers
VALUES ('jsand01', 'HW7NK2YD8K', TO_DATE('2023-02-16', 'YYYY-MM-DD'), 2281582232);
INSERT INTO UserAccount_Registers
VALUES ('payamfz', 'H388HDLNAZ', TO_DATE('2023-02-17', 'YYYY-MM-DD'), 2281582343);
INSERT INTO UserAccount_Registers
VALUES ('ansonchung', '7H2OJDBZGA', TO_DATE('2023-02-19', 'YYYY-MM-DD'), 2281582122);

INSERT INTO Card VALUES ('AF7905D714', 10.45);
INSERT INTO Card VALUES ('2C0EFC1076', 0.00);
INSERT INTO Card VALUES ('10C154982E', 0.75);
INSERT INTO Card VALUES ('31333C2090', 20.00);
INSERT INTO Card VALUES ('519552E6F3', 13.60);

INSERT INTO Card_Used_At_Station
VALUES ('AF7905D714', 123, TO_TIMESTAMP('2022-10-22 07:00:00', 'YYYY-MM-DD HH24:MI:SS'), 0);
INSERT INTO Card_Used_At_Station
VALUES ('AF7905D714', 456, TO_TIMESTAMP('2022-10-22 07:20:00', 'YYYY-MM-DD HH24:MI:SS'), 1);
INSERT INTO Card_Used_At_Station
VALUES ('31333C2090', 789, TO_TIMESTAMP('2019-10-10 23:10:00', 'YYYY-MM-DD HH24:MI:SS'), 0);
INSERT INTO Card_Used_At_Station
VALUES ('10C154982E', 140, TO_TIMESTAMP('2022-10-23 07:10:00', 'YYYY-MM-DD HH24:MI:SS'), 0);
INSERT INTO Card_Used_At_Station
VALUES ('AF7905D714', 123, TO_TIMESTAMP('2021-08-01 23:10:00', 'YYYY-MM-DD HH24:MI:SS'), 0);

INSERT INTO Card_Links_To VALUES ('AF7905D714', 'raymondng', TO_DATE('2017-04-19', 'YYYY-MM-DD'));
INSERT INTO Card_Links_To VALUES ('2C0EFC1076', 'ansonchung', TO_DATE('2018-02-19', 'YYYY-MM-DD'));
INSERT INTO Card_Links_To VALUES ('10C154982E', 'jsand01', TO_DATE('2010-02-19', 'YYYY-MM-DD'));
INSERT INTO Card_Links_To VALUES ('31333C2090', 'flozhou', TO_DATE('2009-01-22', 'YYYY-MM-DD'));
INSERT INTO Card_Links_To VALUES ('519552E6F3', 'payamfz', TO_DATE('2008-05-20', 'YYYY-MM-DD'));

INSERT INTO Pass_Loads_To
VALUES ('M', '6HA86HBNCJ', TO_DATE('2023-03-01', 'YYYY-MM-DD'), TO_DATE('2023-04-01', 'YYYY-MM-DD'), 'AF7905D714');
INSERT INTO Pass_Loads_To
VALUES ('D', 'AFC052E87F', TO_DATE('2023-03-01', 'YYYY-MM-DD'), TO_DATE('2023-03-02', 'YYYY-MM-DD'), '2C0EFC1076');
INSERT INTO Pass_Loads_To
VALUES ('M', '7CEE183B35', TO_DATE('2023-03-01', 'YYYY-MM-DD'), TO_DATE('2023-04-01', 'YYYY-MM-DD'), '10C154982E');
INSERT INTO Pass_Loads_To
VALUES ('M', 'A6FA8EEFE2', TO_DATE('2023-03-01', 'YYYY-MM-DD'), TO_DATE('2023-04-01', 'YYYY-MM-DD'), '31333C2090');
INSERT INTO Pass_Loads_To
VALUES ('M', '24AD7F80B6', TO_DATE('2023-03-01', 'YYYY-MM-DD'), TO_DATE('2023-04-01', 'YYYY-MM-DD'), '519552E6F3');

INSERT INTO Vehicle_Capacity VALUES ('Articulated', 2017, 75);
INSERT INTO Vehicle_Capacity VALUES ('Minibus', 2015, 30);
INSERT INTO Vehicle_Capacity VALUES ('Diesel-Electric', 2015, 40);
INSERT INTO Vehicle_Capacity VALUES ('Battery-Electric', 2020, 45);
INSERT INTO Vehicle_Capacity VALUES ('Hyundai Rotem', 2011, 450);
INSERT INTO Vehicle_Capacity VALUES ('West Coast Express', 1995, 300);
INSERT INTO Vehicle_Capacity VALUES ('Bombardier Innovia Metro 300', 2016, 700);
INSERT INTO Vehicle_Capacity VALUES ('Malaspina Sky', 2008, 462);
INSERT INTO Vehicle_Capacity VALUES ('Kahloke', 1973, 200);
INSERT INTO Vehicle_Capacity VALUES ('Coastal Celebration', 2008, 300);
INSERT INTO Vehicle_Capacity VALUES ('Baynes Sound Connector', 2015, 220);

INSERT INTO Vehicle VALUES ('2c994m', 'Minibus', 2015, TO_DATE('2011-06-07', 'YYYY-MM-DD'));
INSERT INTO Vehicle VALUES ('adef70', 'Diesel-Electric', 2015, TO_DATE('2020-09-04', 'YYYY-MM-DD'));
INSERT INTO Vehicle VALUES ('f0b2a1', 'Articulated', 2017, TO_DATE('2016-01-21', 'YYYY-MM-DD'));
INSERT INTO Vehicle VALUES ('6d1d10', 'Diesel-Electric', 2015, TO_DATE('2020-09-04', 'YYYY-MM-DD'));
INSERT INTO Vehicle VALUES ('c47259', 'Battery-Electric', 2020, TO_DATE('2022-04-17', 'YYYY-MM-DD'));
INSERT INTO Vehicle VALUES ('daaa1f', 'Hyundai Rotem', 2011, TO_DATE('2021-07-19', 'YYYY-MM-DD'));
INSERT INTO Vehicle VALUES ('e886a6', 'West Coast Express', 1995, TO_DATE('2021-12-15', 'YYYY-MM-DD'));
INSERT INTO Vehicle VALUES ('a60cf4', 'Bombardier Innovia Metro 300', 2016, TO_DATE('2020-02-25', 'YYYY-MM-DD'));
INSERT INTO Vehicle VALUES ('236572', 'Bombardier Innovia Metro 300', 2016, TO_DATE('2016-03-17', 'YYYY-MM-DD'));
INSERT INTO Vehicle VALUES ('fa1534', 'West Coast Express', 1995, TO_DATE('2022-06-29', 'YYYY-MM-DD'));
INSERT INTO Vehicle VALUES ('be2626', 'Kahloke', 1973, TO_DATE('2016-08-17', 'YYYY-MM-DD'));
INSERT INTO Vehicle VALUES ('9a8a61', 'Malaspina Sky', 2008, TO_DATE('2019-12-09', 'YYYY-MM-DD'));
INSERT INTO Vehicle VALUES ('efa24a', 'Coastal Celebration', 2008, TO_DATE('2020-09-10', 'YYYY-MM-DD'));
INSERT INTO Vehicle VALUES ('c6f3f0', 'Coastal Celebration', 2008, TO_DATE('2021-09-22', 'YYYY-MM-DD'));
INSERT INTO Vehicle VALUES ('563cc2', 'Baynes Sound Connector', 2015, TO_DATE('2022-01-12', 'YYYY-MM-DD'));

INSERT INTO Bus VALUES ('2c994m');
INSERT INTO Bus VALUES ('adef70');
INSERT INTO Bus VALUES ('f0b2a1');
INSERT INTO Bus VALUES ('6d1d10');
INSERT INTO Bus VALUES ('c47259');

INSERT INTO Train VALUES ('daaa1f');
INSERT INTO Train VALUES ('e886a6');
INSERT INTO Train VALUES ('a60cf4');
INSERT INTO Train VALUES ('236572');
INSERT INTO Train VALUES ('fa1534');

INSERT INTO Ferry VALUES ('be2626');
INSERT INTO Ferry VALUES ('9a8a61');
INSERT INTO Ferry VALUES ('efa24a');
INSERT INTO Ferry VALUES ('c6f3f0');
INSERT INTO Ferry VALUES ('563cc2');

INSERT INTO Operates_In
VALUES ('2c994m', 'Bus14', TO_DATE('2019-07-31', 'YYYY-MM-DD'), TO_DATE('2022-06-27', 'YYYY-MM-DD'));
INSERT INTO Operates_In
VALUES ('e886a6', 'CanadaLine', TO_DATE('2021-01-21', 'YYYY-MM-DD'), null);
INSERT INTO Operates_In
VALUES ('f0b2a1', 'Bus44', TO_DATE('2022-05-10', 'YYYY-MM-DD'), null);
INSERT INTO Operates_In
VALUES ('a60cf4', 'MillenniumLine', TO_DATE('2020-05-28', 'YYYY-MM-DD'), TO_DATE('2022-07-28', 'YYYY-MM-DD'));
INSERT INTO Operates_In
VALUES ('9a8a61', 'SeaBus', TO_DATE('2021-07-20', 'YYYY-MM-DD'), TO_DATE('2022-02-15', 'YYYY-MM-DD'));

INSERT INTO Service_Cost VALUES (1001, 'Oil Change', 250);
INSERT INTO Service_Cost VALUES (1007, 'Brake Check', 200);
INSERT INTO Service_Cost VALUES (2004, 'Battery Replacement', 300);
INSERT INTO Service_Cost VALUES (1024, 'Air Filter Replacement', 100);
INSERT INTO Service_Cost VALUES (1000, 'Exterior Cleaning', 60);

INSERT INTO Services VALUES ('TE2957869336', '2c994m', 1001, TO_DATE('2019-02-05', 'YYYY-MM-DD'));
INSERT INTO Services VALUES ('TE2957869336', '2c994m', 1007, TO_DATE('2023-01-28', 'YYYY-MM-DD'));
INSERT INTO Services VALUES ('TE2957869336', 'adef70', 1024, TO_DATE('2023-01-27', 'YYYY-MM-DD'));
INSERT INTO Services VALUES ('TE2957869336', 'f0b2a1', 1001, TO_DATE('2023-01-26', 'YYYY-MM-DD'));
INSERT INTO Services VALUES ('TE2957869336', '6d1d10', 1001, TO_DATE('2022-06-08', 'YYYY-MM-DD'));
INSERT INTO Services VALUES ('TE2957869336', 'c47259', 1001, TO_DATE('2020-07-18', 'YYYY-MM-DD'));
INSERT INTO Services VALUES ('TE7653204981', 'f0b2a1', 2004, TO_DATE('2011-06-07', 'YYYY-MM-DD'));
INSERT INTO Services VALUES ('TE7653204981', 'f0b2a1', 1024, TO_DATE('2011-06-07', 'YYYY-MM-DD'));
INSERT INTO Services VALUES ('TE7653204981', '2c994m', 1007, TO_DATE('2023-01-28', 'YYYY-MM-DD'));
INSERT INTO Services VALUES ('TE7653204981', 'adef70', 1024, TO_DATE('2019-04-12', 'YYYY-MM-DD'));
INSERT INTO Services VALUES ('TE7653204981', '6d1d10', 1001, TO_DATE('2019-04-12', 'YYYY-MM-DD'));
INSERT INTO Services VALUES ('TE7653204981', 'c47259', 2004, TO_DATE('2020-07-18', 'YYYY-MM-DD'));
INSERT INTO Services VALUES ('TE8675012943', '2c994m', 1000, TO_DATE('2010-10-28', 'YYYY-MM-DD'));

INSERT INTO Drives
VALUES ('DR5810205722', '2c994m', 'Bus14', 'UBC Exchange', 'Northbound Homer St',
        TO_DATE('2020-02-23', 'YYYY-MM-DD'), TO_DATE('2022-06-27', 'YYYY-MM-DD'));
INSERT INTO Drives
VALUES ('DR5303928084', '2c994m', 'Bus14', 'Northbound Homer St', 'UBC Exchange',
        TO_DATE('2022-05-10', 'YYYY-MM-DD'), TO_DATE('2022-08-18', 'YYYY-MM-DD'));
INSERT INTO Drives
VALUES ('DR7529032867', 'f0b2a1', 'Bus44', 'Metrotown', 'UBC Exchange',
        TO_DATE('2021-03-12', 'YYYY-MM-DD'), TO_DATE('2023-01-28', 'YYYY-MM-DD'));
INSERT INTO Drives
VALUES ('DR3850072567', 'a60cf4', 'MillenniumLine', 'VCC–Clark Station', 'Lafarge Lake–Douglas Station',
        TO_DATE('2020-05-28', 'YYYY-MM-DD'), TO_DATE('2022-02-09', 'YYYY-MM-DD'));
INSERT INTO Drives
VALUES ('DR0080015868', 'f0b2a1', 'Bus44', 'Joyce Station', 'UBC Exchange',
        TO_DATE('2021-12-02', 'YYYY-MM-DD'), TO_DATE('2022-02-15', 'YYYY-MM-DD'));
