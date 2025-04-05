<?php
ob_start();
session_start();

// If the branch and lab are submitted via POST, set them in the session.
if (isset($_POST['branch']) && isset($_POST['lab'])) {
    $_SESSION['branch'] = $_POST['branch'];
    $_SESSION['lab'] = $_POST['lab'];
}

// First, check if the user is logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Not logged in—redirect to login.
    header("Location: ../index.php");
    exit();
}

// Now ensure branch and lab are set (this will pass if POST data was submitted).
if (!isset($_SESSION['branch']) || !isset($_SESSION['lab'])) {
    // If they are still not set, redirect to login.
    header("Location: ../index.php");
    exit();
}

// Prevent access for INVENTORY branch and lab.
if ($_SESSION['branch'] === 'INVENTORY' && $_SESSION['lab'] === 'INVENTORY') {
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

        <!-- connect to database  -->
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
    $query = "SELECT * FROM branch_items WHERE sno = '$sno'";
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
            '{$row['got_it_from']}', '$reason' , '{$row['current_condition']}')";
        $insert_result = mysqli_query($conn, $insert_sql);

        if ($insert_result) {
            if ($deprecate_units == $current_units) {
                // If all units are deprecated, remove the item from branch_items
                $delete_sql = "DELETE FROM branch_items WHERE sno = '$sno'";
                mysqli_query($conn, $delete_sql);
            } else {
                // Otherwise, update the remaining units in branch_items
                $new_units = $current_units - $deprecate_units;
                $update_sql = "UPDATE branch_items SET units = '$new_units' WHERE sno = '$sno'";
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
        // Transfer and  Edit logic
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            // 1. Edit Functionality (Modal Edit)
            if (isset($_POST['snoEdit'])) {
                // Retrieve and trim inputs
                $snoEdit = $_POST['snoEdit'];
                $units = $_POST['unitsEdit'];
                $use_for = trim($_POST['use_forEdit']);

                // Fetch item details from branch_items
                $query = "SELECT product_name, rr_reg, allotment_date, branch, lab, unit_price, units FROM branch_items WHERE sno = '$snoEdit'";
                $result = mysqli_query($conn, $query);
                $row = mysqli_fetch_assoc($result);

                if (!$row) {
                    $_SESSION['popup_message'] = "Record not found.";
                    $_SESSION['popup_type'] = "danger";
                } elseif ($units > $row['units']) {
                    $_SESSION['popup_message'] = "Not enough units available.";
                    $_SESSION['popup_type'] = "danger";
                } else {
                    $use_date = date('Y-m-d');
                    $use_name = $_SESSION['name'];

                    // Insert data into consumable_items
                    $insertQuery = "INSERT INTO consumable_items (sno, product_name, rr_reg, allotment_date, branch, lab, unit_price, units, use_date, use_name , use_for) VALUES 
                        ('$snoEdit', '{$row['product_name']}', '{$row['rr_reg']}', '{$row['allotment_date']}', '{$_SESSION['branch']}', '{$_SESSION['lab']}', '{$row['unit_price']}', '$units', '$use_date', '$use_name','$use_for')";
                    $insertResult = mysqli_query($conn, $insertQuery);

                    if ($insertResult) {
                        // Update or delete the branch_items row
                        $new_units = $row['units'] - $units;
                        if ($new_units > 0) {
                            mysqli_query($conn, "UPDATE branch_items SET units = '$new_units' WHERE sno = '$snoEdit'");
                        } else {
                            mysqli_query($conn, "DELETE FROM branch_items WHERE sno = '$snoEdit'");
                        }

                        $_SESSION['popup_message'] = "Record successfully moved to consumable items.";
                        $_SESSION['popup_type'] = "success";
                    } else {
                        $_SESSION['popup_message'] = "Failed to move record: " . mysqli_error($conn);
                        $_SESSION['popup_type'] = "danger";
                    }
                }

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }


            // 2. Transfer Functionality
            elseif (isset($_POST['snoReturn'])) {
                $snoReturn = intval($_POST['snoReturn']);
                $product_name_return = trim($_POST['product_nameReturn'] ?? '');
                $model_name_return = trim($_POST['model_nameReturn'] ?? '');
                $return_units = $_POST['return_units'] ?? '';
                $branch = $_SESSION['branch'] ?? '';
                $lab = $_SESSION['lab'] ?? '';
                $reason = trim($_POST['reasonReturn'] ?? '');
                $return_by = $_SESSION['name'] ?? ''; // User returning the item
        
                // Validate required fields
                if ($product_name_return === '' || $model_name_return === '' || $branch === '' || $lab === '' || $reason === '') {
                    $_SESSION['popup_message'] = "Please fill in all required fields for return.";
                    $_SESSION['popup_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                // Validate numeric return_units
                if (!is_numeric($return_units) || $return_units <= 0) {
                    $_SESSION['popup_message'] = "Return units must be a number greater than 0.";
                    $_SESSION['popup_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }
                $return_units = (int) $return_units;

                // Retrieve item details from branch_items
                $query_item = "SELECT unit_price, `type`, rr_reg, purchase_date, got_it_from 
                   FROM branch_items 
                   WHERE sno = $snoReturn";
                $result_item = mysqli_query($conn, $query_item);
                if ($result_item && mysqli_num_rows($result_item) > 0) {
                    $row_item = mysqli_fetch_assoc($result_item);
                    $price_per_unit = $row_item['unit_price'];
                    $rr_reg = $row_item['rr_reg'];
                    $purchase_date = $row_item['purchase_date'];
                    $got_it_from = $row_item['got_it_from'];
                    $type = $row_item['type'];
                } else {
                    $_SESSION['popup_message'] = "Error retrieving item details.";
                    $_SESSION['popup_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                // Retrieve current units from branch_items
                $query_current_units = "SELECT units FROM branch_items WHERE sno = $snoReturn";
                $result_current_units = mysqli_query($conn, $query_current_units);
                if ($result_current_units && mysqli_num_rows($result_current_units) > 0) {
                    $row_current = mysqli_fetch_assoc($result_current_units);
                    $current_units = (int) $row_current['units'];
                } else {
                    $_SESSION['popup_message'] = "Error retrieving available units.";
                    $_SESSION['popup_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                // Ensure return units do not exceed available units
                if ($return_units > $current_units) {
                    $_SESSION['popup_message'] = "Return units cannot be greater than available units ($current_units).";
                    $_SESSION['popup_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                $new_units = $current_units - $return_units;
                $overall_price = $price_per_unit * $return_units;
                $permission = "Stock Manager";
                $product_condition = "Good";

                // Insert into return_request table
                $sql_insert_return = "INSERT INTO return_request 
        (product_name, `type`, rr_reg, purchase_date, got_it_from, unit_price, overall_price, units, branch, lab, permission, product_condition, return_by, reason) 
        VALUES 
        ('$product_name_return', '$type', '$rr_reg', '$purchase_date', '$got_it_from', '$price_per_unit', '$overall_price', '$return_units', '$branch', '$lab', '$permission', '$product_condition', '$return_by', '$reason')";
                $result_insert_return = mysqli_query($conn, $sql_insert_return);

                // Update branch_items: subtract returned units or delete if none remain
                if ($new_units == 0) {
                    $sql_update_branch = "DELETE FROM branch_items WHERE sno = $snoReturn";
                } else {
                    $sql_update_branch = "UPDATE branch_items SET units = $new_units WHERE sno = $snoReturn";
                }
                $result_update_branch = mysqli_query($conn, $sql_update_branch);

                // Check query execution results
                if ($result_insert_return && $result_update_branch) {
                    $_SESSION['popup_message'] = "Items returned successfully!";
                    $_SESSION['popup_type'] = "success";
                } else {
                    $_SESSION['popup_message'] = "Item return failed: " . mysqli_error($conn);
                    $_SESSION['popup_type'] = "danger";
                }

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }

        }
        ?>


        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModelLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModelLabel">Enter Use Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="snoEdit" id="snoEdit">
                            <div class="form-group">
                                <label for="product_nameEdit">Product Name</label>
                                <input type="text" class="form-control" id="product_nameEdit" name="product_nameEdit"
                                    readonly>
                            </div>
                            <div class="form-group">
                                <label for="rr_regEdit">Stock Reg Page / Sno</label>
                                <input type="text" class="form-control" id="rr_regEdit" name="rr_regEdit" readonly>
                            </div>
                            <div class="form-group">
                                <label for="allotment_dateEdit">Allotment Date</label>
                                <input type="date" class="form-control" id="allotment_dateEdit"
                                    name="allotment_dateEdit" readonly>
                            </div>
                            <div class="form-group">
                                <label for="unitsEdit">Units</label>
                                <input type="number" class="form-control" id="unitsEdit" name="unitsEdit" required>
                            </div>
                            <div class="form-group">
                                <label for="use_forEdit">Use for</label>
                                <textarea class="form-control" id="use_forEdit" name="use_forEdit" rows="3"
                                    required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
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
                            <div class="form-group">
                                <label for="return_units">Units to Return</label>
                                <input type="number" class="form-control" id="return_units" name="return_units"
                                    placeholder="Enter units to return" required>
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
                                <button type="submit" class="btn btn-danger">Return Items</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>


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



        <div class="container">
            <h3 class="mt-4"><?php echo "$_SESSION[branch] $_SESSION[lab] Current Consumable Stock"; ?></h3>
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Stock Reg Page/Sno</th>
                        <th scope="col">Allotment Date</th>
                        <th scope="col">units</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM `branch_items` WHERE `branch` = '$_SESSION[branch]' AND `lab` = '$_SESSION[lab]' AND `type` = 'Consumable Item' ORDER BY `sno` DESC";
                    $result = mysqli_query($conn, $sql);
                    $sno = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $sno++;
                        $allotment_date = !empty($row['allotment_date']) ? date('d-m-Y', strtotime($row['allotment_date'])) : '';
                        echo '
                            <tr>
                                <th scope="row">' . $sno . '</th>
                                <td>' . $row['product_name'] . '</td>
                                <td>' . $row['rr_reg'] . '</td>
                                <td>' . $row['allotment_date'] . '</td>
                                <td>' . $row['units'] . '</td>
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




    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let editButtons = document.querySelectorAll('button.edit');
            Array.from(editButtons).forEach(function (button) {
                button.addEventListener('click', function (e) {
                    e.stopPropagation();
                    console.log("Edit button clicked");

                    // Get the row and its cells
                    let tr = e.target.closest('tr');
                    let tds = tr.getElementsByTagName('td');

                    // Ensure the cells' indices are correctly assigned:
                    let product_name = tds[0].innerText;
                    let rr_reg = tds[1].innerText;
                    let allotment_date = tds[2].innerText;

                    // Populate the Edit modal fields
                    document.getElementById('product_nameEdit').value = product_name;
                    document.getElementById('rr_regEdit').value = rr_reg;
                    document.getElementById('allotment_dateEdit').value = allotment_date;
                    document.getElementById('unitsEdit').value = 0; // Default value for units
                    document.getElementById('use_forEdit').value = ""; // Emptying "use_for" for new entries
                    document.getElementById('snoEdit').value = e.target.id;

                    // Show the Edit modal
                    $('#editModal').modal('show'); // Ensure correct method
                });
            });

            // Form submission validation
            document.getElementById('editForm').addEventListener('submit', function (e) {
                let units = document.getElementById('unitsEdit').value;

                if (units <= 0) {
                    e.preventDefault(); // Prevent form submission
                    alert('Units should not be zero or negative. Please enter a valid number.');
                }
            });


            let returnButtons = document.querySelectorAll('button.return');
            Array.from(returnButtons).forEach(function (button) {
                button.addEventListener('click', function (e) {
                    e.stopPropagation();
                    console.log("Return button clicked");

                    let tr = e.target.closest('tr');
                    let tds = tr.getElementsByTagName('td');

                    let product_name = tds[0].innerText;
                    let model_name = tds[1].innerText;
                    let units_available = parseInt(tds[3].innerText, 10); // Ensure it's an integer

                    let snoReturnValue = e.target.id.replace('r', '');

                    document.getElementById('product_nameReturn').value = product_name;
                    document.getElementById('model_nameReturn').value = model_name;
                    document.getElementById('snoReturn').value = snoReturnValue;

                    document.getElementById('return_units').max = units_available; // Ensure limit
                    document.getElementById('return_units').value = ''; // Prevent stale values

                    $('#returnModal').modal('show');
                });
            });
        });

    </script>

</body>

</html>

<?php
ob_end_flush();
?>