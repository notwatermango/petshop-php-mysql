<?php
session_start();

// if not logged in redirect to login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] != true) {
  header("location: login.php");
  exit;
}

require_once 'config/config.php';

// GET groomings
$query_unpaid_groomings = "
  select
    g.id groom_id,
    m.id member_id, 
    m.name as name,
    m.type as type,
    m.gender as gender,
    m.owner_mobile as mobile,
    g.groom_date as date,
    g.groom_time as time,
    g.price
  from groomings g
  join members m
    on g.member_id = m.id
  where is_paid = false
  order by date asc, time asc;
";
$query_paid_groomings = "
  select
    g.id groom_id,
    m.id member_id, 
    m.name as name,
    m.type as type,
    m.gender as gender,
    m.owner_mobile as mobile,
    g.groom_date as date,
    g.groom_time as time,
    g.price
  from groomings g
  join members m
    on g.member_id = m.id
  where is_paid = true
  order by date asc, time asc;
";

$result_unpaid_groomings = mysqli_query($link, $query_unpaid_groomings);
$result_paid_groomings = mysqli_query($link, $query_paid_groomings);


$new_submit = isset($_POST["newGroomingSubmit"]) ? $_POST["newGroomingSubmit"] : '';
$submit_result = "";
$error = "";

if ($new_submit === "submit") {
  $member_id = $date = $time = $price = $paid =  "";

  // check all
  $member_id = (empty(trim($_POST["member_id"])) ? "" : trim($_POST["member_id"]));
  $error = (empty(trim($_POST["member_id"])) ? "member_id cannot be empty" : "");
  $date = (empty(trim($_POST["date"])) ? "" : trim($_POST["date"]));
  $error = (empty(trim($_POST["date"])) ? "date cannot be empty" : "");
  $time = (empty(trim($_POST["time"])) ? "" : trim($_POST["time"]));
  $error = (empty(trim($_POST["time"])) ? "time cannot be empty" : "");
  $price = (empty(trim($_POST["price"])) ? "" : trim($_POST["price"]));
  $error = (empty(trim($_POST["price"])) ? "price cannot be empty" : "");
  // payment status
  $paid = (empty($_POST["paid"]) ? "false" : trim($_POST["paid"]));

  // all valid
  if (empty($error)) {
    // check member_id
    $query_member_id_check = "select id from members where id = ".$member_id." and expired_at >= now();";
    if ($result_member_id_check = mysqli_query($link, $query_member_id_check)) {
      if (mysqli_num_rows($result_member_id_check)) {
        $query_new_grooming = "insert into groomings value ("
          . "default,\""
          . $member_id . "\","
          . "default,\""
          . $date . "\",\""
          . $time . "\",\""
          . $price . "\","
          . $paid . ");";
        if (mysqli_query($link, $query_new_grooming)) {
          $submit_result = "Success adding groom for " . $member_id;
          header("location: grooming.php");
        } else {
          $submit_result = "An error occured.";
        }
      } else {
        // invalid id or inactive
        $submit_result = "please enter correct id and make sure membership is active"; 
      }
    }
  } else {
    $submit_result = "sql error.";
  }
}

// handle delete
if (array_key_exists("deleteGrooming", $_POST)) {
  handleDeleteGrooming($_POST["deleteGrooming"], $link);
}

function handleDeleteGrooming($id_delete, $link_delete) {
  $query_delete_grooming = "delete from groomings where id=".$id_delete.";";
  if (mysqli_query($link_delete, $query_delete_grooming)) {
    header("location: grooming.php");
  } else {
    $error = "cannot delete";
  }
}
// handle pay
if (array_key_exists("payGrooming", $_POST)) {
  handlePayGrooming($_POST["payGrooming"], $link);
}

function handlePayGrooming($id_pay, $link_pay) {
  $query_extend_member = "update groomings set is_paid = true where id=".$id_pay.";";
  if (mysqli_query($link_pay, $query_extend_member)) {
    header("location: grooming.php");
  } else {
    $error = "cannot pay"; 
  }
} 


?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./css/navbar.css">
  <link rel="stylesheet" href="./css/main.css">
  <link rel="stylesheet" href="./css/grooming.css">
  <title>Grooming</title>
</head>
<body>
  <div class="navbar-container">
    <div class="navbar-row">
      <div class="navbar-left">
        <a href="dashboard.php" class="navbar-item">Dashboard</a>
        <a href="grooming.php" class="navbar-item">Grooming</a>
        <a href="purchase.php" class="navbar-item">Purchase</a>
        <a href="membership.php" class="navbar-item">Membership</a>
        <a href="debug.php" class="navbar-debug navbar-on">Debug</a>

      </div>
      <a href="logout.php" class="navbar-item">Logout</a>
    </div>
  </div>
  <div>
    <form>
      
    </form>
  </div>
</body>

</html>