<?php require('../components/head.php') ?>
        <div class="p-4">
            <div class="card">
                <div class="card-header">
                    Aggregation Query with GROUP BY
                </div>
                <div class="card-body">
                    <h5 class="card-title">Stations in Line</h5>
                    <p class="card-text">
                        For each transit line, this will find the number of stations that are part of that line.
                    </p>
                    <form method="GET" action="q_groupAggregation.php">
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
                            <th scope="col">Line Name</th>
                            <th scope="col">Station Count</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if (array_key_exists('submit', $_GET) && connectToDB()) {

                            // Query the database
                            $query = "SELECT LHS.lineName, COUNT(S.stationID) AS countStations
                                FROM Line_Has_Station LHS, Station S
                                WHERE LHS.stationID = S.stationID
                                GROUP BY LHS.lineName";
                                
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
