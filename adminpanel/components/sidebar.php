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
        <div class="bottom-links">
            <ul class="nav flex-column">
                <form action="logout.php" method="get">
                    <input type="submit" value="Logout">
                </form>

            </ul>
        </div>
    </div>
