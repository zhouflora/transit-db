<!--Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  This file shows the very basics of how to execute PHP commands
  on Oracle.
  Specifically, it will drop a table, create a table, insert values
  update values, and then query for values

  IF YOU HAVE A TABLE CALLED "demoTable" IT WILL BE DESTROYED

  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the
  OCILogon below to be your ORACLE username and password -->

<html>

<head>
    <title>CPSC 304 PHP/Oracle Demonstration</title>
</head>

<body>
    <h2>Reset</h2>
    <p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you
        MUST use reset</p>

    <form method="POST" action="oracle-test.php">
        <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
        <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
        <p><input type="submit" value="Reset" name="reset"></p>
    </form>

    <hr />

    <h2>Insert Values into DemoTable</h2>
    <form method="POST" action="oracle-test.php">
        <!--refresh page when submitted-->
        <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
        Username: <input type="text" name="username"> <br /><br />
        Password: <input type="password" name="password"> <br /><br />

        <input type="submit" value="Insert" name="insertSubmit"></p>
    </form>

    <hr />

    <h2>Update Name in DemoTable</h2>
    <p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>

    <form method="POST" action="oracle-test.php">
        <!--refresh page when submitted-->
        <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">

        UserName: <input type="text" name="userName"> <br /><br />

        Old Password: <input type="password" name="oldPass"> <br /><br />
        New Password: <input type="password" name="newPass"> <br /><br />

        <input type="submit" value="Update" name="updateSubmit"></p>
    </form>

    <hr />

    <h2>Active Lines</h2>
    <form method="GET" action="oracle-test.php">
        <!--refresh page when submitted-->
        <input type="hidden" id="countTupleRequest" name="countTupleRequest">
        Active on Weekends<input type="checkbox" name="weekend" value=1> <br /><br />
        Active on Holidays<input type="checkbox" name="holiday" value=1> <br /><br />
        <input type="submit" name="countTuples"></p>
    </form>

    <hr />

    <hr />

    <h2>Projection</h2>
    <form method="GET" action="oracle-test.php" target="_blank"> <!--refresh page when submitted-->
        <input type="hidden" id="projectionQueryRequest" name="projectionQueryRequest">
        Click below to see a list of all the tables in this database <br /><br />
        <input type="submit" value="See Table Data" name="viewTableNames"></p>
    </form>

    <h3>Choose Table To View</h3>
    <form method="GET" action="oracle-test.php"> <!--refresh page when submitted-->
        <input type="hidden" id="tableSchemaRequest" name="tableSchemaRequest">

        Please type the name (in capitals) of any one table to view its schema details (eg. LINE_HAS_STATION)
        <br /><br />
        <input type="text" name="tabName">
        <input type="submit" value="Confirm" name="viewTableSchema"></p>

        <h3>Retrieve Subset of Data</h3>
        <input type="hidden" id="projectionFinalRequest" name="projectionFinalRequest">
        Please type the name of the table you want to extract data from, followed by the names of the columns (in
        capitals) from the table.
        Separate inputs with commas (eg. "LINE_HAS_STATION, STATIONID, LINENAME") <br /><br />
        <input type="text" name="colName">
        <input type="submit" value="Confirm" name="projectionSubmit"></p>
    </form>

    <hr />

    <h2>Delete Tuples</h2>
        <form name="delete" method="POST" action="fztest.php"> <!--refresh page when submitted-->
            <input type="hidden" id="deleteTupleRequest" name="deleteTupleRequest">
            Select the item you wish to delete <br>
            <select name="deleteFromTuple" id="deleteFromTUple">
                <option value="" disable selected>Select...</option>;
                <option value="USERACCOUNT">USERACCOUNT</option>;
                <option value="CARD">CARD</option>;
            </select>
            <input type="submit" value="Confirm" name="deleteSubmit"></p>

            <input type="hidden" id="deleteColumnsRequest" name="deleteColumnsRequest">
            Please confirm your information here
            <input type="text" name="idData"> 
            <input type="submit" value="Confirm Delete" name="deleteColumnsSubmit"></p>
        </form> 

    <hr />

    <h2>Aggregation: Group By Query</h2>
    <form method="POST" action="oracle-test.php">
        <!--refresh page when submitted-->
        <input type="hidden" id="groupByRequest" name="groupByRequest">
        For each transit line, this will find the number of stations that are part of that line. <br /><br />

        <input type="submit" value="Get Count" name="groupBySubmit"></p>
    </form>

    <hr />

    <h2>Aggregation: Having Query</h2>
    <form method="POST" action="oracle-test.php">
        <!--refresh page when submitted-->
        <input type="hidden" id="havingRequest" name="havingRequest">
        For each station, find the total amount of transit lines that pass through them, only for stations
        that are passed through more than one line <br /><br />
        <input type="submit" value="Get Results" name="havingSubmit"></p>
    </form>

    <h2>Nested Aggregation: Group By</h2>
    <form method="POST" action="new-test.php">
        <!--refresh page when submitted-->
        <input type="hidden" id="groupByRequest" name="groupByRequest">
        This will find the most popular type of pass purchased by users. <br /><br />
        <input type="submit" value="Find Pass" name="groupBySubmit"></p>
    </form>

    <?php
    //this tells the system that it's no longer just parsing html; it's now parsing PHP
    
    $success = True; //keep track of errors so it redirects the page only if there are no errors
    $db_conn = NULL; // edit the login credentials in connectToDB()
    $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())
    
    function debugAlertMessage($message)
    {
        global $show_debug_alert_messages;

        if ($show_debug_alert_messages) {
            echo "<script type='text/javascript'>alert('" . $message . "');</script>";
        }
    }

    function executePlainSQL($cmdstr)
    { //takes a plain (no bound variables) SQL command and executes it
        //echo "<br>running ".$cmdstr."<br>";
        global $db_conn, $success;

        $statement = OCIParse($db_conn, $cmdstr);
        //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work
    
        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
            echo htmlentities($e['message']);
            $success = False;
        }

        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
            echo htmlentities($e['message']);
            $success = False;
        }

        return $statement;
    }

    function executeBoundSQL($cmdstr, $list)
    {
        /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
        In this case you don't need to create the statement several times. Bound variables cause a statement to only be
        parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
        See the sample code below for how this function is used */

        global $db_conn, $success;
        $statement = OCIParse($db_conn, $cmdstr);

        if (!$statement) {
            echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($db_conn);
            echo htmlentities($e['message']);
            $success = False;
        }

        foreach ($list as $tuple) {
            foreach ($tuple as $bind => $val) {
                //echo $val;
                //echo "<br>".$bind."<br>";
                OCIBindByName($statement, $bind, $val);
                unset($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                echo htmlentities($e['message']);
                echo "<br>";
                $success = False;
            }
        }
    }

    function printResult($result)
    { //prints results from a select statement
        echo "<br>Retrieved data from table demoTable:<br>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th></tr>";

        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]"
        }

        echo "</table>";
    }

    function connectToDB()
    {
        global $db_conn;

        // Your username is ora_(CWL_ID) and the password is a(student number). For example,
        // ora_platypus is the username and a12345678 is the password.
        $db_conn = OCILogon("ora_payamfz", "a51597292", "dbhost.students.cs.ubc.ca:1522/stu");

        if ($db_conn) {
            debugAlertMessage("Database is Connected");
            return true;
        } else {
            debugAlertMessage("Cannot connect to Database");
            $e = OCI_Error(); // For OCILogon errors pass no handle
            echo htmlentities($e['message']);
            return false;
        }
    }

    function disconnectFromDB()
    {
        global $db_conn;

        debugAlertMessage("Disconnect from Database");
        OCILogoff($db_conn);
    }

    function handleUpdateRequest()
    {
        global $db_conn;

        $name = $_POST['userName'];
        $old_pass = $_POST['oldPass'];
        $new_pass = $_POST['newPass'];

        // you need the wrap the old name and new name values with single quotations
        executePlainSQL("UPDATE UserAccount SET password='" . $new_pass . "' WHERE password='" . $old_pass . "' AND username='" . $name . "'");
        OCICommit($db_conn);
    }

    function handleResetRequest()
    {
        global $db_conn;
        // Drop old table
        executePlainSQL("DROP TABLE demoTable");

        // Create new table
        echo "<br> creating new table <br>";
        executePlainSQL("CREATE TABLE demoTable (id int PRIMARY KEY, name char(30))");
        OCICommit($db_conn);
    }


    function handleInsertRequest()
    {
        global $db_conn;

        $userAccountInfo = array(
            ":bind1" => $_POST['username'],
            ":bind2" => $_POST['password']
        );

        // function returns a unique 32 bit int catered to username
        $random_int = crc32($_POST['username']);

        $userAccountRegister = array (
            ":bind1" => $_POST['username'],
            ":bind3" => 'A8H6G2H837',
            ":bind5" => $random_int
        );

        $alltuples1 = array(
            $userAccountInfo
        );

        $alltuples2 = array(
            $userAccountRegister
        );

        executeBoundSQL("insert into UserAccount values (:bind1, :bind2)", $alltuples1);
        executeBoundSQL("insert into UserAccount_Registers values (:bind1, :bind3, SYSDATE, :bind5)", $alltuples2);

        OCICommit($db_conn);
    }

    function handleCountRequest()
    {
        global $db_conn;

        $weekends = $_GET['weekend'];
        $holidays = $_GET['holiday'];

        $result = executePlainSQL("SELECT lineName FROM StationLine_Scheduled_For_Timing WHERE activeOnWeekends = '" . $weekends . "' OR activeOnHolidays = '" . $holidays . "'");

        if (($row = oci_fetch_row($result)) != false) {
            echo "<br> The active lines are: " . $row[0] . "<br>"; // so far this is only printing out the first line name
        }
    }

    function handleProjectTableNamesRequest()
    {
        global $db_conn;
        $success = False;
        $count = 1;

        $result = executePlainSQL("SELECT table_name FROM user_tables");

        echo "Names of tables in this database: " . "<br>";
        while ($row = oci_fetch_array($result, OCI_BOTH)) {
            echo "<br>" . $count . ": " . $row[0] . "<br>";
            $count++;
        }
    }

    function handleTableSchemaRequest()
    {
        global $db_conn;
        $success = False;
        $tabName = $_GET['tabName'];

        $result = executePlainSQL("SELECT column_name from ALL_TAB_COLUMNS WHERE table_name='$tabName'");

        echo "The data from this table includes: " . "<br>";
        while ($row = oci_fetch_array($result, OCI_BOTH)) {
            echo "<br>" . $row[0] . "<br>";
        }
    }

    function handleProjectionColumns()
    {
        global $db_conn;
        $success = False;
        $col = $_GET['colName'];
        $colsinArr = explode(",", $col);
        $tabName = $colsinArr[0];

        array_shift($colsinArr);
        $colsReverted = implode(',', $colsinArr);

        $result = executePlainSQL("SELECT $colsReverted FROM $tabName");

        foreach ($colsinArr as $colName) {
            echo $colName . "\t";
        }
        echo "<br>";

        while ($row = oci_fetch_array($result, OCI_ASSOC + OCI_RETURN_NULLS)) {
            foreach ($row as $item) {
                echo $item . " ";
            }
            echo "<br>";
        }
    }

    function handleColumnsToDelete() {
        global $db_conn;

            if(!empty($_POST['deleteFromTuple'])) {
                $tabName = $_POST['deleteFromTuple'];
                
                if($tabName == "CARD") {
                    echo "<br>" . "You have selected to delete your Card from your account. Please confirm your cardID.";
                } elseif($tabName == "USERACCOUNT") {
                    echo "<br>" . "You have selected to delete your transit account. Please confirm your username.";
                }

            } else {
                echo "Please select the table you wish to delete from.";
            }  
    }

    function handleTupleDeletion() {
        global $db_conn;
        $idData = $_GET['idData'];
        
        if(!empty($_POST['deleteFromTuple'])) {
            $tabName = $_POST['deleteFromTuple'];

        if($tabName == 'USERACCOUNT') {
            $sqlRemove = "DELETE FROM UserAccount WHERE username='$idData'";
            $result = executePlainSQL($sqlRemove);               
        } elseif ($tabName == 'CARD') {
            $sqlRemove = "DELETE FROM Card WHERE cardID='$idData'";
            $result = executePlainSQL($sqlRemove);                
        }

        OCICommit($db_conn);
        
        echo "<br>" . "Deletion was successful! We're sorry to see you go.";
        } else {
            echo 'Please select the table you wish to delete from.';
            exit;
        }  
    }

    // For each transit line, find the number of stations that are part of that line
    function handleGroupByQuery()
    {
        $SQL = "SELECT LHS.lineName, COUNT(S.stationID) AS countStations
                FROM Line_Has_Station LHS, Station S
                WHERE LHS.stationID = S.stationID
                GROUP BY LHS.lineName";

        $resultCountSQL = executePlainSQL($SQL);

        if ($resultCountSQL) {
            echo "<table>";
            echo "<tr><th>LineName</th><th>Number of Stations</th></tr>";

            while (($resultCount = oci_fetch_assoc($resultCountSQL)) != false) {
                echo "<tr>";
                echo "<td>" . $resultCount['LINENAME'] . "</td>";
                echo "<td>" . $resultCount['COUNTSTATIONS'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";

            oci_free_statement($resultCountSQL);
        } else {
            echo "No values in database that fit the criteria";
        }
    }

    // For each station, find the total amount of transit lines that pass through them, only for stations that are passed through more than one line
    function handleHavingQuery()
    {
        $SQL = "SELECT LHS.stationID, COUNT(DISTINCT LHS.lineName) AS linesPassedThrough
            FROM Line_Has_Station LHS
            GROUP BY LHS.stationID
            HAVING COUNT(DISTINCT LHS.lineName) > 1";

        $resultSQL = executePlainSQL($SQL);

        if ($resultSQL) {
            echo "<table>";
            echo "<tr><th>StationID</th><th>Number of Lines That Pass Through</th></tr>";

            while (($result = oci_fetch_assoc($resultSQL)) != false) {
                echo "<tr>";
                echo "<td>" . $result['STATIONID'] . "</td>";
                echo "<td>" . $result['LINESPASSEDTHROUGH'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";

            oci_free_statement($resultSQL);
        } else {
            echo "No values in database that fit the criteria";
        }
    }

    function handleNestedAggregation()
    {

        global $db_conn;

        $SQL = "SELECT type, COUNT(transactionID) FROM Pass_Loads_To GROUP BY type HAVING COUNT(transactionID) >= all(SELECT COUNT(p.transactionID) FROM Pass_Loads_To p GROUP BY p.type)";

        $result = executePlainSQL($SQL);

        if ($result) {

            echo "<table>";
            echo "<tr><th>Pass Type</th><th>Number Sold</th></tr>";

            while (($result = oci_fetch_assoc($result)) != false) {
                echo "<tr>";
                echo "<td>" . $result['TYPE'] . "</td>";
                echo "<td>" . $result['COUNT(transactionID)'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";

            oci_free_statement($result);
        } else {
            echo "There are no passes in the database that fit this criteria.";
        }
    }

    // HANDLE ALL POST ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
    function handlePOSTRequest()
    {
        if (connectToDB()) {
            if (array_key_exists('resetTablesRequest', $_POST)) {
                handleResetRequest();
            } else if (array_key_exists('updateQueryRequest', $_POST)) {
                handleUpdateRequest();
            } else if (array_key_exists('insertQueryRequest', $_POST)) {
                handleInsertRequest();
            } else if (array_key_exists('deleteTupleRequest', $_POST) && isset($_POST['deleteSubmit'])) {
                handleColumnsToDelete();
            } else if (array_key_exists('deleteColumnsRequest', $_POST) && isset($_POST['deleteColumnsSubmit'])) {
                handleTupleDeletion();
            } else if (array_key_exists('groupByRequest', $_POST)) {
                handleGroupByQuery();
            } else if (array_key_exists('havingRequest', $_POST)) {
                handleHavingQuery();
            }

            disconnectFromDB();
        }
    }

    // HANDLE ALL GET ROUTES
    // A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
    function handleGETRequest()
    {
        if (connectToDB()) {
            if (array_key_exists('countTuples', $_GET)) {
                handleCountRequest();
            } else if (array_key_exists('viewTableNames', $_GET)) {
                handleProjectTableNamesRequest();
            } else if (array_key_exists('viewTableSchema', $_GET) && isset($_GET['tableSchemaRequest'])) {
                handleTableSchemaRequest();
            } else if (array_key_exists('projectionSubmit', $_GET) && isset($_GET['projectionFinalRequest'])) {
                handleProjectionColumns();
            }

            disconnectFromDB();
        }
    }

    if (
        isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit']) || isset($_POST['deleteSubmit'])
        || isset($_POST['deleteColumnsSubmit']) || isset($_POST['groupBySubmit']) || isset($_POST['havingSubmit'])
    ) {
        handlePOSTRequest();
    } else if (
        isset($_GET['countTupleRequest']) || isset($_GET['projectionQueryRequest']) || isset($_GET['tableSchemaRequest'])
        || isset($_GET['projectionFinalRequest'])
    ) {
        handleGETRequest();
    }
    ?>
</body>

</html>