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
    <?php include "components/style.php" ?>
</head>

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

        <div class="p-3 ml-5 pl-4">
            <h2>Rejected Allotment Requests</h2>
        </div>
        <style>
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
        <?php
        include "../_dbconnect.php";

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sno']) && isset($_POST['action'])) {
            $sno = mysqli_real_escape_string($conn, $_POST['sno']);
            $action = mysqli_real_escape_string($conn, $_POST['action']);

            // Retrieve the record from allotment_reject (since that's where the items are stored)
            $query = "SELECT * FROM allotment_reject WHERE sno = '$sno'";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);

                if ($action === 'accept') {
                    // Accept: Insert the record into the allotment table with 'Officer' permission
                    $insert_sql = "INSERT INTO allotment 
                (product_name, `type`, rr_reg, purchase_date, got_it_from, srno, bill_date, unit_price, overall_price, units, branch, lab, permission) 
                VALUES 
                ('" . $row['product_name'] . "','" . $row['type'] . "','" . $row['rr_reg'] . "','" . $row['purchase_date'] . "',
                 '" . $row['got_it_from'] . "','" . $row['srno'] . "','" . $row['bill_date'] . "','" . $row['unit_price'] . "',
                 '" . $row['overall_price'] . "','" . $row['units'] . "','" . $row['branch'] . "','" . $row['lab'] . "','Officer')";

                    if (mysqli_query($conn, $insert_sql)) {
                        // If insertion is successful, delete the record from allotment_reject
                        $delete_sql = "DELETE FROM allotment_reject WHERE sno = '$sno'";
                        if (mysqli_query($conn, $delete_sql)) {
                            $_SESSION['popup_message'] = 'Item successfully moved to allotment with Officer permission.';
                            $_SESSION['popup_type'] = 'success';
                        } else {
                            $_SESSION['popup_message'] = 'Item moved, but error deleting from allotment_reject.';
                            $_SESSION['popup_type'] = 'warning';
                        }
                    } else {
                        $_SESSION['popup_message'] = 'Error moving item to allotment.';
                        $_SESSION['popup_type'] = 'danger';
                    }
                } elseif ($action === 'reject') {
                    // Reject: Move item to inventory and remove from allotment_reject
                    $product_name = $row['product_name'];
                    $type = $row['type'];
                    $unit_price = $row['unit_price'];
                    $returned_units = (int) $row['units'];
                    $rr_reg = $row['rr_reg'] ?? '';
                    $purchase_date = $row['purchase_date'] ?? null;
                    $got_it_from = $row['got_it_from'] ?? '';
                    $srno = $row['srno'] ?? 0;
                    $bill_date = $row['bill_date'] ?? null;
                    $overall_price = $unit_price * $returned_units;

                    // Check if item exists in inventory
                    $check_sql = "SELECT * FROM inventory_items WHERE product_name = '$product_name' AND `type` = '$type' AND unit_price = '$unit_price' AND rr_reg = '$rr_reg'";
                    $check_result = mysqli_query($conn, $check_sql);

                    if ($check_result && mysqli_num_rows($check_result) > 0) {
                        // Item exists, update quantity
                        $existing = mysqli_fetch_assoc($check_result);
                        $new_units = (int) $existing['units'] + $returned_units;
                        $new_overall_price = $unit_price * $new_units;
                        $update_sql = "UPDATE inventory_items SET units = '$new_units', overall_price = '$new_overall_price' WHERE sno = '" . $existing['sno'] . "'";
                        mysqli_query($conn, $update_sql);
                    } else {
                        // Insert new record into inventory
                        $insert_sql = "INSERT INTO inventory_items 
                        (product_name, `type`, rr_reg, purchase_date, got_it_from, srno, bill_date, unit_price, overall_price, units)
                    VALUES 
                        ('$product_name', '$type', '$rr_reg', '$purchase_date', '$got_it_from', '$srno', '$bill_date', '$unit_price', '$overall_price', '$returned_units')";
                        mysqli_query($conn, $insert_sql);
                    }

                    // Delete the record from allotment_reject after processing
                    $delete_sql = "DELETE FROM allotment_reject WHERE sno = '$sno'";
                    if (mysqli_query($conn, $delete_sql)) {
                        $_SESSION['popup_message'] = 'Item rejected and moved to inventory.';
                        $_SESSION['popup_type'] = 'success';
                    } else {
                        $_SESSION['popup_message'] = 'Error removing item from allotment.';
                        $_SESSION['popup_type'] = 'danger';
                    }
                }
            } else {
                $_SESSION['popup_message'] = 'Item not found in allotment_reject.';
                $_SESSION['popup_type'] = 'danger';
            }

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
        ?>


        <div class="container">

            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Type/Register details</th>
                        <th scope="col">Reg Page/S.NO</th>
                        <th scope="col">Units</th>
                        <th scope="col">Branch</th>
                        <th scope="col">Lab</th>
                        <th scope="col">Rejected By</th>
                        <th scope="col">Reason</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $sql = "SELECT * FROM `allotment_reject` ORDER BY `sno` DESC";
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
                                <td>' . $row['units'] . '</td>
                                <td>' . $row['branch'] . '</td>
                                <td>' . $row['lab'] . '</td>
                                <td>' . $row['rejected_by'] . '</td>
                                <td>' . $row['reason'] . '</td>
                                <td>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                    <button class="btn btn-success accept-button" data-sno="' . $row['sno'] . '" data-toggle="modal" data-target="#acceptModal">Request Again</button>
                                    <button class="btn btn-danger reject-button" data-sno="' . $row['sno'] . '" data-toggle="modal" data-target="#rejectModal">Add Back</button>
                                </div>
                                </td>
                            </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Accept Modal -->
        <div class="modal fade" id="acceptModal" tabindex="-1" role="dialog" aria-labelledby="acceptModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="acceptModalLabel">Accept Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to accept this item?
                    </div>
                    <div class="modal-footer">
                        <form id="acceptForm" method="POST" action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                            <input type="hidden" name="sno" id="hiddenAcceptSno">
                            <!-- Hidden action field to differentiate accept -->
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" name="action" value="accept" class="btn btn-success">Confirm
                                Accept</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
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
                        <p>Are you sure you want to reject it?</p>
                        <form id="rejectForm" method="POST" action="<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                            <input type="hidden" name="sno" id="hiddenRejectSno">
                            <!-- Removed textarea for rejection reason -->
                            <div class="modal-footer p-0">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger">Confirm
                                    Reject</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <script>
        document.querySelectorAll('.accept-button').forEach(button => {
            button.addEventListener('click', function () {
                let selectedAcceptSno = this.getAttribute('data-sno');
                document.getElementById('hiddenAcceptSno').value = selectedAcceptSno;
            });
        });
        document.querySelectorAll('.reject-button').forEach(button => {
            button.addEventListener('click', function () {
                let selectedRejectSno = this.getAttribute('data-sno');
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