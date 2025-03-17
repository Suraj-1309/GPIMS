<?php
ob_start();

session_start();

// Check if the user is logged in and branch and lab are set
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['name'])) {
    // Redirect to login page if not logged in
    header("Location: ../index.php");
    exit();
}
$name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="sidebar.css">
    <?php include "components/style.php" ?>
</head>



<body>


    <div class="container" id="successMessage" style="">

    </div>



    <?php
    include "components/navbar.php";
    ?>

    <?php
    include "components/sidebar.php";
    ?>

    <div id="admin" class="right">

        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-' . $_SESSION['message_type'] . ' alert-dismissible fade show" role="alert">
            <strong>Success!</strong> ' . $_SESSION['message'] . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                setTimeout(() => {
                    let alert = document.querySelector(".alert");
                    if (alert) {
                        alert.style.display = "none";
                    }
                }, 1500);
            });
        </script>


        <?php
        include "../_dbconnect.php";

        // Delete admin if 'delete' parameter is set in GET
        if (isset($_GET['delete'])) {
            // Ideally, use a unique identifier (like an admin ID) instead of the password.
            $deleteParam = $_GET['delete'];

            // Prepare a deletion statement
            $stmt = mysqli_prepare($conn, "DELETE FROM `admin_login` WHERE `password` = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $deleteParam);
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $_SESSION['message'] = "Admin has been deleted successfully.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "No matching admin found or deletion failed.";
                    $_SESSION['message_type'] = "danger";
                }
                mysqli_stmt_close($stmt);
            } else {
                $_SESSION['message'] = "Database error during deletion.";
                $_SESSION['message_type'] = "danger";
            }
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        // Handle POST requests for editing or adding a new admin
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            // Add CSRF token validation here for further protection
        
            if (isset($_POST['snoEdit'])) {
                // Editing an existing admin
                $snoEdit = $_POST['snoEdit']; // the original password used as an identifier
                $title   = trim($_POST["titleEdit"] ?? '');
                $description = trim($_POST["descriptionEdit"] ?? '');
                
                // In this example, we're not hashing passwords.
                $newPassword = $description;
            
                // If the new password is different from the original,
                // check if it already exists in the database.
                if ($newPassword !== $snoEdit) {
                    $stmtCheck = mysqli_prepare($conn, "SELECT COUNT(*) FROM `admin_login` WHERE `password` = ?");
                    mysqli_stmt_bind_param($stmtCheck, "s", $newPassword);
                    mysqli_stmt_execute($stmtCheck);
                    mysqli_stmt_bind_result($stmtCheck, $count);
                    mysqli_stmt_fetch($stmtCheck);
                    mysqli_stmt_close($stmtCheck);
            
                    if ($count > 0) {
                        $_SESSION['message'] = "Sorry but you can't use this password";
                        $_SESSION['message_type'] = "danger";
                        header("Location: " . $_SERVER['PHP_SELF']);
                        exit();
                    }
                }
            
                // Proceed with updating the record using the old password as identifier.
                $stmt = mysqli_prepare($conn, "UPDATE `admin_login` SET `name` = ?, `password` = ? WHERE `password` = ?");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sss", $title, $newPassword, $snoEdit);
                    mysqli_stmt_execute($stmt);
            
                    if (mysqli_stmt_affected_rows($stmt) > 0) {
                        $_SESSION['message'] = "Admin details updated successfully.";
                        $_SESSION['message_type'] = "success";
                    } else {
                        $_SESSION['message'] = "Update failed or no changes made.";
                        $_SESSION['message_type'] = "danger";
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $_SESSION['message'] = "Database error during update.";
                    $_SESSION['message_type'] = "danger";
                }
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
            else {
                // Enable MySQLi exceptions for easier error handling
                mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

                if ($_SERVER['REQUEST_METHOD'] == "POST" && !isset($_POST['snoEdit'])) {
                    // Adding a new admin
                    $title = trim($_POST["title"] ?? '');
                    $description = trim($_POST["description"] ?? '');

                    try {
                        $stmt = mysqli_prepare($conn, "INSERT INTO `admin_login` (`name`, `password`) VALUES (?, ?)");
                        if (!$stmt) {
                            throw new Exception("Database error during insertion: " . mysqli_error($conn));
                        }
                        mysqli_stmt_bind_param($stmt, "ss", $title, $description);
                        mysqli_stmt_execute($stmt);

                        if (mysqli_stmt_affected_rows($stmt) > 0) {
                            $_SESSION['message'] = "New admin added successfully.";
                            $_SESSION['message_type'] = "success";
                        } else {
                            $_SESSION['message'] = "Failed to add new admin.";
                            $_SESSION['message_type'] = "danger";
                        }
                        mysqli_stmt_close($stmt);
                    } catch (mysqli_sql_exception $e) {
                        if ($e->getCode() == 1062) { // Duplicate entry error code
                            $_SESSION['message'] = "sorry but you can't use this password";
                            $_SESSION['message_type'] = "danger";
                        } else {
                            $_SESSION['message'] = "Database error: " . $e->getMessage();
                            $_SESSION['message_type'] = "danger";
                        }
                    }
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

            }
        }
        ?>


        <!-- Edit Modal -->
        <div class="modal fade" id="editModel" tabindex="-1" role="dialog" aria-labelledby="editModelLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModelLabel">Edit this Admin Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form id="editForm" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="snoEdit" id="snoEdit">
                            <div class="form-group">
                                <label for="title">Admin Name</label>
                                <input type="text" class="form-control" id="titleEdit" name="titleEdit" minlength="5"
                                    maxlength="20" aria-describedby="emailHelp" placeholder="Edit Admin Name" required>
                                <small id="emailHelp" class="form-text text-muted">We'll never share your Info with
                                    anyone
                                    else.</small>
                            </div>
                            <div class="form-group">
                                <label for="desc">Admin Password</label>
                                <input type="text" class="form-control" id="descriptionEdit" name="descriptionEdit"
                                    minlength="5" maxlength="15" maxlength=15 placeholder="Password" required>
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

        <!-- important container to add new data -->
        <!-- Add Admin Form -->
        <div class="container my-4">
            <h3>Add new Admin</h3>
            <form id="addForm" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                <input type="hidden" name="fragment" id="fragmentAdd" value="#admin">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="title">Admin Name</label>
                        <input type="text" class="form-control" id="title" name="title" aria-describedby="emailHelp"
                            minlength="5" maxlength="20" placeholder="Enter Admin Name" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="description">Admin Password</label>
                        <input type="text" class="form-control" id="description" name="description" minlength="5"
                            maxlength="15" placeholder="Password" required>
                    </div>
                </div>
                <small id="emailHelp" class="form-text text-muted" style="margin-top: -10px;">Please note the new
                    admin will
                    have the same authority as you</small>
                <button type="submit" class="btn btn-primary my-2">Add</button>
            </form>
        </div>

        <!-- table to show the data from database -->
        <div class="container">
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Admin Name</th>
                        <th scope="col">Password</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    $sql = "SELECT * FROM `admin_login` ";
                    $result = mysqli_query($conn, $sql);
                    $sno = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $sno++;
                        echo '
                        <tr>
                            <th scope="row">' . $sno . '</th>
                            <td>' . $row['name'] . '</td>
                            <td>' . $row['password'] . '</td>
                            <td> <button class="edit btn btn-sm btn-primary" id="' . $row['password'] . '">Edit</button> <button class="delete btn btn-sm btn-primary" id="d' . $row['password'] . '">Delete</button>
                        </tr>';

                    }

                    ?>
                </tbody>
            </table>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', (event) => {
                let edits = document.getElementsByClassName('edit');
                Array.from(edits).forEach(element => {
                    element.addEventListener("click", (e) => {
                        e.stopPropagation();
                        console.log("edit",);
                        tr = e.target.parentNode.parentNode;
                        title = tr.getElementsByTagName("td")[0].innerText;
                        description = tr.getElementsByTagName("td")[1].innerText; // Fixed typo from 'discription' to 'description'
                        console.log(title, description);

                        document.getElementById('titleEdit').value = title; // Ensure you select the element by ID
                        document.getElementById('descriptionEdit').value = description; // Ensure you select the element by ID

                        console.log(e.target.id);
                        document.getElementById('snoEdit').value = e.target.id; // Set the hidden input field value

                        $('#editModel').modal('toggle');
                    });
                });

                let deletes = document.getElementsByClassName('delete');
                Array.from(deletes).forEach(element => {
                    element.addEventListener("click", (e) => {
                        e.stopPropagation();
                        console.log("delete", e);
                        sno = e.target.id.substr(1,);

                        if (confirm("Are You Sure To Delete This Account!")) {
                            console.log("yes");
                            window.location = `/try/adminpanel/adminuser.php?delete=${sno}`;
                        } else {
                            console.log("No");
                        }
                    });
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