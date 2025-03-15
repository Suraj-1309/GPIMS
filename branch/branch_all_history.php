<?php
ob_start();

session_start();

// Check if the user is logged in and branch and lab are set
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['branch']) || !isset($_SESSION['lab'])) {
    // Redirect to login page if not logged in
    header("Location: ../index.php");
    exit();
}

// Prevent access for INVENTORY branch and lab
if ($_SESSION['branch'] == 'INVENTORY' && $_SESSION['lab'] == 'INVENTORY') {
    $_SESSION['popup_message'] = "Access Denied: You cannot access this page.";
    $_SESSION['popup_type'] = "danger";
    header("Location: ../index.php");
    exit();
}

$branch = $_SESSION['branch'];
$lab = $_SESSION['lab'];
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch All History</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="../inventory/style.css">
</head>



<body>


    <?php include "../navbar.php" ?>


    <!-- sidebar  -->
    <div class="sidebar">
        <div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="index.php"><?= $_SESSION['branch'] ?> Inventory</a>
                </li>
                <li class="nav-item">
                    <a href="branch_allotment.php">Alloted Items</a>
                </li>

                <li class="nav-item">
                    <a href="branch_all_history.php">Branch Complete History</a>
                </li>

                <li class="nav-item">
                    <a href="branch_return.php">Returned Items</a>
                </li>

                <li class="nav-item">
                    <a href="branch_return_reject.php">Return Rejected</a>
                </li>

                <li class="nav-item">
                    <a href="branch_deprecate.php">Deprecated Items</a>
                </li>

                <li>

                    <?php
                    echo "$_SESSION[branch] . $_SESSION[lab]";
                    ?>

                </li>
            </ul>

        </div>


        <!-- links from bottom -->
        <div class="bottom-links">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="#account">Your Account</a>
                </li>

                <form action="../logout.php" method="get">
                    <input type="submit" value="Logout">
                </form>
            </ul>
        </div>
    </div>


    <div id="admin" class="right">

        <!-- connect to database  -->
        <?php

        $insert = false;
        $update = false;
        $delete = false;


        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "admin";

        $conn = mysqli_connect($servername, $username, $password, $database);

        ?>


        <div class="container">
            <h2>History of Inventory</h2>
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Model Name</th>
                        <th scope="col">Price Per Unit</th>
                        <th scope="col">Total Units</th>
                        <th scope="col">Time</th>
                        <th scope="col">Record Register</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM `branch_all_history` WHERE `branch_name` = '$_SESSION[branch]' AND `lab_name` = '$_SESSION[lab]' ORDER BY `date` DESC";
                    $result = mysqli_query($conn, $sql);
                    $sno = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $sno++;
                        echo '
                    <tr>
                        <th scope="row">' . $sno . '</th>
                        <td>' . $row['product_name'] . '</td>
                        <td>' . $row['model_name'] . '</td>
                        <td>' . $row['price'] . '</td>
                        <td>' . $row['units'] . '</td>
                        <td>' . $row['date'] . '</td>
                        <td>' . $row['rr_register'] . '</td>
                        <td>' . $row['reason'] . '</td>
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
            $('#myTable').DataTable();

        });
    </script>

</body>

</html>

<?php
ob_end_flush();
?>