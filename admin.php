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
      /* Removed empty height property */
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

    //* Sidebar for larger screens */
    .sidebar {
      position: fixed;
      top: 70px;
      /* Adjust based on your navbar height */
      left: 0;
      width: 250px;
      height: 100%;
      background-color: #f8f9fa;
      padding: 20px;
      overflow-y: auto;
      box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
    }

    /* Sidebar for small screens: hidden by default */
    @media (max-width: 767px) {
      nav {

      }

      .sidebar {
        transform: translateX(-100%);
        
        position: fixed;
        top: 70px;
        left: 0;
        width: 250px;
        height: calc(100% - 70px);
        /* Reserve space for navbar */
        z-index: 1050;
        max-height: calc(100vh - 100px);

      }

      .sidebar.open {
        transform: translateX(0);
      }

      .right {
        margin-left: 0;
        margin-top: 20px;
      }
    }

    /* Toggle arrow button styling */
    #sidebarToggle {
      position: fixed;
      top: 80px;
      /* Adjust to align with the sidebar */
      left: 0;
      /* When sidebar is closed, this is at the screen edge */
      width: 40px;
      height: 40px;
      background-color: #f8f9fa;
      border: 1px solid #ccc;
      border-top-right-radius: 5px;
      border-bottom-right-radius: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      z-index: 1100;
      transition: left 0.3s ease;
    }

    .right {
      position: absolute;
      left: -4%;
      top: 20%;
      margin-top: 70px;
      /* start just below the nav (adjust if nav height changes) */
      margin-left: 350px;
      /* leave space for the sidebar on larger screens */
      padding: 20px;
      width: calc(100% - 350px);
      /* full width minus sidebar */
      max-height: calc(100vh - 70px);
      /* viewport height minus nav height */
      overflow-y: auto;
      /* add scroll if content overflows */
      font-size: 5rem;
      font-weight: bold;
      text-align: left;
      animation: slideUp 2s ease-out;
    }

    /* Adjust for mobile devices */
    @media (max-width: 767px) {
      .right {
        position: absolute;
        left: 2%;
        margin-left: 0;
        /* full width for mobile */
        width: 100%;
        font-size:2.5rem;
        /* smaller font-size for mobile */
        max-height: calc(100vh - 150px);
        overflow-y: auto;
      }
    }
  </style>
</head>

<body>
  <div class="container" id="successMessage"></div>

  <?php include "navbar.php"; ?>

  <!-- Toggle button for sidebar on small screens -->
  <!-- Toggle arrow for sidebar on small screens -->
  <button id="sidebarToggle" class="d-md-none">&gt;</button>

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
                <a class="nav-link hover-class" href="adminpanel/adminuser.php">Admins</a>
              </li>
              <li class="nav-item">
                <a class="nav-link hover-class" href="adminpanel/inventory_officer.php">Stock Officers</a>
              </li>
              <li class="nav-item">
                <a class="nav-link hover-class" href="adminpanel/inventory_incharges.php">Inventory Incharges</a>
              </li>
              <li class="nav-item">
                <a class="nav-link hover-class" href="adminpanel/labs_and_incharges.php"
                  onclick="showContent('labsIncharges')">Labs Incharges</a>
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
                  onclick="showContent('inventoryAllItems')">Inventory All Items</a>
              </li>
              <li class="nav-item">
                <a class="nav-link hover-class" href="#itemAllotment" onclick="showContent('itemAllotment')">Item
                  Allotment</a>
              </li>
              <li class="nav-item">
                <a class="nav-link hover-class" href="#inventoryItemReturn"
                  onclick="showContent('inventoryItemReturn')">Inventory Return Req</a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item bg-col">
          <a class="nav-link hover-class" href="adminpanel/allotment_request.php">Allotment Requests</a>
        </li>
        <li class="nav-item bg-col">
                    <a class="nav-link hover-class" href="adminpanel/return_request.php">Return Requests</a>
                </li>
        <li class="nav-item bg-col">
          <a class="nav-link hover-class" href="#option2" onclick="showContent('option2')">Labs</a>
        </li>
      </ul>
    </div>

    <!-- Links from bottom -->
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
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const sidebarToggle = document.getElementById('sidebarToggle');
      const sidebar = document.querySelector('.sidebar');

      sidebarToggle.addEventListener('click', function () {
        sidebar.classList.toggle('open');

        if (sidebar.classList.contains('open')) {
          // When open, move the toggle arrow to the right edge of the sidebar
          sidebarToggle.style.left = '250px';
          sidebarToggle.innerHTML = '&lt;'; // Show left arrow
        } else {
          // When closed, move the toggle arrow back to the left edge of the screen
          sidebarToggle.style.left = '0';
          sidebarToggle.innerHTML = '&gt;'; // Show right arrow
        }
      });
    });


  </script>
</body>

</html>