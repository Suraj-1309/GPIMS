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

        <!-- connect to database  -->
        <?php
        include "../_dbconnect.php";
        ?>

        <!-- code for cancel button  -->
        <?php
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // Handle Cancellation
            if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['sno'])) {
                $sno = $_POST['sno'];
                $reason = trim($_POST['reason']);

                // Fetch the record from the return_request table
                $query = "SELECT * FROM `return_request` WHERE `sno` = '$sno'";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);

                    // Retrieve all needed fields from the original record
                    $product_name = $row['product_name'];
                    $type = $row['type'];
                    $rr_reg = $row['rr_reg'];
                    $purchase_date = $row['purchase_date'];
                    $got_it_from = $row['got_it_from'];
                    $unit_price = $row['unit_price'];
                    $overall_price = $row['overall_price'];
                    $units = $row['units'];
                    $branch = $row['branch'];
                    $lab = $row['lab'];
                    $rejected_by = 'Stock Manager';
                    $product_condition = $row['product_condition'];
                    // The cancel reason is coming from the modal input
        
                    // Insert the record into return_request_cancel table with the cancel reason and current date
                    $sql1 = "INSERT INTO `return_request_cancel` 
                (`product_name`, `type`, `rr_reg`, `purchase_date`, `got_it_from`, `unit_price`, `overall_price`, `units`, `branch`, `lab`, `rejected_by`, `product_condition`, `reason`, `cancel_date`)
                VALUES 
                ('$product_name', '$type', '$rr_reg', '$purchase_date', '$got_it_from', '$unit_price', '$overall_price', '$units', '$branch', '$lab', '$rejected_by', '$product_condition', '$reason', CURDATE())";
                    $result1 = mysqli_query($conn, $sql1);

                    // Delete the record from the return_request table
                    $delete_sql = "DELETE FROM `return_request` WHERE `sno` = '$sno'";
                    $delete_result = mysqli_query($conn, $delete_sql);

                    if ($delete_result && $result1) {
                        $_SESSION['popup_message'] = "Return request canceled and logged successfully.";
                        $_SESSION['popup_type'] = "success";
                    } else {
                        $_SESSION['popup_message'] = "Error: Failed to cancel return request or log the action.";
                        $_SESSION['popup_type'] = "danger";
                    }
                } else {
                    $_SESSION['popup_message'] = "Error: Return request not found.";
                    $_SESSION['popup_type'] = "danger";
                }

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
        ?>

        <?php
        // Handle Accept Button Click - Grant Permission to Stock Officer
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (isset($_POST['snoAccept'])) {

                $snoAccept = $_POST['snoAccept'];

                // Update the 'approved_by' or 'assigned_to' field to 'Stock Officer'
                $sql_update_permission = "UPDATE return_request SET permission = 'Stock Officer' WHERE sno = '$snoAccept'";
                $result_update_permission = mysqli_query($conn, $sql_update_permission);

                if ($result_update_permission) {
                    $_SESSION['popup_message'] = "Permission granted to Stock Officer.";
                    $_SESSION['popup_type'] = "success";
                } else {
                    $_SESSION['popup_message'] = "Error granting permission.";
                    $_SESSION['popup_type'] = "danger";
                }

                // Redirect back to the page
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }

        ?>

        <!-- Accept Confirmation Modal -->
        <div class="modal fade" id="acceptModal" tabindex="-1" role="dialog" aria-labelledby="acceptModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="acceptModalLabel">Confirm Return Request</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to send this return request for approval?</p>
                    </div>
                    <div class="modal-footer">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="snoAccept" id="snoAccept">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Accept </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-3 ml-5 pl-4">
        <h2>Request To Return Items</h2>
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
                    padding-top: 0%;
                    margin-bottom: -5%;
                    padding-bottom: -5%;

                }
            }
        </style>

        <div class="container">
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Type</th>
                        <th scope="col">Units</th>
                        <th scope="col">Stock Reg Page/Sno</th>
                        <th scope="col">Return By</th>
                        <th scope="col">Reason</th>
                        <th scope="col">Return Req Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM `return_request` WHERE `permission` = 'Stock Manager' ORDER BY `sno` DESC";
                    $result = mysqli_query($conn, $sql);
                    $sno = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $sno++;
                        echo '
                            <tr>
                                <th scope="row">' . $sno . '</th>
                                <td>' . $row['product_name'] . '</td>
                                <td>' . $row['type'] . '</td>
                                <td>' . $row['units'] . '</td>
                                <td>' . $row['rr_reg'] . '</td>
                                <td>' . $row['branch'] . " " . $row['lab'] . '</td>
                                <td>' . $row['reason'] . '</td>
                                <td>' . $row['return_date'] . '</td>
                                <td>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <button class="accept btn btn-sm btn-success" id="' . $row['sno'] . '">Accept</button>
                                        <button class="deprecate btn btn-sm btn-danger" id="d' . $row['sno'] . '">Cancel</button>
                                    </div>
                                </td>
                            </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Cancel Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!-- Begin Form -->
                    <form id="deleteForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <!-- Header -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Cancel Confirmation</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body">
                            <p>Are you sure you want to cancel the request of return?</p>
                            <!-- Hidden field to store the sno -->
                            <input type="hidden" name="sno" id="deleteSno">
                            <input type="hidden" name="action" value="delete">
                            <!-- Textarea for Reason -->
                            <div class="form-group">
                                <label for="reason">Please provide a reason (max 150 characters):</label>
                                <textarea class="form-control" id="reason" name="reason" maxlength="150"
                                    required></textarea>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer">
                            <!-- Buttons -->
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button id="confirmDeleteBtn" type="submit" class="btn btn-danger">Yes, Reject</button>
                        </div>
                    </form>
                    <!-- End Form -->
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Accept Button Logic
            let acceptButtons = document.querySelectorAll('button.accept');
            Array.from(acceptButtons).forEach(function (button) {
                button.addEventListener('click', function (e) {
                    e.stopPropagation();
                    console.log("Accept button clicked");

                    // Get the row and its cells
                    let tr = e.target.closest('tr');
                    let tds = tr.getElementsByTagName('td');

                    // Assuming the first column holds a unique identifier (adjust if needed)
                    let sno = e.target.id; // or tds[0].innerText if the ID is stored there

                    // Set the hidden input value in the modal form
                    document.getElementById('snoAccept').value = sno;

                    // Show the confirmation modal
                    $('#acceptModal').modal('show');
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
            $('.deprecate').on('click', function () {
                var sno = $(this).attr('id').substring(1);
                $('#deleteSno').val(sno);
                // Clear the textarea
                $('#reason').val('');
                $('#deleteModal').modal('show');
            });
        });
    </script>
</body>

</html>



<?php
ob_end_flush();
?>