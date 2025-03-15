<div class="sidebar">
    <div>
        <ul class="nav flex-column">

            <li class="nav-item">
                <a class="nav-link hover-class bg-col" href="#option2" data-toggle="collapse"
                    data-target="#alloteRRDropdown3" aria-expanded="false" aria-controls="alloteRRDropdown3">Lab Current Stock</a>
                <div class="collapse" id="alloteRRDropdown3">
                    <ul class="list-unstyled ml-3">
                        <li class="nav-item">
                            <a class="nav-link hover-class" href="branch_t&p.php">Available T&P Items</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link hover-class" href="index.php">Available Consumable Items</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item bg-col">
                <a href="consume.php" class="nav-link hover-class">Consume Record</a>
            </li>
            

            <li class="nav-item bg-col">
                <a href="branch_allotment.php" class="nav-link hover-class">Alloted Items</a>
            </li>

            <li class="nav-item">
                <a class="nav-link hover-class bg-col" href="#option2" data-toggle="collapse"
                    data-target="#alloteRRDropdown2" aria-expanded="false" aria-controls="alloteRRDropdown2">Recevied Stock Record</a>
                <div class="collapse" id="alloteRRDropdown2">
                    <ul class="list-unstyled ml-3">
                        <li class="nav-item">
                            <a class="nav-link hover-class" href="recevied_t&p.php">T&P Items</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link hover-class" href="recevied_consuable.php">Consumable Items</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link hover-class bg-col" href="#option2" data-toggle="collapse"
                    data-target="#alloteRRDropdown4" aria-expanded="false" aria-controls="alloteRRDropdown4">Return Items </a>
                <div class="collapse" id="alloteRRDropdown4">
                    <ul class="list-unstyled ml-3">
                        <li class="nav-item">
                            <a class="nav-link hover-class" href="branch_return.php">Pending Returns</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link hover-class" href="branch_return_reject.php">Rejected Return Req</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link hover-class" href="return_record.php">Return Record</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item bg-col">
                <a href="branch_deprecate.php" class="nav-link hover-class">Deprecated Items</a>
            </li>

            <li>

                <?php
                echo "$_SESSION[branch] . $_SESSION[lab]";
                ?>

            </li>
        </ul>

    </div>


    <!-- links from bottom -->
    <div class="bottom-links">
        <ul class="nav flex-column">
            <li class="nav-item bg-col">
                <a class="nav-link hover-class" href="#account">Your Account</a>
            </li>

            <form action="../logout.php" method="get">
                <input type="submit" value="Logout">
            </form>
        </ul>
    </div>
</div>