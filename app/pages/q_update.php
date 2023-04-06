<?php require('../components/head.php') ?>
        <div class="p-4">
            <div class="card">
                <div class="card-header">
                    Update Query
                </div>
                <div class="card-body">
                    <h5 class="card-title">Change password</h5>
                    <p class="card-text">
                        Enter user credentials and the new password. Username and passwords are case sensitive.
                    </p>
                    <form method="POST" action="q_update.php">
                        <div class="input-group mb-3">
                            <label for="inputUname" class="input-group-text">Username</label>
                            <input type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1" id="inputUname" name="username">
                        </div>
                        <div class="input-group mb-3">
                            <label for="inputPasswordOld" class="input-group-text">Old Password</label>
                            <input type="password" class="form-control" id="inputPasswordOld" name="passwordOld">
                        </div>
                        <div class="input-group mb-3">
                            <label for="inputPasswordNew" class="input-group-text">New Password</label>
                            <input type="password" class="form-control" id="inputPasswordNew" name="passwordNew">
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit">Update</button>
                        <?php
                            if (isset($_POST['username']) && isset($_POST['passwordOld']) && isset($_POST['passwordNew'])
                                && array_key_exists('submit', $_POST) && connectToDB()) {

                                // Get user inputs
                                $name = $_POST['username'];
                                $old_pass = $_POST['passwordOld'];
                                $new_pass = $_POST['passwordNew'];

                                // Check old password
                                $query = "SELECT password
                                    FROM useraccount
                                    WHERE username = '".$name."'";
                                $result = executePlainSQL($query);
                                $row = OCI_Fetch_Array($result, OCI_BOTH);

                                if (!$name || !$old_pass || !$new_pass) {
                                    echo "<div class='error p-2'>All fields are required. Please try again.</div>";
                                } else if (!$row || $row[0] != $old_pass) {
                                    echo "<div class='error p-2'>The original password you entered is wrong.</div>";
                                } else {
                                    // you need the wrap the old name and new name values with single quotations
                                    executePlainSQL("UPDATE UserAccount SET password='" . $new_pass . "' WHERE password='" . $old_pass . "' AND username='" . $name . "'");
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
                            // $selected_line = $_GET['selection'];

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
