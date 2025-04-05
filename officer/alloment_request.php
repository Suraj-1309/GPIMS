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


    <div id="admin" class="right">
        <div class="p-3 ml-5 pl-4">
            <h2>Allotment Requests</h2>
        </div>
        <style>
            h2{
                padding-top: 2%;
                padding-bottom: -5%;
                padding-left: 1%;
                margin-bottom: -2%;
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

        <!-- logic to add new admin -->
        <?php
        include "../_dbconnect.php";

        // Process POST requests for both Accept and Reject actions.
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sno']) && isset($_POST['action'])) {
            $sno = mysqli_real_escape_string($conn, $_POST['sno']);
            $action = mysqli_real_escape_string($conn, $_POST['action']);

            // Retrieve the allotment record.
            $query = "SELECT * FROM allotment WHERE sno = '$sno'";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);

                if ($action === 'accept') {
                    // For accept, update the record’s permission to 'admin'
                    $update_sql = "UPDATE allotment SET permission = 'admin' WHERE sno = '$sno'";
                    if (mysqli_query($conn, $update_sql)) {
                        $_SESSION['popup_message'] = 'Item accepted. Permission updated to admin.';
                        $_SESSION['popup_type'] = 'success';
                    } else {
                        $_SESSION['popup_message'] = 'Error updating item permission.';
                        $_SESSION['popup_type'] = 'danger';
                    }
                } elseif ($action === 'reject') {
                    // For reject, get the reason from POST
                    $reason = isset($_POST['reason']) ? mysqli_real_escape_string($conn, $_POST['reason']) : '';

                    // Calculate overall_price as unit_price * units.
                    $unit_price = $row['unit_price'];
                    $units = $row['units'];
                    $overall_price = $unit_price * $units;

                    // Insert record into allotment_reject including the reason.
                    $insert_sql = "INSERT INTO allotment_reject 
                        (sno, product_name, `type`, rr_reg, purchase_date, got_it_from, srno, bill_date, unit_price, overall_price, units, branch, lab, rejected_by, reason) 
                        VALUES ('" . $row['sno'] . "','" . $row['product_name'] . "','" . $row['type'] . "','" . $row['rr_reg'] . "',
                            '" . $row['purchase_date'] . "','" . $row['got_it_from'] . "','" . $row['srno'] . "','" . $row['bill_date'] . "',
                            '$unit_price','$overall_price','$units','" . $row['branch'] . "','" . $row['lab'] . "','Stock Officer',
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
                }
            } else {
                $_SESSION['popup_message'] = 'Item not found.';
                $_SESSION['popup_type'] = 'danger';
            }
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
        ?>

        <div class="container mt-4">
            <!-- Table -->
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
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM `allotment` WHERE `permission` = 'Officer' ORDER BY `purchase_date` DESC";
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
                            <td>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <button class="btn btn-success accept-button" data-sno="' . $row['sno'] . '" data-toggle="modal" data-target="#acceptModal">Accept</button>
                                    <button class="btn btn-danger reject-button" data-sno="' . $row['sno'] . '" data-toggle="modal" data-target="#rejectModal">Reject</button>
                                </div>
                            </td>
                        </tr>';
                    }
                    ?>
                </tbody>
            </table>

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
                            <form id="acceptForm" method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
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
                            <form id="rejectForm" method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
                                <input type="hidden" name="sno" id="hiddenRejectSno">
                                <!-- Add a textarea for rejection reason -->
                                <div class="form-group">
                                    <label for="reason">Reason for Rejection:</label>
                                    <textarea name="reason" id="reason" class="form-control"
                                        placeholder="Enter reason for rejection" required></textarea>
                                </div>
                                <!-- Hidden action field to differentiate reject -->
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

        <!-- JavaScript to populate hidden field for Accept and Reject Modals -->
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