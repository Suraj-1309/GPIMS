<?php
include "../../_dbconnect.php"; // Make sure to include your database connection

$sql = "SELECT DISTINCT branch_name FROM all_labs";
$result = mysqli_query($conn, $sql);

$options = "";
while ($row = mysqli_fetch_assoc($result)) {
    $options .= '<option value="' . $row['branch_name'] . '">' . $row['branch_name'] . '</option>';
}
echo $options;
?>
