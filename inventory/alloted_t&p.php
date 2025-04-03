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
    <style>
        .right {
            position: relative;
            margin-top: 60px;
        }
    </style>
</head>
<?php include "components/style.php" ?>


<body>

    <?php include "components/popup.php" ?>


    <?php include "../adminpanel/components/navbar.php" ?>

    <!-- sidebar  -->
    <?php include "components/sidebar.php" ?>


    <div id="admin" class="right">
        <div class="p-3 ml-5 pl-4">
            <h2>Record of Alloted T&P items</h2>
        </div>
        <style>
            h2{
                padding-left: 1%;
                margin-bottom: -1%;
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
        <!-- logic to add new admin -->
        <?php
        include "../_dbconnect.php";
        ?>


        <div class="container">
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Product Name</th>
                        <th scope="col">Reg Page/S.NO</th>

                        <th scope="col">Allotment Date</th>
                        <th scope="col">Given To</th>
                        <th scope="col">Units</th>
                        <th scope="col">Comment</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM `rr_allocate_items` WHERE `type` = 'T&P Item' ORDER BY `sno` DESC";
                    $result = mysqli_query($conn, $sql);
                    $sno = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $sno++;

                        // Format purchase_date and bill_date to dd-mm-yyyy
                        $allotment_date = !empty($row['allotment_date']) ? date('d-m-Y', strtotime($row['allotment_date'])) : '';

                        echo '<tr>
                            <th scope="row">' . $sno . '</th>
                            <td>' . htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') . '</td>
                            <td>' . htmlspecialchars($row['rr_reg'], ENT_QUOTES, 'UTF-8') . '</td>
                            <td>' . $allotment_date . '</td>
                            <td>' . htmlspecialchars("To " . $row['branch'], ENT_QUOTES, 'UTF-8') . "  " . htmlspecialchars($row['lab'], ENT_QUOTES, 'UTF-8') . '</td>

                            <td>' . $row['units'] . '</td>
                            <td>' . $row['used_for'] . '</td>
            </tr>';
                    }
                    ?>
                </tbody>

            </table>
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



</body>

</html>

<?php
ob_end_flush();
?>