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
    <div class="p-3 ml-5 pl-4">
            <h2 class="mt-4"><?php echo "$_SESSION[branch] $_SESSION[lab] Deprecated Stock"; ?></h2>
        </div>
        <style>
            h2 {
                padding-left: 1%;
                margin-bottom: -1%;
            }

            @media (max-width: 767px) {

                h2 {
                    text-align: left;
                    margin-left: -27px;
                    font-size: x-large;
                    font-weight: 900;
                    padding-top: 0%;
                    margin-bottom: -5%;
                    padding-bottom: -5%;

                }
            }
        </style>

        <!-- connect to database  -->
        <?php
        include "../_dbconnect.php";
        ?>

        <?php
        // Transfer and  Edit logic
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            // 1. Edit Functionality (Modal Edit)
            if (isset($_POST['snoEdit'])) {
                // Retrieve and trim inputs
                $sno = $_POST['snoEdit'];
                $restore_units = (int) $_POST['unitsEdit'];
                $use_for = $_POST['use_forEdit']; // Reason or details of the "use"
        
                // Retrieve the record from branch_deprecate using the provided sno
                $query = "SELECT * FROM branch_deprecate WHERE sno = '$sno'";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $current_units = (int) $row['units'];

                    // Ensure that we are not trying to restore more units than available
                    if ($restore_units > $current_units) {
                        $_SESSION['popup_message'] = "Restore units cannot exceed available deprecated units ($current_units).";
                        $_SESSION['popup_type'] = "danger";
                        header("Location: " . $_SERVER['PHP_SELF']);
                        exit();
                    }

                    // Retrieve item details from branch_deprecate record
                    $product_name = $row['product_name'];
                    $type = $row['type'];
                    $rr_reg = $row['rr_reg'];
                    $allotment_date = $row['allotment_date'];
                    $branch = $row['branch'];
                    $lab = $row['lab'];
                    $unit_price = $row['unit_price'];
                    $purchase_date = $row['purchase_date'];
                    $got_it_from = $row['got_it_from'];

                    // Calculate the overall price for the restored units
                    $restore_overall_price = $unit_price * $restore_units;

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
                        // Existing record found: update its units and overall_price.
                        $existing = mysqli_fetch_assoc($check_result);
                        $new_units = (int) $existing['units'] + $restore_units;
                        $new_overall_price = $unit_price * $new_units;
                        $update_sql = "UPDATE branch_items 
                           SET units = '$new_units', unit_price = '$new_overall_price'
                           WHERE sno = '" . $existing['sno'] . "'";
                        mysqli_query($conn, $update_sql);
                    } else {
                        // No matching record found: insert a new record into branch_items.
                        // For the new record, set allotment_date to today's date.
                        $new_allotment_date = date("Y-m-d");
                        $insert_sql = "INSERT INTO branch_items 
                           (product_name, `type`, rr_reg, allotment_date, branch, lab, unit_price, overall_price, units, purchase_date, got_it_from)
                           VALUES 
                           ('$product_name', '$type', '$rr_reg', '$new_allotment_date', '$branch', '$lab', '$unit_price', '$restore_overall_price', '$restore_units', '$purchase_date', '$got_it_from')";
                        mysqli_query($conn, $insert_sql);
                    }

                    // Update the branch_deprecate record: subtract the restored units.
                    $remaining_units = $current_units - $restore_units;
                    if ($remaining_units <= 0) {
                        // If all units have been restored, delete the record from branch_deprecate.
                        $delete_sql = "DELETE FROM branch_deprecate WHERE sno = '$sno'";
                        mysqli_query($conn, $delete_sql);
                    } else {
                        // Otherwise, update the record with the remaining units.
                        $update_deprecate_sql = "UPDATE branch_deprecate SET units = '$remaining_units' WHERE sno = '$sno'";
                        mysqli_query($conn, $update_deprecate_sql);
                    }

                    $_SESSION['popup_message'] = "Item restored successfully from deprecated stock.";
                    $_SESSION['popup_type'] = "success";
                } else {
                    $_SESSION['popup_message'] = "Deprecated item not found.";
                    $_SESSION['popup_type'] = "danger";
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
                $permission = "Stock Officer";
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
                                <button type="submit" class="btn btn-primary">Return Items</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>


        <div class="container">
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Type</th>
                        <th scope="col">Stock Reg Page/Sno</th>
                        <th scope="col">Deprecate Date</th>
                        <th scope="col">units</th>
                        <th scope="col">Current Condition</th>
                        <th scope="col">Reason</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM `branch_deprecate` WHERE `branch` = '$_SESSION[branch]' AND `lab` = '$_SESSION[lab]' ORDER BY `sno` DESC";
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
                                <td>' . $row['deprecate_date'] . '</td>
                                <td>' . $row['units'] . '</td>
                                <td>' . $row['current_condition'] . '</td>
                                <td>' . $row['reason'] . '</td>
                                <td>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <button class="edit btn btn-sm btn-primary" id="' . $row['sno'] . '">Add Back</button>
                                        <button class="return btn btn-sm btn-primary" id="r' . $row['sno'] . '">Return Item</button>
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
            var dtOptions = {};
            // Check if the viewport width is 767px or less (mobile)
            if ($(window).width() <= 767) {
                dtOptions.lengthChange = false;
            }

            // Initialize DataTable with the options
            $('#myTable').DataTable(dtOptions);
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
                    let allotment_date = tds[3].innerText;

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