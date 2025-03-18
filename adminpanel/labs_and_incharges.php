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
    <div class="container" id="successMessage" style=""></div>

    <?php
    include "components/navbar.php";
    ?>

    <?php include "components/sidebar.php";
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


        <!-- logic to add new admin -->
        <?php
        include "../_dbconnect.php";
        // to delete a user
        if (isset($_GET['delete'])) {
            $password = $_GET['delete'];
            $sql = "DELETE FROM `user_login` WHERE `password` = '$password' ;";
            $resultD = mysqli_query($conn, $sql);
            if ($resultD) {
                $_SESSION['message'] = "This Lab Incharge Account Deleted successfully.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "This Lab Incharge Account Deleted successfully.";
                $_SESSION['message_type'] = "success";
            }
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }


        // to edit a user 
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (isset($_POST['snoEdit'])) {
                // Get and sanitize input values
                $snoEdit = trim($_POST['snoEdit']); // original password used as identifier
                $name = trim($_POST['nameEdit'] ?? '');
                $branch = trim($_POST['branchEdit'] ?? '');
                $lab = trim($_POST['labEdit'] ?? '');
                $password = trim($_POST['passwordEdit'] ?? ''); // new password
        
                // If the new password differs from the original, check if it already exists
                if ($password !== $snoEdit) {
                    $stmtCheck = mysqli_prepare($conn, "SELECT COUNT(*) FROM `user_login` WHERE `password` = ?");
                    if ($stmtCheck) {
                        mysqli_stmt_bind_param($stmtCheck, "s", $password);
                        mysqli_stmt_execute($stmtCheck);
                        mysqli_stmt_bind_result($stmtCheck, $count);
                        mysqli_stmt_fetch($stmtCheck);
                        mysqli_stmt_close($stmtCheck);

                        if ($count > 0) {
                            $_SESSION['message'] = "Sorry, you can't use this password";
                            $_SESSION['message_type'] = "danger";
                            header("Location: " . $_SERVER['PHP_SELF']);
                            exit();
                        }
                    } else {
                        $_SESSION['message'] = "Database error during duplicate check.";
                        $_SESSION['message_type'] = "danger";
                        header("Location: " . $_SERVER['PHP_SELF']);
                        exit();
                    }
                }

                // Update the record using a prepared statement
                $stmt = mysqli_prepare($conn, "UPDATE `user_login` SET `name` = ?, `branch` = ?, `lab` = ?, `password` = ? WHERE `password` = ?");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssss", $name, $branch, $lab, $password, $snoEdit);
                    mysqli_stmt_execute($stmt);

                    if (mysqli_stmt_affected_rows($stmt) > 0) {
                        $_SESSION['message'] = "Lab Incharge details updated successfully.";
                        $_SESSION['message_type'] = "success";
                    } else {
                        $_SESSION['message'] = "Failed to update Lab Incharge Details due to an internal issue.";
                        $_SESSION['message_type'] = "danger";
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $_SESSION['message'] = "Database error during update.";
                    $_SESSION['message_type'] = "danger";
                }
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                // Retrieve and sanitize input values
                $name = trim($_POST["name"] ?? '');
                $branch = trim($_POST["branch"] ?? '');
                $lab = trim($_POST["lab"] ?? '');
                $password = trim($_POST["password"] ?? '');

                // Check if the password already exists in the database
                $stmtCheck = mysqli_prepare($conn, "SELECT COUNT(*) FROM `user_login` WHERE `password` = ?");
                if ($stmtCheck) {
                    mysqli_stmt_bind_param($stmtCheck, "s", $password);
                    mysqli_stmt_execute($stmtCheck);
                    mysqli_stmt_bind_result($stmtCheck, $count);
                    mysqli_stmt_fetch($stmtCheck);
                    mysqli_stmt_close($stmtCheck);

                    if ($count > 0) {
                        $_SESSION['message'] = "Sorry, you can't use this password";
                        $_SESSION['message_type'] = "danger";
                        header("Location: " . $_SERVER['PHP_SELF']);
                        exit();
                    }
                } else {
                    $_SESSION['message'] = "Database error during duplicate check.";
                    $_SESSION['message_type'] = "danger";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }

                // Insert new Lab Incharge record using a prepared statement
                $stmt = mysqli_prepare($conn, "INSERT INTO `user_login` (`name`, `branch`, `lab`, `password`) VALUES (?, ?, ?, ?)");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ssss", $name, $branch, $lab, $password);
                    mysqli_stmt_execute($stmt);

                    if (mysqli_stmt_affected_rows($stmt) > 0) {
                        $_SESSION['message'] = "New Lab Incharge added successfully.";
                        $_SESSION['message_type'] = "success";
                    } else {
                        $_SESSION['message'] = "Failed to add new Lab Incharge due to an internal issue.";
                        $_SESSION['message_type'] = "danger";
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $_SESSION['message'] = "Database error during insertion.";
                    $_SESSION['message_type'] = "danger";
                }
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }

        }
        ?>


        <!-- Edit Modal -->


        <style>
            @media (max-width: 700px) {

                /* Force Branch and Lab fields to appear on the same row */
                #editForm .form-group:nth-of-type(2),
                #editForm .form-group:nth-of-type(3) {
                    display: inline-block;
                    width: 43%;
                    box-sizing: border-box;
                    vertical-align: top;
                }

                /* Optional: add a small gap between the two fields */
                #editForm .form-group:nth-of-type(2) {
                    margin-right: 4%;
                }
            }
        </style>
        <!-- Edit Modal -->
        <div class="modal fade" id="editModel" tabindex="-1" role="dialog" aria-labelledby="editModelLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editModelLabel">Edit This User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="editForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="snoEdit" id="snoEdit">

                            <div class="form-group">
                                <label for="nameEdit">User Name</label>
                                <input type="text" class="form-control" id="nameEdit" name="nameEdit"
                                    placeholder="Enter User Name" required minlength="5" maxlength="20">
                            </div>

                            <div class="form-group">
                                <label for="branchEdit">Branch</label>
                                <select class="form-control" id="branchEdit" name="branchEdit" required>
                                    <option value="" disabled selected>Select Branch</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="labEdit">Lab</label>
                                <select class="form-control" id="labEdit" name="labEdit" required>
                                    <option value="" disabled selected>Select Lab</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="passwordEdit">User Password</label>
                                <input type="text" class="form-control" id="passwordEdit" name="passwordEdit"
                                    placeholder="Enter Password" minlength="5" maxlength="15" required>
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

        <style>
            /* Your mobile styling (max-width: 700px) */
            @media (max-width: 700px) {

                #addForm .form-group:nth-of-type(2),
                #addForm .form-group:nth-of-type(3) {
                    display: inline-block;
                    width: 43%;
                    box-sizing: border-box;
                    vertical-align: top;
                }

                /* Optional: add a small gap between the two fields */
                #addForm .form-group:nth-of-type(2) {
                    margin-right: 4%;
                }
            }
        </style>

        <!-- important container to add new data -->
        <div class="container my-4">
            <h3>Add new User</h3>
            <form id="addForm" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="name">Admin Name</label>
                        <input type="text" class="form-control" id="name" name="name" maxlength="20"
                            placeholder="Enter Name" minlength="5" maxlength="20" required>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="branch">Branch Name</label>
                        <select class="form-control" id="branch" name="branch" required>
                            <option value="" disabled selected>Select Branch</option>
                            <!-- Branch options will be loaded dynamically -->
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="lab">Lab Name</label>
                        <select name="lab" id="lab" class="form-control" required>
                            <option value="" disabled selected>Select Lab</option>
                            <!-- Lab options will be loaded dynamically -->
                        </select>
                    </div>



                    <div class="form-group col-md-3">
                        <label for="password">Password</label>
                        <input type="text" class="form-control" id="password" name="password" maxlength="15"
                            placeholder="Password" minlength="5" maxlength="15" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary my-2">Add</button>
            </form>
        </div>



        <!-- table to show the data from database -->
        <div class="container">
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">User Name</th>
                        <th scope="col">Branch</th>
                        <th scope="col">Lab Name</th>
                        <th scope="col">Password</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    $sql = "SELECT * FROM `user_login` WHERE (`branch` != 'INVENTORY' AND `branch` != 'INVENTORY_OFFICER') AND (`lab` != 'INVENTORY' AND `lab` != 'INVENTORY_OFFICER')";
                    $result = mysqli_query($conn, $sql);
                    $sno = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $sno++;
                        echo '
                        <tr>
                            <th scope="row">' . $sno . '</th>
                            <td>' . $row['name'] . '</td>
                            <td>' . $row['branch'] . '</td>
                            <td>' . $row['lab'] . '</td>
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
                        console.log("edit");

                        // Get the row containing the clicked edit button
                        let tr = e.target.closest("tr");
                        let name = tr.getElementsByTagName("td")[0].innerText;
                        let branch = tr.getElementsByTagName("td")[1].innerText;
                        let lab = tr.getElementsByTagName("td")[2].innerText;
                        let password = tr.getElementsByTagName("td")[3].innerText;

                        // Populate the text inputs
                        document.getElementById('nameEdit').value = name;
                        document.getElementById('branchEdit').value = branch;
                        document.getElementById('labEdit').value = lab;
                        document.getElementById('passwordEdit').value = password;

                        // Populate the select dropdowns
                        let branchSelect = document.querySelector('select[name="branch"]');
                        let labSelect = document.querySelector('select[name="lab"]');

                        // Set the branch select value
                        Array.from(branchSelect.options).forEach(option => {
                            if (option.value === branch) {
                                option.selected = true;
                            } else {
                                option.selected = false;
                            }
                        });

                        // Set the lab select value
                        Array.from(labSelect.options).forEach(option => {
                            if (option.value === lab) {
                                option.selected = true;
                            } else {
                                option.selected = false;
                            }
                        });

                        // Set the hidden input field value with the id of the clicked button
                        document.getElementById('snoEdit').value = e.target.id;

                        // Toggle the modal
                        $('#editModel').modal('toggle');
                    });
                });



                let deletes = document.getElementsByClassName('delete');
                Array.from(deletes).forEach(element => {
                    element.addEventListener("click", (e) => {
                        e.stopPropagation();
                        console.log("delete", e);
                        sno = e.target.id.substr(1,);

                        if (confirm("Press a button!")) {
                            console.log("yes");
                            window.location = `/try/adminpanel/labs_and_incharges.php?delete=${sno}`;
                        } else {
                            console.log("No");
                        }
                    });
                });
            });


        </script>


    </div>


    <script src="components/loginsuccess.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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
        $(document).ready(function () {
            // For Add Modal
            // Fetch branches on page load
            $.ajax({
                url: "components/fetch_branches.php",
                type: "GET",
                success: function (data) {
                    $("#branch").append(data);
                }
            });

            // Fetch labs based on selected branch for Add Modal
            $("#branch").change(function () {
                var branch = $(this).val();
                $.ajax({
                    url: "components/fetch_labs.php",
                    type: "POST",
                    data: { branch: branch },
                    success: function (data) {
                        $("#lab").html('<option value="" disabled selected>Select Lab</option>' + data);
                    }
                });
            });

            // For Edit Modal
            // Fetch branches on page load for Edit Modal
            $.ajax({
                url: "components/fetch_branches.php",
                type: "GET",
                success: function (data) {
                    $("#branchEdit").append(data);
                }
            });

            // Fetch labs based on selected branch for Edit Modal
            $("#branchEdit").change(function () {
                var branch = $(this).val();
                $.ajax({
                    url: "components/fetch_labs.php",
                    type: "POST",
                    data: { branch: branch },
                    success: function (data) {
                        $("#labEdit").html('<option value="" disabled selected>Select Lab</option>' + data);
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