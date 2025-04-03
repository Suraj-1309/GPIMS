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
            <h2>Allocated Items to Lab</h2>
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

        <!-- code to reject the allotment request  -->
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'reject' && isset($_POST['sno']) && isset($_POST['reason'])) {
            // Sanitize inputs
            $sno = mysqli_real_escape_string($conn, $_POST['sno']);
            $reason = mysqli_real_escape_string($conn, $_POST['reason']);

            // Fetch the record from allotment table
            $query = "SELECT * FROM allotment WHERE sno = '$sno'";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                // Calculate overall_price as unit_price * units.
                $unit_price = $row['unit_price'];
                $units = $row['units'];
                $overall_price = $unit_price * $units;

                // Insert record into allotment_reject including the reason.
                $insert_sql = "INSERT INTO allotment_reject 
                    (product_name, `type`, rr_reg, purchase_date, got_it_from, srno, bill_date, unit_price, overall_price, units, branch, lab, rejected_by, reason) 
                    VALUES (
                        '" . $row['product_name'] . "','" . $row['type'] . "',
                        '" . $row['rr_reg'] . "','" . $row['purchase_date'] . "','" . $row['got_it_from'] . "',
                        '" . $row['srno'] . "','" . $row['bill_date'] . "','$unit_price','$overall_price',
                        '$units','" . $row['branch'] . "','" . $row['lab'] . "','Lab Incharge',
                        '$reason'
                    )";

                if (mysqli_query($conn, $insert_sql)) {
                    // On successful insert, delete the record from allotment.
                    $delete_sql = "DELETE FROM allotment WHERE sno = '$sno'";
                    if (mysqli_query($conn, $delete_sql)) {
                        $_SESSION['popup_message'] = 'Item rejected and moved to allotment_reject.';
                        $_SESSION['popup_type'] = 'success';
                    } else {
                        $_SESSION['popup_message'] = 'Item rejected but error deleting original record.';
                        $_SESSION['popup_type'] = 'warning';
                    }
                } else {
                    $_SESSION['popup_message'] = 'Error rejecting item.';
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



        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'accept' && isset($_POST['sno']) && isset($_POST['used_for'])) {
            // Sanitize inputs.
            $sno = mysqli_real_escape_string($conn, $_POST['sno']);
            $used_for = mysqli_real_escape_string($conn, $_POST['used_for']);

            // Fetch the record from the allotment table.
            $query = "SELECT * FROM allotment WHERE sno = '$sno'";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);

                // Build the insert query into rr_allocate_items.
                // Note: allotment_date is defaulted by MySQL.
                $insert_sql = "INSERT INTO rr_allocate_items 
                        (product_name, `type`, rr_reg, branch, lab, unit_price, units, used_for)
                        VALUES (
                            '" . $row['product_name'] . "',
                            '" . $row['type'] . "',
                            '" . $row['rr_reg'] . "',
                            '" . $row['branch'] . "',
                            '" . $row['lab'] . "',
                            '" . $row['unit_price'] . "',
                            '" . $row['units'] . "',
                            '$used_for'
                        )";

                if (mysqli_query($conn, $insert_sql)) {
                    // Get 'got_it_from' from the allotment record, not the modal.
                    $got_it_from = $row['got_it_from'];
                    // Get 'current_condition' from the modal (via POST).
                    $current_condition = mysqli_real_escape_string($conn, $_POST['current_condition']);


                    // Insert into rr_recevied_branch. Note: allotment_date will be defaulted by MySQL.
                    $insert_sql2 = "INSERT INTO rr_recevied_branch 
                            (product_name, `type`, got_it_from, current_condition, rr_reg, branch, lab, unit_price, units)
                            VALUES (
                                '" . $row['product_name'] . "',
                                '" . $row['type'] . "',
                                '$got_it_from',
                                '$current_condition',
                                '" . $row['rr_reg'] . "',
                                '" . $row['branch'] . "',
                                '" . $row['lab'] . "',
                                '" . $row['unit_price'] . "',
                                '" . $row['units'] . "'
                            )";

                    if (mysqli_query($conn, $insert_sql2)) {
                        // Now, insert into branch_items.
                        $insert_sql3 = "INSERT INTO branch_items 
                            (product_name, `type`, rr_reg, branch, lab, unit_price, units, purchase_date, got_it_from , current_condition)
                            VALUES (
                                '" . $row['product_name'] . "',
                                '" . $row['type'] . "',
                                '" . $row['rr_reg'] . "',
                                '" . $row['branch'] . "',
                                '" . $row['lab'] . "',
                                '" . $row['unit_price'] . "',
                                '" . $row['units'] . "',
                                '" . $row['purchase_date'] . "',
                                '$got_it_from',
                                '$current_condition'
                            )";

                        if (mysqli_query($conn, $insert_sql3)) {
                            // Now delete the record from the allotment table.
                            $delete_sql = "DELETE FROM allotment WHERE sno = '" . $row['sno'] . "'";
                            if (mysqli_query($conn, $delete_sql)) {
                                $_SESSION['popup_message'] = "Item accepted and moved to all tables; row deleted from allotment.";
                                $_SESSION['popup_type'] = "success";
                            } else {
                                $_SESSION['popup_message'] = "Item accepted into all tables but error deleting row from allotment.";
                                $_SESSION['popup_type'] = "warning";
                            }
                        } else {
                            $_SESSION['popup_message'] = "Item accepted into rr_allocate_items and rr_recevied_branch but error inserting into branch_items.";
                            $_SESSION['popup_type'] = "warning";
                        }
                    } else {
                        $_SESSION['popup_message'] = "Item accepted but error inserting into rr_recevied_branch.";
                        $_SESSION['popup_type'] = "warning";
                    }
                } else {
                    $_SESSION['popup_message'] = "Error accepting item into rr_allocate_items.";
                    $_SESSION['popup_type'] = "danger";
                }

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();

            }
        }

        ?>

        <div class="container mt-4">
            <!-- Table (Example) -->
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Type</th>
                        <th scope="col">Stock Reg Page/ Sno.</th>
                        <th scope="col">Unit Price</th>
                        <th scope="col">Units</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $branch = $_SESSION['branch'];
                    $lab = $_SESSION['lab'];
                    $sql = "SELECT * FROM `allotment` WHERE `branch` = '$_SESSION[branch]' AND `lab` = '$_SESSION[lab]' AND `permission` = 'labincharge' ORDER BY `sno` DESC";
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
                    <td>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <button class="btn btn-success accept-button" data-sno="' . $row['sno'] . '">Accept</button>
                            <button class="btn btn-danger reject-button" data-sno="' . $row['sno'] . '">Reject</button>
                        </div>
                    </td>
                </tr>';
                    }
                    ?>
                </tbody>
            </table>

            <!-- Bootstrap Modal for Empty Input Warning -->
            <div class="modal fade" id="emptyInputModal" tabindex="-1" aria-labelledby="emptyInputModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="emptyInputModalLabel">Input Required</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Please enter the Record Reg page value before accepting.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accept Modal -->
            <div class="modal fade" id="acceptModal" tabindex="-1" role="dialog" aria-labelledby="acceptModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="acceptForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <!-- Hidden fields to mark the action and pass the record identifiers -->
                            <input type="hidden" name="action" value="accept">
                            <input type="hidden" name="sno" id="acceptHiddenSno">
                            <input type="hidden" name="intent_reg" id="acceptIntentReg">


                            <div class="modal-header">
                                <h5 class="modal-title" id="acceptModalLabel">Acceptance Details</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- This textarea collects the "Why I need this store item" value -->
                                <div class="form-group">
                                    <label for="currentCondition">Current Condition</label>
                                    <input type="text" name="current_condition" id="currentCondition"
                                        class="form-control" maxlength="50" required>
                                </div>
                                <div class="form-group">
                                    <label for="usedFor">Why do you need this store item? (max 200 characters)</label>
                                    <textarea name="used_for" id="usedFor" class="form-control" maxlength="200"
                                        required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Accept</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Reject Confirmation Modal -->
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
                            Are you sure you want to reject this item?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button id="confirmRejectBtn" type="button" class="btn btn-danger">Yes, Reject</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reject Reason Modal -->
            <div class="modal fade" id="rejectReasonModal" tabindex="-1" role="dialog"
                aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="rejectReasonForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <input type="hidden" name="action" value="reject">
                            <div class="modal-header">
                                <h5 class="modal-title" id="rejectReasonModalLabel">Rejection Reason</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="rejectReason">Please provide a reason for rejection (max 150
                                        characters):</label>
                                    <textarea name="reason" id="rejectReason" class="form-control" maxlength="150"
                                        required></textarea>
                                </div>
                                <!-- Hidden field to pass the sno value -->
                                <input type="hidden" name="sno" id="hiddenReasonSno">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Submit Rejection</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>


        <!-- Bootstrap Modal for Alert -->
        <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <p id="alertMessage"></p>
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


    <!-- jQuery code to handle the modal chain -->
    <script>
        $(document).ready(function () {
            $('.reject-button').on('click', function () {
                var sno = $(this).data('sno');
                $('#hiddenReasonSno').val(sno);
                $('#rejectModal').modal('show');
            });
            $('#confirmRejectBtn').on('click', function () {
                $('#rejectModal').modal('hide');
                $('#rejectReasonModal').modal('show');
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = document.getElementById('myTable');

            table.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('accept-button')) {
                    // Set the hidden fields in the accept modal.
                    document.getElementById('acceptHiddenSno').value = e.target.getAttribute('data-sno');

                    // Show the Accept modal.
                    $('#acceptModal').modal('show');
                }
            });
        });
    </script>



</body>

</html>

<?php
ob_end_flush();
?>