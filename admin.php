<?php
ob_start();

session_start();

// Check if the user is logged in and branch and lab are set
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['name'])) {
  // Redirect to login page if not logged in
  header("Location: ./index.php");
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
  <link rel="stylesheet" href="admin.css">
  <style>
    .sidebar form input[type="submit"] {
      width: 100%;
      text-align: left;
      background-color: #f8f9fa;
      border: none;
      padding: 10px 20px;
      font-size: 17px;
      font-weight: 900;
      cursor: pointer;
      height: ;
      color: black;
      text-decoration: none;
    }

    .sidebar form input[type="submit"]:hover {
      background-color: red;
      color: white;
    }

    .nav-item .nav-link {
      font-size: 16px;
      text-decoration: none;
      color: #000;
    }

    .bg-col {
      background-color: rgb(226, 226, 226);
    }

    .hover-class:hover {
      background-color: rgb(57, 155, 234);
      color: white;
    }

    .right {
      width: 70%;
      height: 70%;
      position: relative;
      margin-left: 350px;
      margin-top: 250px;

      font-size: 4rem;
      font-weight: bold;
      text-align: left;
      animation: slideUp 2s ease-out;
    }

    @keyframes slideUp {
      from {
        opacity: 0.4;
        transform: translateY(100%);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>

</head>

<body>
  <div class="container" id="successMessage" style=""></div>

  <?php
  include "navbar.php";
  ?>

  <div class="sidebar">
    <div>
      <h4>Admin Options</h4>
      <ul class="nav flex-column">
        <!-- Users dropdown -->
        <li class="nav-item">
          <a class="nav-link bg-col hover-class" href="#option1" data-toggle="collapse" data-target="#allUsersDropdown"
            aria-expanded="false" aria-controls="allUsersDropdown">All Users</a>
          <div class="collapse" id="allUsersDropdown">
            <ul class="list-unstyled ml-3">

              <li class="nav-item">
                <a class="nav-link hover-class" href="adminpanel/adminuser.php">Admin</a>
              </li>

              <li class="nav-item">
                <a class="nav-link hover-class" href="adminpanel/inventory_officer.php">Inventory Officer</a>
              </li>

              <li class="nav-item">
                <a class="nav-link hover-class" href="adminpanel/inventory_incharges.php">Inventory Incharges</a>
              </li>
              <li class="nav-item">
                <a class="nav-link hover-class" href="adminpanel/labs_and_incharges.php"
                  onclick="showContent('labsIncharges')">Labs
                  and
                  Incharges</a>
              </li>
            </ul>
          </div>
        </li>


        <!-- Main Inventory dropdown -->
        <li class="nav-item">
          <a class="nav-link hover-class bg-col" href="#option1" data-toggle="collapse"
            data-target="#collegeInventoryDropdown" aria-expanded="false"
            aria-controls="collegeInventoryDropdown">College Inventory</a>
          <div class="collapse" id="collegeInventoryDropdown">
            <ul class="list-unstyled ml-3">
              <li class="nav-item">
                <a class="nav-link hover-class" href="#inventoryAllItems"
                  onclick="showContent('inventoryAllItems')">Inventory All
                  Items</a>
              </li>
              <li class="nav-item">
                <a class="nav-link hover-class" href="#itemAllotment" onclick="showContent('itemAllotment')">Item
                  Allotment</a>
              </li>
              <li class="nav-item">
                <a class="nav-link hover-class" href="#inventoryItemReturn"
                  onclick="showContent('inventoryItemReturn')">Inventory
                  Item Return</a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item bg-col">
                    <a class="nav-link hover-class" href="adminpanel/allotment_request.php">Allotment Requests</a>
                </li>
        <li class="nav-item bg-col">
          <a class="nav-link hover-class" href="#option2" onclick="showContent('option2')">Labs</a>
        </li>
      </ul>
    </div>


    <!-- links from bottom -->
    <div class="bottom-links">
      <ul class="nav flex-column">
        <li class="nav-item">
          <a class="nav-link hover-class" href="#account" onclick="showContent('account')">Your Account</a>
        </li>

        <form action="logout.php" method="get">
          <input type="submit" value="Logout">
        </form>

      </ul>
    </div>
  </div>

  <div class="right">
    Welcome To Admin panel of <br> GPIMS Inventory ...
  </div>

  <script src="components/loginsuccess.js"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>