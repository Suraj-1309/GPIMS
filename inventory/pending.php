<?php
ob_start();
session_start();

// First, make sure the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit();
}
if (
    !(
        (isset($_SESSION['branch'], $_SESSION['lab']) && $_SESSION['branch'] === 'INVENTORY' && $_SESSION['lab'] === 'INVENTORY')
        ||
        (isset($_SESSION['name']) && !empty($_SESSION['name']))
    )
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

    <?php include "components/popup.php" ?>


    <?php include "../adminpanel/components/navbar.php" ?>


    <!-- sidebar  -->
    <?php include "components/sidebar.php" ?>

    <style>
        h2 {
            padding-left: 2%;
            padding-top: 2%;
            font-weight: 500;
            margin-bottom: -1%;
        }

        @media (max-width: 767px) {
            h2 {
                text-align: left;
                margin-left: -27px;
                font-size: x-large;
                font-weight: 900;
                padding-top: 5%;
            }
        }
    </style>
    <div id="admin" class="right">

        <div class="p-3 ml-5 pl-4">
            <h2>Allocated Items to Branches</h2>
        </div>

        <?php

        include "../_dbconnect.php";

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sno'])) {
            $sno = mysqli_real_escape_string($conn, $_POST['sno']);

            // Retrieve the allotment record using the new table column names
            $query = "SELECT * FROM allotment WHERE sno = '$sno'";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);

                // Use new column names: type (instead of model_name), unit_price (instead of price_per_unit)
                $product_name = $row['product_name'];
                $type = $row['type'];
                $unit_price = $row['unit_price'];
                $returned_units = (int) $row['units'];
                $rr_reg = $row['rr_reg'] ?? '';
                $purchase_date = $row['purchase_date'] ?? null;
                $got_it_from = $row['got_it_from'] ?? '';
                $srno = $row['srno'] ?? 0;
                $bill_date = $row['bill_date'] ?? null;
                // Calculate overall_price as unit_price * returned_units
                $overall_price = $unit_price * $returned_units;

                // Check if an identical item exists in inventory_items
                // Note: Using new column names: type and unit_price.
                $check_sql = "SELECT * FROM inventory_items WHERE product_name = '$product_name' AND type = '$type'AND unit_price = '$unit_price' AND rr_reg = '$rr_reg'";
                $check_result = mysqli_query($conn, $check_sql);

                if ($check_result && mysqli_num_rows($check_result) > 0) {
                    // Found an existing record, so update the units and overall_price
                    $existing = mysqli_fetch_assoc($check_result);
                    $new_units = (int) $existing['units'] + $returned_units;
                    $new_overall_price = $unit_price * $new_units;

                    $update_sql = "UPDATE inventory_items SET units = '$new_units', overall_price = '$new_overall_price'  WHERE sno = '" . $existing['sno'] . "'";
                    mysqli_query($conn, $update_sql);

                } else {
                    // Insert a new record into inventory_items using the new column names
                    $insert_sql = "INSERT INTO inventory_items 
                                        (product_name, `type`, rr_reg, purchase_date, got_it_from, srno, bill_date, unit_price, overall_price, units)
                                    VALUES 
                                    ('$product_name', '$type', '$rr_reg', '$purchase_date', '$got_it_from', '$srno', '$bill_date', '$unit_price', '$overall_price', '$returned_units')";
                    mysqli_query($conn, $insert_sql);
                }

                // Delete the record from the allotment table
                $delete_sql = "DELETE FROM allotment WHERE sno = '$sno'";
                $delete_result = mysqli_query($conn, $delete_sql);

                if ($delete_result) {
                    $_SESSION['popup_message'] = 'Item successfully returned to inventory.';
                    $_SESSION['popup_type'] = 'success';
                } else {
                    $_SESSION['popup_message'] = 'Error in returning item.';
                    $_SESSION['popup_type'] = 'danger';
                }
            } else {
                $_SESSION['popup_message'] = 'Item not found.';
                $_SESSION['popup_type'] = 'danger';
            }
            header("Location: pending.php");
            exit();
        }

        ?>



        <div class="container mt-4">
            <!-- Table (Example) -->
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Type/Register details</th>
                        <th scope="col">Reg Page/S.NO</th>
                        <th scope="col">Price</th>
                        <th scope="col">Units</th>
                        <th scope="col">Branch</th>
                        <th scope="col">Lab</th>
                        <th scope="col">Permission</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM `allotment` ORDER BY `purchase_date` DESC";
                    $result = mysqli_query($conn, $sql);
                    $sno = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $sno++;
                        echo '
                                <tr>
                                    <th scope="row">' . $sno . '</th>
                                    <td>' . $row['product_name'] . '</td>
                                    <td>' . $row['type'] . '</td>
                                    <td>' . $row['rr_reg'] . '</td>
                                    <td>' . $row['unit_price'] . '</td>
                                    <td>' . $row['units'] . '</td>
                                    <td>' . $row['branch'] . '</td>
                                    <td>' . $row['lab'] . '</td>
                                    <td>' . $row['permission'] . '</td>
                                    <td>
                                        <div style="display: flex; gap: 10px; align-items: center;">
                                            <button class="btn btn-danger reject-button" data-sno="' . $row['sno'] . '" data-toggle="modal" data-target="#rejectModal">Reject</button>
                                        </div>
                                    </td>

                                </tr>';
                    }

                    ?>

                </tbody>
            </table>

            <!-- Modal -->
            <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectModalLabel">Reject Confirmation</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to reject this Request?
                        </div>
                        <div class="modal-footer">
                            <form id="rejectForm" method="POST" action="pending.php">
                                <input type="hidden" name="sno" id="hiddenRejectSno">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Confirm Reject</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.querySelectorAll('.reject-button').forEach(button => {
            button.addEventListener('click', function () {
                selectedRejectSno = this.getAttribute('data-sno');
                document.getElementById('hiddenRejectSno').value = selectedRejectSno;
            });
        });


    </script>

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