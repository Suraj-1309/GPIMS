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



    <?php
    if (isset($_SESSION['popup_message'])) {
        // Use Bootstrap's alert classes. "success" and "danger" here correspond to $_SESSION['popup_type']
        $alertClass = ($_SESSION['popup_type'] === 'success') ? 'alert-success' : 'alert-danger';
        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert" id="popupMessage">'
            . $_SESSION['popup_message'] .
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
             <span aria-hidden="true">&times;</span>
          </button>
         </div>';
        // Remove the message after displaying it
        unset($_SESSION['popup_message']);
        unset($_SESSION['popup_type']);
    }

    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                var popup = document.getElementById('popupMessage');
                if (popup) {
                    popup.style.transition = "opacity 1s ease";
                    popup.style.opacity = 0;
                    setTimeout(function () {
                        if (popup.parentNode) {
                            popup.parentNode.removeChild(popup);
                        }
                    }, 1500);
                }
            }, 1500);
        });
    </script>


    <?php include "../adminpanel/components/navbar.php" ?>

    <!-- sidebar  -->
    <?php include "components/sidebar.php" ?>

    <style>
          @media (max-width: 767px) {
            
            h2{ 
                text-align: left;
                margin-left: -27px;
                font-size: x-large;
                font-weight: 900;
                padding-top: 5%;
            }
        }
    </style>

    <div id="admin" class="right">
    <div class="p-3 ml-5 pl-4">
                <h2>RR Register Record of Consumable items</h2>
    </div>
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
                        <th scope="col">Purchase Date</th>
                        <th scope="col">Got it From</th>
                        <th scope="col">SRNO / Bill date</th>
                        <th scope="col">Unit Price</th>
                        <th scope="col">Units</th>
                        <th scope="col">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM `rr_received_items` WHERE `type` = 'Consumable Item' ORDER BY `sno` DESC";
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
                            <td>' . htmlspecialchars($row['rr_reg'], ENT_QUOTES, 'UTF-8') . '</td>
                            <td>' . $purchase_date_formatted . '</td>
                            <td>' . htmlspecialchars($row['got_it_from'], ENT_QUOTES, 'UTF-8') . '</td>
                            
                            <td>' . htmlspecialchars($row['srno'], ENT_QUOTES, 'UTF-8') . '<br>' . $bill_date_formatted . '</td>

                            <td>' . $row['unit_price'] . '</td>
                            <td>' . $row['units'] . '</td>
                            <td>' . $row['overall_price'] . '</td>
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