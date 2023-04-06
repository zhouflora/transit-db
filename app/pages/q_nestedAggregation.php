<?php require('../components/head.php') ?>
        <div class="p-4">
            <div class="card">
                <div class="card-header">
                    Nested Aggregation Query with GROUP BY
                </div>
                <div class="card-body">
                    <h5 class="card-title">Most Popular Pass</h5>
                    <p class="card-text">
                    This will find the most popular type of pass purchased by users.
                    </p>
                    <form method="GET" action="q_nestedAggregation.php">
                        <button type="submit" class="btn btn-primary" name="submit">Find</button>
                    </form>
                </div>
            </div>
            <br />
            <div class="card">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Most Popular Pass Type</th>
                            <th scope="col">Number Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if (array_key_exists('submit', $_GET) && connectToDB()) {

                            // Query the database
                            $query = "SELECT type, COUNT(transactionID)
                                FROM Pass_Loads_To
                                GROUP BY type
                                HAVING COUNT(transactionID) >= all(
                                    SELECT COUNT(p.transactionID)
                                    FROM Pass_Loads_To p
                                    GROUP BY p.type)";
                                
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
                                    <td>" . $type . "</td>
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
