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
                    Division Query
                </div>
                <div class="card-body">
                    <h5 class="card-title">Experienced technicians</h5>
                    <p class="card-text">
                        Find the name and salary of the technicians who have serviced
                        all the buses.
                    </p>
                    <form method="GET" action="q_division.php">
                        <div class="mb-3">
                            <!--<label for="exampleInputEmail1" class="form-label">Line</label>
                             <select class="form-select" name="selection">
                                <option selected>Select...</option>
                                <?php
                                    // connectToDB();

                                    // // Get line names
                                    // $query = "SELECT linename FROM line";
                                    // $result = executePlainSQL($query);
                                
                                    // // Generate the options
                                    // while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                                    //     echo '<option value="' . $row[0] . '">' . $row[0] . '</option>';
                                    // }
                                
                                    // disconnectFromDB();
                                ?>
                            </select> -->
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit" >Find</button>
                        
                    </form>
                </div>
            </div>
            <br />
            <div class="card">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Technician Name</th>
                            <th scope="col">Technician Salary</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if (array_key_exists('submit', $_GET) && connectToDB()) {
                            // $selected_line = $_GET['selection'];

                            // Query the database
                            $query = "SELECT e.name, e.salary
                                FROM employee e, technician t
                                WHERE e.employeeID = t.employeeID
                                AND NOT EXISTS
                                (SELECT b.vehicleID
                                FROM bus b
                                MINUS 
                                (SELECT s.vehicleID
                                FROM services s
                                WHERE s.employeeID = t.employeeID))";
                                
                            $result = executePlainSQL($query);

                            // Fill out the table
                            $count = 0;
                            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                                $count++;
                                echo "
                                    <tr>
                                    <th scope='row'> $count </th>
                                    <td>" . $row[0] . "</td>
                                    <td>" . $row[1] . "</td>
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
