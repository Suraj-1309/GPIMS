<?php
ob_start();
session_start();

// Check if the user is logged in and branch and lab are set
if (
    !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true ||
    !isset($_SESSION['branch']) || !isset($_SESSION['lab'])
) {

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
    <title>Branch Deprecate List</title>
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
            <h2><?php echo $_SESSION['branch'] . " " . $_SESSION['lab'] ?> Return Items List</h2>
        </div>
        <style>
            h2 {
                padding-top: 2%;
                padding-left: 1%;
                margin-bottom: -1%;
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
        <!-- connect to database  -->
        <?php
        include "../_dbconnect.php";
        ?>


        <!-- cancel button -->
        <?php

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sno'])) {
            // Get the return request ID from POST
            $sno = $_POST['sno'];

            // Retrieve the record from the return_request table
            $query = "SELECT * FROM return_request WHERE sno = '$sno'";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);

                // Extract details from the return_request record
                $product_name = $row['product_name'];
                $type = $row['type'];
                $rr_reg = $row['rr_reg'];
                $purchase_date = $row['purchase_date'];
                $got_it_from = $row['got_it_from'];
                $unit_price = $row['unit_price'];
                $returned_units = (int) $row['units'];
                $branch = $row['branch'];
                $lab = $row['lab'];

                // Calculate the overall price for the returned units
                $overall_price = $unit_price * $returned_units;

                // Check if an identical record exists in branch_items,
                // matching on product_name, type, rr_reg, branch, and lab.
                $check_sql = "SELECT * FROM branch_items 
                              WHERE product_name = '$product_name' 
                                AND `type` = '$type' 
                                AND rr_reg = '$rr_reg'
                                AND branch = '$branch'
                                AND lab = '$lab'";
                $check_result = mysqli_query($conn, $check_sql);

                if ($check_result && mysqli_num_rows($check_result) > 0) {
                    // Found an existing record in branch_items: update its units and overall_price
                    $existing = mysqli_fetch_assoc($check_result);
                    $new_units = (int) $existing['units'] + $returned_units;

                    $update_sql = "UPDATE branch_items 
                                   SET units = '$new_units' 
                                   WHERE sno = '" . $existing['sno'] . "'";
                    mysqli_query($conn, $update_sql);
                } else {
                    // No matching record found in branch_items: insert a new record.
                    // For the new record, set allotment_date to today's date.
                    $allotment_date = date("Y-m-d");
                    $insert_sql = "INSERT INTO branch_items 
                                   (product_name, `type`, rr_reg, allotment_date, branch, lab, unit_price, units, purchase_date, got_it_from) 
                                   VALUES 
                                   ('$product_name', '$type', '$rr_reg', '$allotment_date', '$branch', '$lab', '$unit_price', '$returned_units', '$purchase_date', '$got_it_from')";
                    mysqli_query($conn, $insert_sql);
                }

                // Delete the record from the return_request table
                $delete_sql = "DELETE FROM return_request WHERE sno = '$sno'";
                $delete_result = mysqli_query($conn, $delete_sql);

                if ($delete_result) {
                    $_SESSION['popup_message'] = 'Item successfully cancelled and returned to branch inventory.';
                    $_SESSION['popup_type'] = 'success';
                } else {
                    $_SESSION['popup_message'] = 'Error in returning item.';
                    $_SESSION['popup_type'] = 'danger';
                }
            } else {
                $_SESSION['popup_message'] = 'Item not found.';
                $_SESSION['popup_type'] = 'danger';
            }

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
        ?>



        <!-- Add item to branch Form -->



        <div class="container">
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Type</th>
                        <th scope="col">Stock Reg Page/Sno</th>
                        <th scope="col">Return Request Date</th>
                        <th scope="col">Units</th>
                        <th scope="col">Reason</th>
                        <th scope="col">Permission</th>
                        <th scope="col">Action</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM `return_request` WHERE `branch` = '$_SESSION[branch]' AND `lab` = '$_SESSION[lab]' ORDER BY `sno` DESC";
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
                                <td>' . $row['return_date'] . '</td>

                                <td>' . $row['units'] . '</td>
                                <td>' . $row['reason'] . '</td>
                                <td>' . $row['permission'] . '</td>
                                <td>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <button class="cancel btn btn-sm btn-danger" id="' . $row['sno'] . '">Cancel</button>
                                    </div>
                                </td>
                            </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- cenal Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!--  Head -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Cancel Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">
                        Are you sure you want to Cancel Return Request?
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <form id="deleteForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                            <!-- Hidden field to store the sno -->
                            <input type="hidden" name="sno" id="deleteSno">

                            <!-- Buttons-->
                            <input type="hidden" name="action" value="delete">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                            <button id="confirmDeleteBtn" type="submit" class="btn btn-danger">Yes, Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

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


    <!-- JavaScript to handle the delete action -->
    <script>
        $(document).ready(function () {
            $('.cancel').on('click', function () {
                var sno = $(this).attr('id');
                $('#deleteSno').val(sno);
                $('#deleteModal').modal('show');
            });
        });

    </script>
</body>

</html>

<?php
ob_end_flush();
?>