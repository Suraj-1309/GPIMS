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
            $sno = $_GET['delete']; // Correctly capture the sno from GET
            $sql = "DELETE FROM `all_labs` WHERE `sno` = '$sno'";
            $resultD = mysqli_query($conn, $sql);

            if ($resultD) {
                $_SESSION['message'] = "This Lab has been deleted successfully.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Failed to delete lab due to an internal issue.";
                $_SESSION['message_type'] = "danger";
            }
            header("Location: " . $_SERVER['PHP_SELF']);// Redirect back to labs.php after deletion
            exit();
        }



        // to edit a user 
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
                $branch = $_POST["branch"] ?? '';
                $lab = $_POST["lab"] ?? '';

                // Insert query
                $sql = "INSERT INTO `all_labs` (`branch_name`,`lab_name`) VALUES ('$branch','$lab')";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    $_SESSION['message'] = "New Lab added To Inventory successfully.";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "Failed to add new Lab due to an internal issue.";
                    $_SESSION['message_type'] = "danger";
                }
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
        }
        ?>

        <!-- important container to add new data -->

        <div class="container my-4">
            <h3>Add New Lab</h3>
            <form id="addForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="branch">Branch Name</label>
                        <input type="text" class="form-control" id="branch" name="branch"
                            placeholder="Enter Branch Name">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="lab">Lab Name</label>
                        <input type="text" class="form-control" id="lab" name="lab" placeholder="Enter Lab Name">
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
                        <th scope="col">Branch</th>
                        <th scope="col">Lab Name</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    $sql = "SELECT DISTINCT * FROM `all_labs`";
                    $result = mysqli_query($conn, $sql);
                    $sno = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $sno++;
                        echo '
                        <tr>
                            <th scope="row">' . $sno . '</th>
                            <td>' . $row['branch_name'] . '</td>
                            <td>' . $row['lab_name'] . '</td>
                            <td>
                                <form action="../branch/index.php" method="post" style="display:inline;">
                                    <input type="hidden" name="branch" value="' . $row['branch_name'] . '">
                                    <input type="hidden" name="lab" value="' . $row['lab_name'] . '">
                                    <button type="submit" class="edit btn btn-sm btn-primary">View This Lab</button>
                                </form>
                                <button class="delete btn btn-sm btn-danger" id="d' . $row['sno'] . '">Delete</button>
                            </td>
                        </tr>';
                    }                    
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', (event) => {

                let deletes = document.getElementsByClassName('delete');
                Array.from(deletes).forEach(element => {
                    element.addEventListener("click", (e) => {
                        e.stopPropagation();
                        // Extract the sno by removing the first character ('d')
                        let sno = e.target.id.substr(1);

                        if (confirm("Are you sure you want to delete this lab?")) {
                            // Redirect to labs.php with the delete parameter
                            window.location = `labs.php?delete=${sno}`;
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
            $('#myTable').DataTable();

        });
    </script>
</body>

</html>
<?php
ob_end_flush();
?>