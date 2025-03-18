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


    <div id="admin" class="right">

        <!-- connect to database  -->
        <?php
        include "../_dbconnect.php";

        // Transfer, Edit, and Insert Logic
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            // 1. Edit Functionality (Modal Edit)
            if (isset($_POST['snoEdit'])) {
                // Retrieve input fields
                $snoEdit = $_POST['snoEdit'];
                $product_name = $_POST['product_nameEdit'];
                $type = $_POST['typeEdit'];
                $rr_reg = $_POST['register_numberEdit'] . '/' . $_POST['page_numberEdit']; // Combining register number and page number
                $purchase_date = $_POST['purchase_dateEdit'];
                $got_it_from = $_POST['got_it_fromEdit'];
                $srno = $_POST['srnoEdit'];
                $bill_date = $_POST['bill_dateEdit'];
                $units = $_POST['unitsEdit'];
                $unit_price = $_POST['unit_priceEdit'];
                $overall_price = $_POST['overall_priceEdit'];

                // Validate required fields
                if ($product_name === '' || $type === '' || $_POST['register_numberEdit'] === '' || $_POST['page_numberEdit'] === '') {
                    $_SESSION['popup_message'] = "Please fill in all required fields.";
                    $_SESSION['popup_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }


                // Validate numeric fields
                if (!is_numeric($unit_price) || $unit_price <= 0) {
                    $_SESSION['popup_message'] = "Unit price must be a positive number.";
                    $_SESSION['popup_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                if (!is_numeric($units) || $units <= 0) {
                    $_SESSION['popup_message'] = "Units must be a positive number.";
                    $_SESSION['popup_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                if (!is_numeric($overall_price) || $overall_price <= 0) {
                    $_SESSION['popup_message'] = "Overall price must be a positive number.";
                    $_SESSION['popup_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                // Update inventory_items using prepared statements
                $sql = "UPDATE `inventory_items` SET 
                            `product_name` = '$product_name', 
                            `type` = '$type', 
                            `rr_reg` = '$rr_reg', 
                            `purchase_date` = '$purchase_date', 
                            `got_it_from` = '$got_it_from', 
                            `srno` = '$srno', 
                            `bill_date` = '$bill_date', 
                            `unit_price` = $unit_price, 
                            `units` = $units, 
                            `overall_price` = $overall_price 
                        WHERE `sno` = $snoEdit";

                $updateResult = mysqli_query($conn, $sql);

                // Fetch rr_reg for the given snoEdit
                $fetchSQL = "SELECT rr_reg FROM inventory_items WHERE sno = $snoEdit";
                $result = mysqli_query($conn, $fetchSQL);
                $row = mysqli_fetch_assoc($result);
                $rr_reg_found = $row['rr_reg'];  // Get rr_reg from inventory_items
        
                // Update rr_received_items using the fetched rr_reg
                $sql2 = "UPDATE `rr_received_items` SET 
                        `product_name` = '$product_name', 
                        `type` = '$type', 
                        `purchase_date` = '$purchase_date', 
                        `got_it_from` = '$got_it_from', 
                        `bill_date` = '$bill_date', 
                        `unit_price` = $unit_price, 
                        `units` = $units, 
                        `overall_price` = $overall_price 
                    WHERE `rr_reg` = '$rr_reg_found'";

                if (!empty($rr_reg_found)) {
                    $sql2 = "UPDATE `rr_received_items` SET 
                            `product_name` = '$product_name', 
                            `type` = '$type', 
                            `purchase_date` = '$purchase_date', 
                            `got_it_from` = '$got_it_from', 
                            `bill_date` = '$bill_date', 
                            `unit_price` = $unit_price, 
                            `units` = $units, 
                            `overall_price` = $overall_price 
                        WHERE `srno` = '$srno'";

                    mysqli_query($conn, $sql2);
                }


                $updateResult2 = mysqli_query($conn, $sql2);


                // Check update status
                if ($updateResult) {
                    $_SESSION['popup_message'] = "Record updated successfully!";
                    $_SESSION['popup_type'] = "success";
                } else {
                    $_SESSION['popup_message'] = "Record update failed: " . mysqli_error($conn);
                    $_SESSION['popup_type'] = "danger";
                }

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();

            }

            // 2. Transfer Functionality
            elseif (isset($_POST['snoTransfer'])) {
                // Retrieve and trim inputs for transfer
                $snoTransfer = $_POST['snoTransfer'];
                $product_name_transfer = trim($_POST['product_nameTransfer'] ?? '');
                $type_transfer = trim($_POST['model_nameTransfer'] ?? '');
                $transfer_units = $_POST['transfer_units'] ?? '';
                $branch = trim($_POST['branch'] ?? '');
                $lab = trim($_POST['lab'] ?? '');

                // Retrieve additional fields from inventory_items
                $query_details = "SELECT rr_reg, purchase_date, got_it_from, srno, bill_date, unit_price FROM inventory_items WHERE sno = '$snoTransfer'";
                $result_details = mysqli_query($conn, $query_details);

                if ($result_details && mysqli_num_rows($result_details) > 0) {
                    $row_details = mysqli_fetch_assoc($result_details);
                    $rr_reg = $row_details['rr_reg'];
                    $purchase_date = $row_details['purchase_date'];
                    $got_it_from = $row_details['got_it_from'];
                    $srno = $row_details['srno'];
                    $bill_date = $row_details['bill_date'];
                    $unit_price = $row_details['unit_price'];
                } else {
                    $_SESSION['popup_message'] = "Error retrieving item details.";
                    $_SESSION['popup_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                // Validate required fields
                if ($product_name_transfer === '' || $type_transfer === '' || $branch === '' || $lab === '') {
                    $_SESSION['popup_message'] = "Please fill in all required fields for transfer.";
                    $_SESSION['popup_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                // Validate transfer_units
                if (!is_numeric($transfer_units) || $transfer_units <= 0) {
                    $_SESSION['popup_message'] = "Transfer units must be a number greater than 0.";
                    $_SESSION['popup_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                // Retrieve current available units
                $query_current_units = "SELECT units FROM inventory_items WHERE sno = '$snoTransfer'";
                $result_current_units = mysqli_query($conn, $query_current_units);
                if ($result_current_units && mysqli_num_rows($result_current_units) > 0) {
                    $row_current = mysqli_fetch_assoc($result_current_units);
                    $current_units = (int) $row_current['units']; // Corrected field name
                } else {
                    $_SESSION['popup_message'] = "Error retrieving available units.";
                    $_SESSION['popup_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }


                // Ensure that the transfer units do not exceed available units
                if ($transfer_units > $current_units) {
                    $_SESSION['popup_message'] = "Transfer units cannot be greater than available units ($current_units).";
                    $_SESSION['popup_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                // Calculate overall price
                $overall_price = $unit_price * $transfer_units;

                // Insert into new table
                $sql_insert = "INSERT INTO allotment (product_name, type, rr_reg, purchase_date, got_it_from, srno, bill_date, unit_price, overall_price, units, branch, lab,permission) 
                    VALUES ( '$product_name_transfer', '$type_transfer', '$rr_reg', '$purchase_date', '$got_it_from', '$srno', '$bill_date', '$unit_price', '$overall_price', '$transfer_units', '$branch', '$lab','Officer')";

                $result_insert = mysqli_query($conn, $sql_insert);

                // Update inventory_items: subtract transferred units or delete if none remain
                $new_units = $current_units - $transfer_units;
                if ($new_units == 0) {
                    $sql_update = "DELETE FROM inventory_items WHERE sno = '$snoTransfer'";
                } else {
                    $sql_update = "UPDATE inventory_items SET units = '$new_units' WHERE sno = '$snoTransfer'";
                }
                $result_update = mysqli_query($conn, $sql_update);

                // Check if queries succeeded
                if ($result_insert && $result_update) {
                    $_SESSION['popup_message'] = "Record transferred successfully!";
                    $_SESSION['popup_type'] = "success";
                } else {
                    $_SESSION['popup_message'] = "Record transfer failed: " . mysqli_error($conn);
                    $_SESSION['popup_type'] = "danger";
                }

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }

            // 3. Insert New Record Functionality
            else {
                // Retrieve and trim inputs
                $product_name = trim($_POST['product_name'] ?? '');
                $type = trim($_POST['type'] ?? '');
                $register_number = $_POST['register_number'] ?? '';
                $page_number = $_POST['page_number'] ?? '';
                $purchase_date = $_POST['purchase_date'] ?? '';
                $got_it_from = trim($_POST['got_it_from'] ?? '');
                $srno = $_POST['srno'] ?? '';
                $bill_date = $_POST['bill_date'] ?? '';
                $unit_price = $_POST['unit_price'] ?? '';
                $units = $_POST['units'] ?? '';
                $overall_price = $_POST['overall_price'] ?? '';

                // Validation: Required fields
                if ($product_name === '' || $type === '' || $register_number === '' || $page_number === '' || $purchase_date === '' || $unit_price === '' || $overall_price === '') {
                    $_SESSION['popup_message'] = "Please fill in all required fields.";
                    $_SESSION['popup_type'] = "danger";
                }
                // Validation: Numeric fields
                elseif (!is_numeric($register_number) || !is_numeric($page_number)) {
                    $_SESSION['popup_message'] = "Register number and page number must be numeric.";
                    $_SESSION['popup_type'] = "danger";
                } elseif (!is_numeric($unit_price) || $unit_price <= 0) {
                    $_SESSION['popup_message'] = "Unit price must be a number greater than 0.";
                    $_SESSION['popup_type'] = "danger";
                } elseif (!is_numeric($overall_price) || $overall_price <= 0) {
                    $_SESSION['popup_message'] = "Overall price must be a number greater than 0.";
                    $_SESSION['popup_type'] = "danger";
                } else {
                    $sql = "INSERT INTO `inventory_items` 
                                (`product_name`, `type`, `rr_reg`, `purchase_date`, `got_it_from`, `srno`, `bill_date`, `unit_price`,`units`, `overall_price`) 
                            VALUES 
                                ('$product_name', '$type', '$register_number/$page_number', '$purchase_date', '$got_it_from', '$srno', '$bill_date', '$unit_price','$units', '$overall_price')";

                    $result = mysqli_query($conn, $sql);

                    $sql2 = "INSERT INTO `rr_received_items` 
                                (`product_name`, `type`, `rr_reg`, `purchase_date`, `got_it_from`, `srno`, `bill_date`, `unit_price`,`units`, `overall_price`) 
                            VALUES 
                                ('$product_name', '$type', '$register_number/$page_number', '$purchase_date', '$got_it_from', '$srno', '$bill_date', '$unit_price','$units', '$overall_price')";

                    $result2 = mysqli_query($conn, $sql2);

                    if ($result && $result2) {
                        $_SESSION['popup_message'] = "Item added successfully!";
                        $_SESSION['popup_type'] = "success";
                    } else {
                        $_SESSION['popup_message'] = "Failed to add item: " . mysqli_error($conn);
                        $_SESSION['popup_type'] = "danger";
                    }
                }

                // Redirect to avoid form resubmission
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }

        }
        ?>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModel" tabindex="-1" role="dialog" aria-labelledby="editModelLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModelLabel">Edit Inventory Item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                            method="POST">
                            <input type="hidden" name="snoEdit" id="snoEdit">

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="product_nameEdit">Product Name</label>
                                    <input type="text" class="form-control" id="product_nameEdit"
                                        name="product_nameEdit" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="typeEdit">Type</label>
                                    <select class="form-control" id="typeEdit" name="typeEdit" required>
                                        <option value="">Select Type</option>
                                        <option value="T&P Item">T&P Item</option>
                                        <option value="Consumable Item">Consumable Item</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="register_numberEdit">Register Page/S No.</label>
                                    <div class="d-flex">
                                        <input type="number" class="form-control mr-2" id="register_numberEdit"
                                            name="register_numberEdit" required>
                                        <input type="number" class="form-control" id="page_numberEdit"
                                            name="page_numberEdit" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="purchase_dateEdit">Purchase Date</label>
                                    <input type="date" class="form-control" id="purchase_dateEdit"
                                        name="purchase_dateEdit" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="got_it_fromEdit">Got It From</label>
                                    <input type="text" class="form-control" id="got_it_fromEdit" name="got_it_fromEdit">
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="srnoEdit">Serial Number & Bill Date</label>
                                    <div class="d-flex">
                                        <input type="number" class="form-control mr-2" id="srnoEdit" name="srnoEdit">
                                        <input type="date" class="form-control" id="bill_dateEdit" name="bill_dateEdit">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="unit_priceEdit">Unit Price</label>
                                    <input type="number" step="0.01" class="form-control" id="unit_priceEdit"
                                        name="unit_priceEdit">
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="unitsEdit">Units</label>
                                    <input type="number" step="0.01" class="form-control" id="unitsEdit"
                                        name="unitsEdit">
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="overall_priceEdit">Overall Price</label>
                                    <input type="number" step="0.01" class="form-control" id="overall_priceEdit"
                                        name="overall_priceEdit">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" form="editForm" class="btn btn-primary">Update Item</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Transfer Modal -->
        <div class="modal fade" id="transferModel" tabindex="-1" role="dialog" aria-labelledby="transferModelLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="transferModelLabel">Allocate Stock Item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="transferForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="snoTransfer" id="snoTransfer">

                            <div class="form-group">
                                <label for="product_nameTransfer">Product Name</label>
                                <input type="text" class="form-control" id="product_nameTransfer"
                                    name="product_nameTransfer" readonly>
                            </div>

                            <div class="form-group">
                                <label for="model_nameTransfer">Type/Register details</label>
                                <input type="text" class="form-control" id="model_nameTransfer"
                                    name="model_nameTransfer" readonly>
                            </div>

                            <div class="form-group">
                                <label for="transfer_units">Units to Transfer</label>
                                <input type="number" class="form-control" id="transfer_units" name="transfer_units"
                                    placeholder="Enter units to transfer" required>
                            </div>

                            <div class="form-group">
                                <label for="branchTransfer">Select Branch</label>
                                <select class="form-control" id="branchTransfer" name="branch" required>
                                    <option value="">Select Branch</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="labTransfer">Select Lab</label>
                                <select class="form-control" id="labTransfer" name="lab" required>
                                    <option value="">Select Lab</option>
                                </select>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Transfer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <!-- Add Admin Button -->
        <div class="container my-4">
            <!-- Add Item Button -->
            <button type="button" class="add btn btn-sm btn-primary" data-toggle="modal" data-target="#addItemModal"
                style="width: 25%; height: 60px; font-size: large;">Add New Item to Inventory</button>

        </div>

        <!-- Add Item Modal -->
        <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addItemModalLabel">Add New Inventory Item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addItemForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"
                            method="POST">
                            <div class="form-row">

                                <div class="form-group col-md-4">
                                    <label for="product_name">Product Name</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name"
                                        placeholder="Enter product name" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="type">Type</label>
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="T&P Item">T&P Item</option>
                                        <option value="Consumable Item">Consumable Item</option>
                                    </select>
                                </div>


                                <div class="form-group col-md-4">
                                    <label for="register_number">Register Page/S No.</label>
                                    <div class="d-flex">
                                        <input type="number" class="form-control mr-2" id="register_number"
                                            name="register_number" placeholder="Enter Register Number" required>
                                        <input type="number" class="form-control" id="page_number" name="page_number"
                                            placeholder="Enter Page No." required>
                                    </div>
                                </div>

                            </div>


                            <div class="form-row">

                                <div class="form-group col-md-4">
                                    <label for="purchase_date">Purchase Date</label>
                                    <input type="date" class="form-control" id="purchase_date" name="purchase_date"
                                        required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="got_it_from">Got It From</label>
                                    <input type="text" class="form-control" id="got_it_from" name="got_it_from"
                                        placeholder="Enter supplier name">
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="srno">Serial Number & Bill Date</label>
                                    <div class="d-flex">
                                        <input type="number" class="form-control mr-2" id="srno" name="srno"
                                            placeholder="Enter Serial Number">
                                        <input type="date" class="form-control" id="bill_date" name="bill_date">
                                    </div>
                                </div>


                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="unit_price">Unit Price</label>
                                    <input type="number" step="0.01" class="form-control" id="unit_price"
                                        name="unit_price" placeholder="Enter unit price">
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="units">Units</label>
                                    <input type="number" step="0.01" class="form-control" id="units" name="units"
                                        placeholder="Enter num of units">
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="overall_price">Overall Price</label>
                                    <input type="number" step="0.01" class="form-control" id="overall_price"
                                        name="overall_price" placeholder="Enter overall price">
                                </div>
                            </div>
                            <input type="hidden" name="fragment" id="fragmentAdd" value="#inventory">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" form="addItemForm" class="btn btn-primary">Add Item</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript for add Modal Handling -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.add').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.stopPropagation();
                        console.log("Add button clicked");
                        $('#addItemModal').modal('show');
                    });
                });
            });
        </script>



        <!-- showing the table  -->
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
                        <th class="srno-td d-none" style="display: hidden;">'hidden for srno'</th>
                        <th class="billdate-td d-none" style="display: hidden;">' hidden for billdate'</th>
                        <th scope="col">Unit Price</th>
                        <th scope="col">Units</th>
                        <th scope="col">Total Price</th>
                        <th scope="col">Action</th>
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
                            
                            <!-- Visible column for SRNO & Bill Date (Combined) -->
                            <td><small>' . htmlspecialchars($row['srno'], ENT_QUOTES, 'UTF-8') . '<br>' . $bill_date_formatted . '</small></td>

                            <!-- Hidden columns for SRNO and Bill Date (For JavaScript use) -->
                            <td class="srno-td d-none">' . htmlspecialchars($row['srno'], ENT_QUOTES, 'UTF-8') . '</td>
                            <td class="billdate-td d-none">' . $bill_date_formatted . '</td>

                            <td>' . $row['unit_price'] . '</td>
                            <td>' . $row['units'] . '</td>
                            <td>' . $row['overall_price'] . '</td>
                            <td>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <button class="edit btn btn-sm btn-primary" id="' . $row['sno'] . '">Edit</button>
                                    <button class="transfer btn btn-sm btn-primary" id="t' . $row['sno'] . '">Allocate</button>
                                </div>
                            </td>
                        </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- script for edting and allotment button  -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Edit Button Logic
                $(document).ready(function () {
                    $('.edit').click(function () {
                        var tr = $(this).closest('tr'); // Get the closest row

                        $('#snoEdit').val($(this).attr('id')); // Get the item ID
                        $('#product_nameEdit').val(tr.find("td:eq(0)").text());
                        $('#typeEdit').val(tr.find("td:eq(1)").text());

                        // Extract Register Number & Page No.
                        var rr_regText = tr.find("td:eq(2)").text().split('/');
                        $('#register_numberEdit').val(rr_regText[0] ? rr_regText[0].trim() : '');
                        $('#page_numberEdit').val(rr_regText[1] ? rr_regText[1].trim() : '');

                        // Corrected indices for Purchase Date and Got it From
                        var purchaseDateText = tr.find("td:eq(3)").text().trim();
                        var purchaseDateParts = purchaseDateText.split('-');
                        if (purchaseDateParts.length === 3) {
                            var purchaseDateFormatted = purchaseDateParts[2] + '-' + purchaseDateParts[1] + '-' + purchaseDateParts[0]; // yyyy-mm-dd
                            $('#purchase_dateEdit').val(purchaseDateFormatted);
                        } else {
                            $('#purchase_dateEdit').val(''); // Set empty value if date is invalid
                        }

                        $('#got_it_fromEdit').val(tr.find("td:eq(4)").text());

                        // Fetch SRNO & Bill Date from hidden columns
                        $('#srnoEdit').val(tr.find(".srno-td").text().trim());

                        var billDateText = tr.find(".billdate-td").text().trim();
                        var billDateParts = billDateText.split('-');
                        if (billDateParts.length === 3) {
                            var billDateFormatted = billDateParts[2] + '-' + billDateParts[1] + '-' + billDateParts[0]; // yyyy-mm-dd
                            $('#bill_dateEdit').val(billDateFormatted);
                        } else {
                            $('#bill_dateEdit').val(''); // Set empty value if date is invalid
                        }

                        // Indices after hidden columns
                        $('#unit_priceEdit').val(tr.find("td:eq(8)").text());
                        $('#unitsEdit').val(tr.find("td:eq(9)").text());
                        $('#overall_priceEdit').val(tr.find("td:eq(10)").text());

                        $('#editModel').modal('show');
                    });
                });




                // Transfer Button Logic
                let transferButtons = document.querySelectorAll('button.transfer');
                Array.from(transferButtons).forEach(function (button) {
                    button.addEventListener('click', function (e) {
                        e.stopPropagation();
                        console.log("Transfer button clicked");

                        // Get the row and its cells
                        let tr = e.target.closest('tr');
                        let tds = tr.getElementsByTagName('td');

                        // Retrieve the needed values (adjust indices as per your table structure)
                        let product_name = tds[0].innerText;
                        let model_name = tds[1].innerText;

                        // Extract the sno from the button's ID (assuming it starts with "t")
                        let snoTransferValue = e.target.id.replace('t', '');

                        // Debug logs
                        console.log("Product Name:", product_name);
                        console.log("Model Name:", model_name);
                        console.log("snoTransfer:", snoTransferValue);

                        // Populate the Transfer modal fields
                        document.getElementById('product_nameTransfer').value = product_name;
                        document.getElementById('model_nameTransfer').value = model_name;
                        document.getElementById('snoTransfer').value = snoTransferValue;

                        // Show the Transfer modal
                        $('#transferModel').modal('toggle');
                    });
                });
            });

        </script>


    </div>


    <script src="components/loginsuccess.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#myTable').DataTable({
            });
        });


    </script>
    <!-- JavaScript to Populate Branch & Lab Dropdown -->
    <script>
        $(document).ready(function () {
            // Fetch branches on page load
            $.ajax({
                url: "components/fetch_branches.php",
                type: "GET",
                success: function (data) {
                    console.log("Branches received:", data); // Debugging
                    $("#branchTransfer").append(data);
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching branches:", error);
                }
            });

            // Fetch labs based on selected branch
            $("#branchTransfer").change(function () {
                var branch = $(this).val();
                $.ajax({
                    url: "components/fetch_labs.php",
                    type: "POST",
                    data: { branch: branch },
                    success: function (data) {
                        console.log("Labs received:", data); // Debugging
                        $("#labTransfer").html('<option value="" disabled selected>Select Lab</option>' + data);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching labs:", error);
                    }
                });
            });
        });

    </script>



</body>

</html>

<?php
ob_end_flush();
?>