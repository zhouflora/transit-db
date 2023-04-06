<?php require('../components/head.php') ?>
        <div class="p-4">
            <div class="card">
                <div class="card-header">
                    Selection Query
                </div>
                <div class="card-body">
                    <h5 class="card-title">Super-active lines!</h5>
                    <p class="card-text">
                        Filter the transit lines by their availability over weekends or holidays.
                    </p>
                    <form method="GET" action="q_selection.php">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value=1 id="weekend" name="weekend">
                            <label class="form-check-label" for="weekend">
                                Active on Weekends
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value=1 id="holiday" name="holiday">
                            <label class="form-check-label" for="holiday">
                                Active on Holidays
                            </label>
                        </div>
                        <br/>
                        <button type="submit" class="btn btn-primary" name="submit">Filter</button>
                    </form>
                </div>
            </div>
            <br />
            <div class="card">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Line name</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if (connectToDB()) {

                            $weekends = $_GET['weekend'];
                            $holidays = $_GET['holiday'];

                            // Query the database
                            $query = "SELECT UNIQUE lineName
                                FROM StationLine_Scheduled_For_Timing"
                                . (($weekends || $holidays)? " WHERE" : "")
                                . ($weekends ? " activeOnWeekends = 1" : "")
                                . (($weekends && $holidays)? " AND" : "")
                                . ($holidays ? " activeOnHolidays = 1" : "");

                            $result = executePlainSQL($query);

                            // Fill out the table
                            $count = 0;
                            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                                $count++;
                                echo "
                                    <tr>
                                    <th scope='row'> $count </th>
                                    <td>" . $row[0] . "</td>
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
