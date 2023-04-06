<?php require('../components/head.php') ?>
        <div class="p-4">
            <div class="card">
                <div class="card-header">
                    Insert Query
                </div>
                <div class="card-body">
                    <h5 class="card-title">Register new user</h5>
                    <p class="card-text">
                        Enter username and password for the new user.
                    </p>
                    <form method="POST" action="q_insert.php">
                        <div class="input-group mb-3">
                            <label for="inputUname" class="input-group-text">Username</label>
                            <input type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1" id="inputUname" name="username">
                        </div>
                        <div class="input-group mb-3">
                            <label for="inputPassword" class="input-group-text">Password</label>
                            <input type="password" class="form-control" id="inputPassword" name="password">
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">Register</button>
                        <?php
                            if (isset($_POST['username']) && isset($_POST['password'])
                                && array_key_exists('submit', $_POST) && connectToDB()) {

                                // Get user inputs
                                $userAccountInfo = array (
                                    ":bind1" => $_POST['username'],
                                    ":bind2" => $_POST['password']
                                );
                    
                                $userAccountRegister = array (
                                    ":bind1" => $_POST['username'],
                                    ":bind3" => 'A8H6G2H837',
                                    ":bind5" => 1234567890 // need to generate
                                );

                                $alltuples1 = array (
                                    $userAccountInfo
                                );
                    
                                $alltuples2 = array (
                                    $userAccountRegister
                                );
                                
                                // Perform Insertion queries
                                executeBoundSQL("insert into UserAccount values (:bind1, :bind2)", $alltuples1);
                                executeBoundSQL("insert into UserAccount_Registers values (:bind1, :bind3, SYSDATE, :bind5)", $alltuples2);
                    
                                OCICommit($db_conn);
                                disconnectFromDB();
                            }
                        ?>
                    </form>
                </div>
            </div>
            <br />
            <div class="card">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Username</th>
                            <th scope="col">Password</th>
                            <th scope="col">Registration Date</th>
                            <th scope="col">Center ID</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if (connectToDB()) {

                            // Query the database
                            $query = "SELECT a.username, a.password, r.registrationdate, r.centreid
                                FROM useraccount a, useraccount_registers r
                                WHERE a.username = r.username";
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
                                    <td>" . $row[2] . "</td>
                                    <td>" . $row[3] . "</td>
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
