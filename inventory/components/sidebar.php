<!-- sidebar  -->
<div class="sidebar">
    <div>
        <ul class="nav flex-column">
            <li class="nav-item nav-link bg-col  hover-class">
                <a href="index.php">Inventory items</a>
            </li>

            <li class="nav-item nav-link bg-col">
                <a href="pending.php">Pending Allocated items</a>
            </li>

            <li class="nav-item nav-link bg-col">
                <a href="reject.php">Rejected Allocation</a>
            </li>

            <li class="nav-item nav-link bg-col">
                <a href="return_request.php">Returned Requests</a>
            </li>

            <li class="nav-item nav-link bg-col">
                <a href="return_history.php">Return Itmes Record</a>
            </li>



            <li class="nav-item">
                <a class="nav-link hover-class bg-col" href="#option1" data-toggle="collapse"
                    data-target="#receivedRRDropdown" aria-expanded="false" aria-controls="receivedRRDropdown">Received
                    RR Reg</a>
                <div class="collapse" id="receivedRRDropdown">
                    <ul class="list-unstyled ml-3">
                        <li class="nav-item">
                            <a class="nav-link hover-class" href="recevied_t&p.php">T&P RR Reg</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link hover-class" href="received_consumable.php">Consumable RR Reg</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link hover-class bg-col" href="#option2" data-toggle="collapse"
                    data-target="#alloteRRDropdown" aria-expanded="false" aria-controls="alloteRRDropdown">Alloted RR
                    Reg</a>
                <div class="collapse" id="alloteRRDropdown">
                    <ul class="list-unstyled ml-3">
                        <li class="nav-item">
                            <a class="nav-link hover-class" href="alloted_t&p.php">T&P RR Reg</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link hover-class" href="alloted_consumable.php">Consumable RR Reg</a>
                        </li>
                    </ul>
                </div>
            </li>

        </ul>

    </div>


    <!-- links from bottom -->
    <div class="bottom-links">
        <ul class="nav flex-column">
            <li class="nav-item nav-link">
                <a class="nav-link nav-link bg-col" href="#account" style="width : 100%">Your Account</a>
            </li>

            <form action="../logout.php" method="get">
                <input type="submit" value="Logout" class="input">
            </form>
        </ul>
    </div>
</div>