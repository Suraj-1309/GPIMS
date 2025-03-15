<?php

include "_dbconnect.php";

$loggedin = false;
$role = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password']) && isset($_POST['loginType'])) {
    $passwordInput = $_POST['password'];
    $loginType = $_POST['loginType'];

    if ($loginType == 'admin'){
      $query = "SELECT * FROM `admin_login` WHERE `password` = '$passwordInput' ";
    } else {
      $query = "SELECT * FROM `user_login` WHERE `password` = '$passwordInput' ";
    }

    $result = mysqli_query($conn, $query);

    if ($result && $result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $_SESSION['loggedin'] = true;
      $_SESSION['role'] = $loginType;
      $loggedin = true;
      $role = $loginType;

      $_SESSION['popup_message'] = "You are logged in successfully as $loginType.";
      $_SESSION['popup_type'] = "success";

      if ($loginType == 'user') {
          $_SESSION['name'] = $row['name'];
          $_SESSION['branch'] = $row['branch'];
          $_SESSION['lab'] = $row['lab'];
          $_SESSION['user_password'] = $row['password'];

          if ($row['branch'] == 'INVENTORY' && $row['lab'] == 'INVENTORY'){
              header("Location: inventory/index.php");
              exit();
          }
          elseif($row['branch'] == 'INVENTORY_OFFICER' && $row['lab'] == 'INVENTORY_OFFICER'){
              header('Location: officer/index.php');
          } 
          else{
            $branch = strtolower($row['branch']);
            $lab = strtolower($row['lab']);
            header("Location: branch/index.php");
            exit();
          }
      }


      if ($loginType == 'admin'){
          $_SESSION['name'] = $row['name'];
          header("Location: admin.php");
          exit();
      }
      
      
    } else {
        $_SESSION['popup_message'] = "Password is incorrect.";
        $_SESSION['popup_type'] = "danger";
        header("Location: index.php");
        exit();
    }
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    $loggedin = false;
}
?>
