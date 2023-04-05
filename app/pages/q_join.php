<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>TransitDB</title>
        <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" href="../css/custom_styles.css" />
        <script src="../bootstrap/js/bootstrap.min.js" ></script>
    </head>
    <body>
        <?php include('../components/navbar.php');?>
        <?php include('connection.php');?>
        <div class="p-4">
            <div class="card">
                <div class="card-header">
                    Join Query
                </div>
                <div class="card-body">
                    <h5 class="card-title">Drivers of a transit line</h5>
                    <p class="card-text">
                        Select a transit line to see the name and email of the drivers operating in that line.
                    </p>
                    <form method="GET" action="q_join.php">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Line</label>
                            <select class="form-select" name="selection">
                                <option selected>Select...</option>
                                <?php
                                    connectToDB();

                                    // Get line names
                                    $query = "SELECT linename FROM line";
                                    $result = executePlainSQL($query);
                                
                                    // Generate the options
                                    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                                        echo '<option value="' . $row[0] . '">' . $row[0] . '</option>';
                                    }
                                
                                    disconnectFromDB();
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                        
                    </form>
                </div>
            </div>
            <br />
            <div class="card">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Driver Name</th>
                            <th scope="col">Driver Email</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if (isset($_GET['selection']) && connectToDB()) {
                            $selected_line = $_GET['selection'];


                            // Query the database
                            $query = "SELECT EMPLOYEE.NAME, EMPLOYEE.EMAIL
                                FROM OPERATES_IN, DRIVES, DRIVER, EMPLOYEE
                                WHERE OPERATES_IN.LINENAME = '$selected_line'
                                AND OPERATES_IN.VEHICLEID = DRIVES.VEHICLEID
                                AND OPERATES_IN.LINENAME = DRIVES.LINENAME
                                AND DRIVES.EMPLOYEEID = DRIVER.EMPLOYEEID
                                AND DRIVER.EMPLOYEEID = EMPLOYEE.EMPLOYEEID";
                            $result = executePlainSQL($query);

                            $count = 0;
                            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                                $count++;
                                echo "
                                    <tr>
                                    <th scope='row'> $count </th>
                                    <td>" . $row["NAME"] . "</td>
                                    <td>" . $row["EMAIL"] . "</td>
                                    </tr>";
                            }
                            if ($count == 0) {
                                echo "<tr> <td>No results</td><td></td><td></td> </tr>";
                            }
                            
                            disconnectFromDB();
                        }
                        
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>
