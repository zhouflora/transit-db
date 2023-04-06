<?php require('../components/head.php') ?>
        <div class="p-4">
            <div class="card">
                <div class="card-header">
                    Aggregation Query with HAVING
                </div>
                <div class="card-body">
                    <h5 class="card-title">Busy Stations</h5>
                    <p class="card-text">
                        For each station, find the total number of transit lines that pass through them. <br />
                        Only show those stations that more than one line pass through them.
                    </p>
                    <form method="GET" action="q_havingAggregation.php">
                        <button type="submit" class="btn btn-primary" name="submit">Find</button>
                    </form>
                </div>
            </div>
            <br />
            <div class="card">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Station ID</th>
                            <th scope="col">Lines passing through</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if (array_key_exists('submit', $_GET) && connectToDB()) {

                            // Query the database
                            $query = "SELECT LHS.stationID, COUNT(DISTINCT LHS.lineName) AS linesPassedThrough
                                FROM Line_Has_Station LHS
                                GROUP BY LHS.stationID
                                HAVING COUNT(DISTINCT LHS.lineName) > 1";
                                
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
