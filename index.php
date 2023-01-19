<?php
session_start();

// if logged in redirect to dashboard
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
  header("location: dashboard.php");
  exit;
} else {
  header("location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset='utf-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <title>Home</title>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
</head>
<body>
  Should've been redirected to <a href="./login.php">Login</a>
</body>
</html>