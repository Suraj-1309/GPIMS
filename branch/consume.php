<?php
ob_start();

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['branch']) || !isset($_SESSION['lab'])) {
    header("Location: ../index.php");
    exit();
}


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
    <title>Brach Allocated Panel</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="../inventory/style.css">
    <?php include "components/style.php" ?>
</head>



<body>

    <?php include "components/popup.php" ?>

    <?php include "../adminpanel/components/navbar.php" ?>

    <!-- sidebar  -->
    <?php include "components/sidebar.php" ?>


    <div id="admin" class="right">
        <div class="p-3 ml-5 pl-4">
            <h2>Consumable Items Use Record</h2>
        </div>
        <style>
            h2 {
                padding-top: 2%;
                padding-left: 2%;
                margin-bottom: -2%;
            }

            @media (max-width: 767px) {

                h2 {
                    text-align: left;
                    margin-left: -27px;
                    font-size: x-large;
                    font-weight: 900;
                    padding-top: 5%;
                    margin-bottom: -5%;
                    padding-bottom: -5%;
                }
            }
        </style>
        <?php
        include "../_dbconnect.php";
        ?>

        <div class="container mt-4">
            <!-- Table (Example) -->
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Stock Reg Page/Sno.</th>
                        <th scope="col">Received Date</th>
                        <th scope="col">Unit Price</th>
                        <th scope="col">Units</th>
                        <th scope="col">Used By</th>
                        <th scope="col">Used For</th>
                        <th scope="col">Use Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $branch = $_SESSION['branch'];
                    $lab = $_SESSION['lab'];
                    $sql = "SELECT * FROM `consumable_items` WHERE `branch` = '$_SESSION[branch]' AND `lab` = '$_SESSION[lab]' ORDER BY `sno` DESC";
                    $result = mysqli_query($conn, $sql);
                    $sno = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $sno++;
                        echo '
                <tr>
                    <th scope="row">' . $sno . '</th>
                    <td>' . $row['product_name'] . '</td>
                    <td>' . $row['rr_reg'] . '</td>
                    <td>' . $row['allotment_date'] . '</td>
                    <td>' . $row['unit_price'] . '</td>
                    <td>' . $row['units'] . '</td>
                    <td>' . $row['use_name'] . '</td>
                    <td>' . $row['use_for'] . '</td>
                    <td>' . $row['use_date'] . '</td>
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