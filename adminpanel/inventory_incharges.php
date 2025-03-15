
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
            if ($result) {
                $_SESSION['message'] = "Inventory Incharge has been Deleted successfully.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Inventory Incharge been Deleted successfully.";
                $_SESSION['message_type'] = "success";
            }
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }


        // to edit a user 
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (isset($_POST['snoEdit'])) {
                // Get data for editing
                $snoEdit = $_POST['snoEdit'];
                $name = $_POST['nameEdit'] ?? '';
                $branch = 'INVENTORY';
                $lab = 'INVENTORY';
                $password = $_POST['passwordEdit'] ?? '';

                // Update query
                $sql = "UPDATE `user_login` SET `name` = '$name',  `branch` = '$branch',  `lab` = '$lab',  `password` = '$password'  WHERE `password` = '$snoEdit';";

                $result = mysqli_query($conn, $sql);

                if ($result) {
                    $_SESSION['message'] = "Inventory Incharge details updated successfully.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Failed to update Inventory Incharge Details due to an internal issue.";
                    $_SESSION['message_type'] = "danger";
                }
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();

            } else {

                $name = $_POST["name"] ?? '';
                $branch = 'INVENTORY';
                $lab = 'INVENTORY';
                $password = $_POST["password"] ?? '';

                // Insert query
                $sql = "INSERT INTO `user_login` (`name`,`branch`,`lab`, `password`) VALUES ('$name', '$branch','$lab','$password')";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    $_SESSION['message'] = "New Incharge added successfully.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Failed to add new Inventory Incharge due to an internal issue.";
                    $_SESSION['message_type'] = "danger";
                }
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
        ?>


        <!-- Edit Modal -->



        <!-- Edit Modal -->
        <div class="modal fade" id="editModel" tabindex="-1" role="dialog" aria-labelledby="editModelLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModelLabel">Edit This Inventory Incharge</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="snoEdit" id="snoEdit">
                            <div class="form-group">
                                <label for="name">User Name</label>
                                <input type="text" class="form-control" id="nameEdit" name="nameEdit"
                                    placeholder="Enter Name">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="text" class="form-control" id="passwordEdit" name="passwordEdit"
                                    placeholder="Enter Password">
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
            <h4>Add New Inventory Incharge</h4>
            <form id="addForm" action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                <div class="form-row">

                    <div class="form-group col-md-5">
                        <label for="name">Inventory Incharge Name</label>
                        <input type="text" class="form-control" id="name" name="name" aria-describedby="emailHelp"
                            maxlength="20" placeholder="Enter Name">
                    </div>

                    <div class="form-group col-md-5">
                        <label for="password">Password</label>
                        <input type="text" class="form-control" id="password" name="password" maxlength="15"
                            placeholder="Password">
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
                        <th scope="col">Role</th>
                        <th scope="col">Password</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    $sql = "SELECT * FROM `user_login` WHERE `branch` = 'INVENTORY' AND `lab` = 'INVENTORY'";

                    $result = mysqli_query($conn, $sql);
                    $sno = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $sno++;
                        echo '
                        <tr>
                            <th scope="row">' . $sno . '</th>
                            <td>' . $row['name'] . '</td>
                            <td>' . 'Inventory Incharge' . '</td>
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
                        let password = tr.getElementsByTagName("td")[2].innerText;

                        // Populate the text inputs
                        document.getElementById('nameEdit').value = name;
                        document.getElementById('passwordEdit').value = password;

                        // Set the hidden input field value with the id of the clicked button
                        document.getElementById('snoEdit').value = e.target.id;

                        // Toggle the modal using Bootstrap's jQuery method
                        $('#editModel').modal('show');
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
                            window.location = `/try/adminpanel/inventory_incharges.php?delete=${sno}`;
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
            $('#myTable').DataTable();

        });
    </script>

</body>

</html>

<?php
ob_end_flush();
?>