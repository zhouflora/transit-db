<?php 
    session_start();
    // $_SESSION['tabName'] = 'CARD';
?>
<?php require('../components/head.php') ?>
        <div class="p-4">
            <div class="card">
                <div class="card-header"> Deletion Query </div>
                <div class="card-body">
                    <h5 class="card-title">Remove Card</h5>
                    <p class="card-text">
                        Type a Card ID bellow to be deleted. <br />
                        Notice that the passes loaded to that card will be deleted as well.
                    </p>
                    <form method="POST" action="q_delete.php">
                        <div class="input-group mb-3">
                            <label for="inputUname" class="input-group-text">Card ID</label>
                            <input type="text" class="form-control" aria-describedby="basic-addon1" id="inputCardID" name="inputCardID">
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit_delete">Delete</button>
                        <?php
                            if (isset($_POST['inputCardID']) && array_key_exists('submit_delete', $_POST) && connectToDB()) {
                                
                                // Get user input
                                $idData = $_POST['inputCardID'];
                                
                                // Check if matches cardID
                                $query = "SELECT * FROM Card WHERE cardID = '$idData'";
                                $result = executePlainSQL($query);
                                $row = OCI_Fetch_Array($result, OCI_BOTH);

                                if (!$idData) {
                                    echo "<div class='error p-2'>All fields are required. Please try again.</div>";
                                } else if (!$row || !$row[0]) {
                                    echo "<div class='error p-2'>The card ID you entered is wrong.</div>";
                                } else {
                                    executePlainSQL("DELETE FROM Card WHERE cardID='$idData'");
                                    executePlainSQL("DELETE FROM Card WHERE cardID='$idData'");
                                    OCICommit($db_conn);
                                }

                                disconnectFromDB();
                            }
                        ?>
                    </form>
                </div>
            </div>
            <br />
            <div class="card">
                <h5 class="card-header">Cards</h5> 
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Card ID</th>
                            <th scope="col">Stored Value</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if (connectToDB()) {

                            // Query the database
                            $query = "SELECT * FROM card";
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
            <br />
            <div class="card">
                <h5 class="card-header">Passes</h5> 
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Pass Type</th>
                            <th scope="col">Card ID</th>
                            <th scope="col">Purchase Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if (connectToDB()) {

                            // Query the database
                            $query = "SELECT type, cardID, purchaseDate FROM Pass_Loads_To";
                            $result = executePlainSQL($query);

                            // Fill out the table
                            $count = 0;
                            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                                $count++;
                                $type = 'Unknown';
                                switch ($row[0]) {
                                    case 'M':
                                        $type = 'Monthly';
                                        break;
                                    case 'D':
                                        $type = 'Daily';
                                        break;
                                }
                                echo "
                                    <tr>
                                    <th scope='row'> $count </th>
                                    <td>" . $type . "</td>
                                    <td>" . $row[1] . "</td>
                                    <td>" . $row[2] . "</td>
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
