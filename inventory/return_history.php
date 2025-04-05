<?php

ob_start();
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit();
}
if (
    !isset($_SESSION['branch']) || !isset($_SESSION['lab']) ||
    $_SESSION['branch'] !== 'INVENTORY' || $_SESSION['lab'] !== 'INVENTORY'
) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Panel</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<?php include "components/style.php" ?>

<body>

    <style>
        thead th td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>

    <?php include "components/popup.php" ?>

    <?php include "../adminpanel/components/navbar.php" ?>
    <!-- sidebar  -->
    <?php include "components/sidebar.php" ?>

    <div id="admin" class="right">

        <!-- connect to database  -->
        <?php
        include "../_dbconnect.php";
        ?>


        <!-- Add item to branch Form -->
        <div class="container my-4">
            <h3>Return Items Record</h3>
        </div>
        <style>
            @media (max-width: 767px) {

                h3 {
                    text-align: left;
                    margin-left: 27px;
                    font-size: x-large;
                    font-weight: 900;
                    padding-top: 5%;
                    padding-bottom: -10%;
                    margin-bottom: -2%;
                }
            }
        </style>


        <div class="container">
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Type</th>
                        <th scope="col">Units</th>
                        <th scope="col">Stock Reg Page/Sno</th>
                        <th scope="col">Return By</th>
                        <th scope="col">Reason</th>
                        <th scope="col">Return Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM `return_items_record` ORDER BY `sno` DESC";
                    $result = mysqli_query($conn, $sql);
                    $sno = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $sno++;
                        echo '
                            <tr>
                                <th scope="row">' . $sno . '</th>
                                <td>' . $row['product_name'] . '</td>
                                <td>' . $row['type'] . '</td>
                                <td>' . $row['units'] . '</td>
                                <td>' . $row['rr_reg'] . '</td>
                                <td>' . $row['branch'] . " " . $row['lab'] . '</td>
                                <td>' . $row['reason'] . '</td>
                                <td>' . $row['return_date'] . '</td>
                            </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>





    <script src="components/loginsuccess.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            var dtOptions = {};
            // Check if the viewport width is 767px or less (mobile)
            if ($(window).width() <= 767) {
                dtOptions.lengthChange = false;
            }

            // Initialize DataTable with the options
            $('#myTable').DataTable(dtOptions);
        });
    </script>
</body>

</html>



<?php
ob_end_flush();
?>