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
    <title>Branch panel</title>
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

        <?php
        include "../_dbconnect.php";
        ?>


        <!-- code to handle deprecate system -->
        <?php

        if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['action']) && $_POST['action'] === 'deprecate') {
            // Sanitize input values
            $sno = mysqli_real_escape_string($conn, $_POST['sno']);
            $deprecate_units = (int) $_POST['deprecate_units'];
            $reason = mysqli_real_escape_string($conn, $_POST['reason']);

            // Get the record from branch_items
            $query = "SELECT * FROM return_request_cancel WHERE sno = '$sno'";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $current_units = (int) $row['units'];

                if ($deprecate_units > $current_units) {
                    $_SESSION['popup_message'] = "Error: Cannot deprecate more than available units.";
                    $_SESSION['popup_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                // Insert deprecated item into branch_deprecate
                $insert_sql = "INSERT INTO branch_deprecate 
            (product_name, type, rr_reg, allotment_date, branch, lab, unit_price, units, purchase_date, got_it_from, reason , current_condition) 
            VALUES 
            ('{$row['product_name']}', '{$row['type']}', '{$row['rr_reg']}', '{$row['allotment_date']}', 
            '{$row['branch']}', '{$row['lab']}', '{$row['unit_price']}', '$deprecate_units', '{$row['purchase_date']}', 
            '{$row['got_it_from']}', '$reason' , '{$row['product_condition']}')";
                $insert_result = mysqli_query($conn, $insert_sql);

                if ($insert_result) {
                    if ($deprecate_units == $current_units) {
                        // If all units are deprecated, remove the item from branch_items
                        $delete_sql = "DELETE FROM return_request_cancel WHERE sno = '$sno'";
                        mysqli_query($conn, $delete_sql);
                    } else {
                        // Otherwise, update the remaining units in branch_items
                        $new_units = $current_units - $deprecate_units;
                        $update_sql = "UPDATE return_request_cancel SET units = '$new_units' WHERE sno = '$sno'";
                        mysqli_query($conn, $update_sql);
                    }

                    $_SESSION['popup_message'] = "Item deprecated successfully.";
                    $_SESSION['popup_type'] = "success";
                } else {
                    $_SESSION['popup_message'] = "Error: Failed to insert into deprecate table.";
                    $_SESSION['popup_type'] = "danger";
                }
            } else {
                $_SESSION['popup_message'] = "Error: Item not found.";
                $_SESSION['popup_type'] = "danger";
            }

            // Redirect back
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
        ?>


        <?php
        // Add and Return logic
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            // 1. Add to branch_inventory
            if (isset($_POST['action']) && $_POST['action'] === 'request_again_return' && isset($_POST['sno'])) {
                $sno = $_POST['sno'];

                // Fetch item details from return_request_cancel table, including product_condition
                $query = "SELECT product_name, type, rr_reg, purchase_date, got_it_from, unit_price, units, branch, lab, product_condition 
                          FROM return_request_cancel 
                          WHERE sno = '$sno'";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);

                    // Escape fields for safety
                    $product_name = mysqli_real_escape_string($conn, $row['product_name']);
                    $type = mysqli_real_escape_string($conn, $row['type']);
                    $rr_reg = mysqli_real_escape_string($conn, $row['rr_reg']);
                    $purchase_date = mysqli_real_escape_string($conn, $row['purchase_date']);
                    $got_it_from = mysqli_real_escape_string($conn, $row['got_it_from']);
                    $unit_price = mysqli_real_escape_string($conn, $row['unit_price']);
                    $units = (int) $row['units'];
                    $branch = mysqli_real_escape_string($conn, $row['branch']);
                    $lab = mysqli_real_escape_string($conn, $row['lab']);
                    $current_condition = mysqli_real_escape_string($conn, $row['product_condition']);

                    // Check if a matching record exists in branch_items
                    $sql_check = "SELECT sno, units 
                                  FROM branch_items 
                                  WHERE product_name = '$product_name'
                                    AND type = '$type'
                                    AND rr_reg = '$rr_reg'
                                    AND purchase_date = '$purchase_date'
                                    AND got_it_from = '$got_it_from'
                                    AND unit_price = '$unit_price'
                                    AND branch = '$branch'
                                    AND lab = '$lab'
                                  LIMIT 1";
                    $result_check = mysqli_query($conn, $sql_check);

                    if ($result_check && mysqli_num_rows($result_check) > 0) {
                        // Matching record exists; update the units
                        $existing = mysqli_fetch_assoc($result_check);
                        $existing_units = (int) $existing['units'];
                        $new_units = $existing_units + $units;
                        $sno_existing = $existing['sno'];

                        $sql_update = "UPDATE branch_items 
                                       SET units = '$new_units' 
                                       WHERE sno = '$sno_existing'";
                        $result_update = mysqli_query($conn, $sql_update);

                        if ($result_update) {
                            $_SESSION['popup_message'] = "Units updated in branch items successfully.";
                            $_SESSION['popup_type'] = "success";
                        } else {
                            $_SESSION['popup_message'] = "Error updating branch items.";
                            $_SESSION['popup_type'] = "danger";
                        }
                    } else {
                        // No matching record found; insert a new row into branch_items
                        $sql_insert = "INSERT INTO branch_items 
                            (product_name, type, rr_reg, allotment_date, branch, lab, unit_price, units, purchase_date, got_it_from, current_condition)
                            VALUES 
                            ('$product_name', '$type', '$rr_reg', CURDATE(), '$branch', '$lab', '$unit_price', '$units', '$purchase_date', '$got_it_from', '$current_condition')";
                        $result_insert = mysqli_query($conn, $sql_insert);

                        if ($result_insert) {
                            $_SESSION['popup_message'] = "New branch item added successfully.";
                            $_SESSION['popup_type'] = "success";
                        } else {
                            $_SESSION['popup_message'] = "Error inserting new branch item.";
                            $_SESSION['popup_type'] = "danger";
                        }
                    }

                    // Delete the processed record from return_request_cancel
                    $sql_delete = "DELETE FROM return_request_cancel WHERE sno = '$sno'";
                    mysqli_query($conn, $sql_delete);
                } else {
                    $_SESSION['popup_message'] = "Error: Could not retrieve item details from return_request_cancel.";
                    $_SESSION['popup_type'] = "danger";
                }

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }



            // 2. Transfer Functionality: Move item from return_request_cancel to return_request
elseif (isset($_POST['snoReturn'])) {
    $snoReturn = intval($_POST['snoReturn']);
    // Reason for return is provided by the user in the modal
    $reason = trim($_POST['reasonReturn'] ?? '');
    // Return by the current user (from session)
    $return_by = $_SESSION['name'] ?? '';
    
    // Fetch item details from return_request_cancel table
    $query = "SELECT product_name, type, rr_reg, purchase_date, got_it_from, unit_price, overall_price, units, branch, lab, product_condition 
              FROM return_request_cancel 
              WHERE sno = '$snoReturn'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Escape values for safety
        $product_name     = mysqli_real_escape_string($conn, $row['product_name']);
        $type             = mysqli_real_escape_string($conn, $row['type']);
        $rr_reg           = mysqli_real_escape_string($conn, $row['rr_reg']);
        $purchase_date    = mysqli_real_escape_string($conn, $row['purchase_date']);
        $got_it_from      = mysqli_real_escape_string($conn, $row['got_it_from']);
        $unit_price       = mysqli_real_escape_string($conn, $row['unit_price']);
        $overall_price    = mysqli_real_escape_string($conn, $row['overall_price']);
        $units            = (int)$row['units'];
        $branch           = mysqli_real_escape_string($conn, $row['branch']);
        $lab              = mysqli_real_escape_string($conn, $row['lab']);
        $product_condition= mysqli_real_escape_string($conn, $row['product_condition']);
    } else {
        $_SESSION['popup_message'] = "Error: Could not retrieve item details.";
        $_SESSION['popup_type'] = "danger";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
    // Validate required fields (using values from the table and the form)
    if ($product_name === '' || $branch === '' || $lab === '' || $reason === '') {
        $_SESSION['popup_message'] = "Please fill in all required fields for return.";
        $_SESSION['popup_type'] = "danger";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
    // Set additional fields for return_request
    $permission = "Stock Manager"; // Permission is hard-coded
    // Insert data into return_request table.
    // Fields in return_request: product_name, type, rr_reg, purchase_date, got_it_from, unit_price, overall_price, units, branch, lab, permission, product_condition, return_by, reason, return_date
    $sql_insert_return = "INSERT INTO return_request 
        (product_name, `type`, rr_reg, purchase_date, got_it_from, unit_price, overall_price, units, branch, lab, permission, product_condition, return_by, reason, return_date) 
        VALUES 
        ('$product_name', '$type', '$rr_reg', '$purchase_date', '$got_it_from', '$unit_price', '$overall_price', '$units', '$branch', '$lab', '$permission', '$product_condition', '$return_by', '$reason', CURDATE())";
    $result_insert_return = mysqli_query($conn, $sql_insert_return);
    
    // Delete the processed record from return_request_cancel
    $sql_delete_cancel = "DELETE FROM return_request_cancel WHERE sno = '$snoReturn'";
    $result_delete_cancel = mysqli_query($conn, $sql_delete_cancel);
    
    // Check if both operations were successful
    if ($result_insert_return && $result_delete_cancel) {
        $_SESSION['popup_message'] = "Items transferred successfully!";
        $_SESSION['popup_type'] = "success";
    } else {
        $_SESSION['popup_message'] = "Transfer failed: " . mysqli_error($conn);
        $_SESSION['popup_type'] = "danger";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


        }
        ?>



        <!-- Deprecate Modal -->
        <div class="modal fade" id="deprecateModal" tabindex="-1" role="dialog" aria-labelledby="deprecateModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="deprecateForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deprecateModalLabel">Deprecate Item</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Read-only fields -->
                            <div class="form-group">
                                <label for="productNameDeprecate">Product Name</label>
                                <input type="text" class="form-control" id="productNameDeprecate" readonly>
                            </div>
                            <div class="form-group">
                                <label for="allotmentDateDeprecate">Allotment Date</label>
                                <input type="text" class="form-control" id="allotmentDateDeprecate" readonly>
                            </div>
                            <div class="form-group">
                                <label for="currentUnitsDeprecate">Current Units</label>
                                <input type="text" class="form-control" id="currentUnitsDeprecate" readonly>
                            </div>
                            <!-- Input fields -->
                            <div class="form-group">
                                <label for="deprecateUnits">Units to Deprecate</label>
                                <input type="number" class="form-control" id="deprecateUnits" name="deprecate_units"
                                    min="1" required>
                            </div>
                            <div class="form-group">
                                <label for="rejectReason">Reason for Deprecation</label>
                                <textarea class="form-control" id="rejectReason" name="reason" rows="3" maxlength="150"
                                    required></textarea>
                            </div>
                            <!-- Hidden fields -->
                            <input type="hidden" name="sno" id="hiddenSno">
                            <input type="hidden" name="action" value="deprecate">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Submit Deprecation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Model to add data back to branch_items Modal -->
        <div class="modal fade" id="confirmReturnModal" tabindex="-1" role="dialog"
            aria-labelledby="confirmReturnModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmReturnModalLabel">Confirm Return Request</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to request again to return this item?</p>
                    </div>
                    <div class="modal-footer">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <!-- Hidden field to pass the item identifier, if needed -->
                            <input type="hidden" name="sno" id="snoConfirmReturn">
                            <input type="hidden" name="action" value="request_again_return">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Yes, Request Again</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Return Modal -->
        <div class="modal fade" id="returnModal" tabindex="-1" role="dialog" aria-labelledby="returnModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="returnModalLabel">Return to Inventory</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form id="returnForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="snoReturn" id="snoReturn">
                            <div class="form-group">
                                <label for="product_nameReturn">Product Name</label>
                                <input type="text" class="form-control" id="product_nameReturn"
                                    name="product_nameReturn" readonly>
                            </div>
                            <div class="form-group">
                                <label for="model_nameReturn">Stock Reg / Page Sno</label>
                                <input type="text" class="form-control" id="model_nameReturn" name="model_nameReturn"
                                    readonly>
                            </div>
                            <!-- Reason Input Section with Textarea -->
                            <div class="form-group">
                                <label for="reasonReturn">Reason for Return</label>
                                <textarea class="form-control" id="reasonReturn" name="reasonReturn"
                                    placeholder="Enter reason for return" required maxlength="150" rows="3"
                                    style="resize: vertical;"></textarea>
                            </div>
                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Return Items</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <!-- Add item to branch Form -->
        <div class="container my-4">
            <h3>Request To Return Items</h3>
        </div>


        <div class="container">
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Type</th>
                        <th scope="col">Stock Reg Page/Sno</th>
                        <th scope="col">units</th>
                        <th scope="col">Rejected By</th>
                        <th scope="col">Reason</th>
                        <th scope="col">Return Reject Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM `return_request_cancel` WHERE `branch` = '$_SESSION[branch]' AND `lab` = '$_SESSION[lab]' ORDER BY `sno` DESC";
                    $result = mysqli_query($conn, $sql);
                    $sno = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $sno++;
                        $allotment_date = !empty($row['allotment_date']) ? date('d-m-Y', strtotime($row['allotment_date'])) : '';
                        echo '
                            <tr>
                                <th scope="row">' . $sno . '</th>
                                <td>' . $row['product_name'] . '</td>
                                <td>' . $row['type'] . '</td>
                                <td>' . $row['rr_reg'] . '</td>
                                <td>' . $row['units'] . '</td>
                                <td>' . $row['rejected_by'] . '</td>
                                <td>' . $row['reason'] . '</td>
                                <td>' . $row['cancel_date'] . '</td>
                                <td>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <button class="edit btn btn-sm btn-primary" id="' . $row['sno'] . '">Use</button>
                                        <button class="return btn btn-sm btn-primary" id="r' . $row['sno'] . '">Return</button>
                                        <button class="deprecate btn btn-sm btn-primary" id="d' . $row['sno'] . '">Deprecate</button>
                                    </div>
                                </td>
                            </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>




    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Edit Button Logic
            let requestButtons = document.querySelectorAll('button.edit');
            Array.from(requestButtons).forEach(function (button) {
                button.addEventListener('click', function (e) {
                    e.stopPropagation();
                    console.log("Request Again button clicked");

                    // Assume the button's id contains the unique record identifier.
                    let sno = e.target.id;
                    document.getElementById('snoConfirmReturn').value = sno;

                    // Show the confirmation modal
                    $('#confirmReturnModal').modal('show');
                });
            });

            document.querySelectorAll('button.return').forEach(function (button) {
                button.addEventListener('click', function (e) {
                    e.preventDefault();

                    // Get the closest row
                    const tr = e.target.closest('tr');

                    // Extract data from the row's td cells
                    const cells = tr.querySelectorAll('td');

                    // Based on your table structure:
                    // cells[0] => Product Name
                    // cells[2] => Stock Reg / Page Sno
                    const productName = cells[0].innerText.trim();
                    const stockReg = cells[2].innerText.trim();

                    // Extract the 'sno' from the button's ID (remove the leading 'r')
                    const snoReturnValue = e.target.id.replace('r', '');

                    // Populate the modal fields
                    document.getElementById('snoReturn').value = snoReturnValue;
                    document.getElementById('product_nameReturn').value = productName;
                    document.getElementById('model_nameReturn').value = stockReg;
                    document.getElementById('reasonReturn').value = ''; // Clear input

                    // Show the modal
                    $('#returnModal').modal('show');
                });
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
            $('#myTable').DataTable();

        });
    </script>



    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let deprecateButtons = document.querySelectorAll('button.deprecate');
            Array.from(deprecateButtons).forEach(function (button) {
                button.addEventListener('click', function (e) {
                    e.stopPropagation();
                    console.log("Deprecate button clicked");

                    // Get the row and its cells
                    let tr = e.target.closest('tr');
                    let tds = tr.getElementsByTagName('td');

                    // Assuming: 
                    // tds[0] -> Product Name
                    // tds[2] -> Allotment Date
                    // tds[3] -> Current Units
                    let product_name = tds[0].innerText;
                    let allotment_date = tds[2].innerText;
                    let current_units = tds[3].innerText;

                    // Populate the Deprecate modal fields
                    document.getElementById('productNameDeprecate').value = product_name;
                    document.getElementById('allotmentDateDeprecate').value = allotment_date;
                    document.getElementById('currentUnitsDeprecate').value = current_units;
                    // Clear any previous values for input fields
                    document.getElementById('deprecateUnits').value = '';
                    document.getElementById('rejectReason').value = '';
                    // Extract sno by removing the 'd' prefix from the button's id
                    document.getElementById('hiddenSno').value = e.target.id.substring(1);

                    // Show the Deprecate modal (using jQuery for Bootstrap 4)
                    $('#deprecateModal').modal('show');
                });
            });
        });
    </script>


</body>

</html>

<?php
ob_end_flush();
?>