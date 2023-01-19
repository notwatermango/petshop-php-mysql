<?php
session_start();

// if logged in redirect to dashboard
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
  header("location: dashboard.php");
  exit;
}

require_once "./config/config.php";

$username = $password = "";
$error = "";

// form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // check all
  if (empty(trim($_POST["username"]))) {
    $error = "Please enter username.";
  } else {
    $username = trim($_POST["username"]);
  }
  if (empty(trim($_POST["password"]))) {
    $error = "Please enter your password.";
  } else {
    $password = trim($_POST["password"]);
  }

  // all valid
  if (empty($error)) {
    // select username
    $sql = "select id, username, password from users where username = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
      // bind variables
      mysqli_stmt_bind_param($stmt, "s", $param_username);
      $param_username = $username;
      if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        // if username exist
        if (mysqli_stmt_num_rows($stmt) == 1) {
          mysqli_stmt_bind_result($stmt, $id, $username, $db_password);
          if (mysqli_stmt_fetch($stmt)) {
            // if password match
            if ($password === $db_password) {
              session_start();

              $_SESSION["loggedin"] = true;
              $_SESSION["id"] = $id;
              $_SESSION["username"] = $username;

              header("location: dashboard.php");
            } else {
              $error = "Invalid password";
            }
          }
        } else {
          $error = "Invalid username";
        }
      } else {
        $error = "Some error";
      }
      mysqli_stmt_close($stmt);
    }
  }
  mysqli_close($link);
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset='utf-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <link rel='stylesheet' href='./css/login.css'>
  <link rel='stylesheet' href='./css/main.css'>
  <title>Login</title>
</head>
<body>
  <form action="login.php" method ="post">
    <h2>admin login</h2>
    <?php echo $error; ?>
    <br>
    <label>username</label>
    <input type="text" name="username" placeholder="admin"><br>
    <label>password</label>
    <input type="password" name="password" placeholder="*****"><br> 
    <input type="submit" value="login">
  </form>
</body>
</html>