<?php
ob_start();
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit();
}
if (
    !isset($_SESSION['branch']) || !isset($_SESSION['lab']) ||
    $_SESSION['branch'] !== 'INVENTORY_OFFICER' || $_SESSION['lab'] !== 'INVENTORY_OFFICER'
) {
    // Optionally, you could display an error message or log the attempt
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Officer</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<?php include "components/style.php" ?>


<body>

    <?php include "components/popup.php" ?>

    <?php include "../adminpanel/components/navbar.php" ?>

    <!-- sidebar  -->
    <?php include "components/sidebar.php" ?>


    <style>
        @media (max-width: 767px) {
            
            h2{ 
                text-align: left;
                margin-left: -27px;
                font-size: x-large;
                font-weight: 900;
                padding-top: 5%;
                padding-bottom: -2%;
                margin-bottom: -4%;
            }
        }
    </style>
    <div id="admin" class="right">
        <div class="p-3 ml-5 pl-4">
            <h2 class="pr-0 py-4">Available Stock Of College</h2>
        </div>

        <!-- logic to add new admin -->
        <?php
        include "../_dbconnect.php";
        ?>

        <div class="container">
            <table class="table table-bordered" id="myTable">

                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Type/Register details</th>
                        <th scope="col">Reg Page/S.NO</th>
                        <th scope="col">Purchase Date</th>
                        <th scope="col">Got it From</th>
                        <th scope="col">SRNO / Bill date</th>
                        <th scope="col">Unit Price</th>
                        <th scope="col">Units</th>
                        <th scope="col">Total Price</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $sql = "SELECT * FROM `inventory_items` ORDER BY `sno` DESC";
                    $result = mysqli_query($conn, $sql);
                    $sno = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $sno++;

                        // Format purchase_date and bill_date to dd-mm-yyyy
                        $purchase_date_formatted = !empty($row['purchase_date']) ? date('d-m-Y', strtotime($row['purchase_date'])) : '';
                        $bill_date_formatted = !empty($row['bill_date']) ? date('d-m-Y', strtotime($row['bill_date'])) : '';

                        echo '<tr>
                                <th scope="row">' . $sno . '</th>
                                <td>' . htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . htmlspecialchars($row['rr_reg'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>' . $purchase_date_formatted . '</td>
                                <td>' . htmlspecialchars($row['got_it_from'], ENT_QUOTES, 'UTF-8') . '</td>
                                <td><small>' . htmlspecialchars($row['srno'], ENT_QUOTES, 'UTF-8') . '<br>' . $bill_date_formatted . '</small></td>

                                <td>' . $row['unit_price'] . '</td>
                                <td>' . $row['units'] . '</td>
                                <td>' . $row['overall_price'] . '</td>
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