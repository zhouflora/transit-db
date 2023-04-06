<?php 
    session_start();
    // $_SESSION['tabName'] = 'CARD';
?>
<?php require('../components/head.php') ?>
        <div class="p-4">
            <div class="card">
                <div class="card-header"> Projection Query </div>
                <div class="card-body">
                    <h5 class="card-title">See everything!</h5>
                    <p class="card-text">
                        Select the table and a column within that table to display the content.
                    </p>
                    <form method="GET" action="q_projection.php">
                        <div class="mb-3 input-group">
                            <label for="table_selection" class="input-group-text">Table</label>
                            <select class="form-select" id="table_selection" name="table_selection">
                                <option>Select...</option>
                                <?php
                                    if (connectToDB()) {
                
                                        // Get all table names
                                        $query = "SELECT table_name FROM user_tables";
                                        $result = executePlainSQL($query);
                                    
                                        // Generate the options
                                        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                                            echo '<option value="' . $row[0] . '">' . $row[0] . '</option>';
                                        }
                                    
                                        disconnectFromDB();
                                    }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit_table">Get columns</button>
                        
                        <p></p>
                        <p class="card-text">
                            Hold Ctrl to select multiple columns.
                        </p>
                        <div class="mb-3 input-group">
                            <label for="column_selection" class="input-group-text">Column</label>
                            <select class="form-select" id="column_selection" name="column_selection[]" multiple>
                                <?php
                                    // session_start();
                                    if (isset($_GET['table_selection']) && connectToDB()) {
                                        $tabName = $_GET['table_selection'];
                                        if ($tabName != "Select...") {
                                            $_SESSION['tabName'] = $tabName;
                                        }
                                        
                                        // Get column names
                                        $query = "SELECT column_name from USER_TAB_COLUMNS WHERE table_name='$tabName'";
                                        $result = executePlainSQL($query);
                                    
                                        // Generate the options
                                        while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                                            echo '<option value="' . $row[0] . '">' . $row[0] . '</option>';
                                        }
                                    
                                        disconnectFromDB();
                                    }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit_column">Get data</button>
                    </form>
                </div>
            </div>
            <br />
            <div class="card">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <?php
                                if (isset($_GET['column_selection'])) {
                                    $cols = $_GET['column_selection'];
                                    for ($i = 0; $i < count($cols); $i++) {
                                        echo "<th scope='col'>" . $cols[$i] . "</th>";
                                    }
                                }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if (isset($_GET['column_selection']) 
                            && array_key_exists('submit_column', $_GET) && connectToDB()) {
                            
                            $tabName = $_SESSION['tabName'];
                            $colsArray = $_GET['column_selection'];
                            $colsReverted = implode(',', $colsArray);

                            // Query the database
                            $query = "SELECT $colsReverted FROM $tabName";
                            $result = executePlainSQL($query);

                            // Fill out the table
                            $count = 0;
                            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                                $count++;
                                $cells = "<tr> <th scope='row'> $count </th>";
                                for ($i = 0; $i < count($colsArray); $i++) {
                                    $cells .= ("<td>" . $row[$i] . "</td>");
                                }
                                $cells .= "</tr>";

                                echo $cells;
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
