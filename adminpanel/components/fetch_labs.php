<?php
include "../../_dbconnect.php";

if (isset($_POST['branch'])) {
    $branch = $_POST['branch'];
    $sql = "SELECT lab_name FROM all_labs WHERE branch_name='$branch'";
    $result = mysqli_query($conn, $sql);

    $options = "";
    while ($row = mysqli_fetch_assoc($result)) {
        $options .= '<option value="' . $row['lab_name'] . '">' . $row['lab_name'] . '</option>';
    }
    echo $options;
}
?>
