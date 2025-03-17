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
        max-height: calc(100vh);

      }

      .sidebar.open {
        transform: translateX(0);
      }

      .right {
        margin-left: 0;
        margin-top: 20px;
      }

      #logout{
        background-color: red;
        color: white;
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

  </style>
  
  <style>
/* Responsive adjustments for mobile devices */
@media (max-width: 767px) {


  #admin.right {
    font-size: small;
    position: relative;  /* Use normal document flow */
    left: 0;
    margin: 5px;        /* Reduced margins */
    padding: 0px;       /* Less padding for small screens */
    width: 100%;         /* Full width */
    max-height: none;
    overflow: hidden;/* Smaller font-size for mobile */
  }
  h3{
    text-align: end;
  }
  /* Make the form row display as block so its children stack vertically */
#addForm .form-row {
  display: block;
}

/* Ensure each form-group spans full width and has some spacing */
#addForm .form-group {
    font-size: small;
  width: 100%;
  padding: -10px;

}
#addForm .form-control {
  padding: 0.25rem 0.5rem;  /* Reduced padding for a smaller input height */
  font-size: 0.875rem;      /* Smaller font size if needed */
  height: 2rem;             /* Fixed height (adjust as needed) */
  line-height: 1.2;         /* Optional: adjust line-height for better text alignment */
}


/* Hide the small text so that only the Add button shows in row 3 */
#addForm small {
  display: none;
}

/* Force the submit button to appear on its own row */
#addForm button[type="submit"] {
    font-size: small;
  display: block;
}


/* Allow horizontal scrolling if the table is wider than the viewport */
.container {
  overflow-x: auto;
}

/* Make the table size based solely on its content */
#myTable {
  table-layout: auto;
  width: auto;
}

/* Prevent wrapping inside table headers and cells */
#myTable th,
#myTable td {
  white-space: nowrap;
  vertical-align: middle;
}

/* Ensure buttons within cells remain inline */
#myTable .btn {
  display: inline-block;
}

@media (max-width: 767px) {
  /* Center the modal dialog */
  #editModel .modal-dialog {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    min-height: 85vh; /* Ensures the modal occupies full height to center vertically */
  }

  /* Set the modal content width and auto margin for horizontal centering */
  #editModel .modal-content {
    width: 90%;
    margin: auto;
  }
}


}

    </style>
  <!-- Toggle arrow for sidebar on small screens -->
  <button id="sidebarToggle" class="d-md-none">&gt;</button>
<div class="sidebar">
        <div>
            <h4>Admin Options</h4>
            <ul class="nav flex-column">
                <!-- Users dropdown -->
                <li class="nav-item">
                    <a class="nav-link bg-col hover-class" href="#option1" data-toggle="collapse"
                        data-target="#allUsersDropdown" aria-expanded="false" aria-controls="allUsersDropdown">All
                        Users</a>
                    <div class="collapse" id="allUsersDropdown">
                        <ul class="list-unstyled ml-3">

                            <li class="nav-item">
                                <a class="nav-link hover-class" href="adminuser.php">Admin</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link hover-class" href="inventory_officer.php">Inventory
                                    Officer</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link hover-class" href="inventory_incharges.php">Inventory
                                    Incharges</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link hover-class" href="labs_and_incharges.php">Labs and Incharges</a>
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
                                <a class="nav-link hover-class" href="#itemAllotment"
                                    onclick="showContent('itemAllotment')">Item
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
                    <a class="nav-link hover-class" href="allotment_request.php">Allotment Requests</a>
                </li>
                <li class="nav-item bg-col">
                    <a class="nav-link hover-class" href="return_request.php">Return Requests</a>
                </li>
                <li class="nav-item bg-col">
                    <a class="nav-link hover-class" href="labs.php">Labs</a>
                </li>
            </ul>
        </div>


        <!-- links from bottom -->
        <div class="bottom-links" >
            <ul class="nav flex-column" >
                <form action="logout.php" method="get" >
                    <input type="submit" value="Logout" id="logout">
                </form>

            </ul>
        </div>
    </div>
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