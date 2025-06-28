<?php 
session_start();
require "conn.php";
extract($_POST);

$sql = "CALL users_sp('$username','$password')";
$res = $conn->query($sql);
$r = $res->fetch_array();

if ($r["error"]) {
    header("Location: ../html/index.php?error=$r[0]");
    exit();
} else {
    foreach ($r as $key => $value) {
        $_SESSION[$key] = $value;
    }
    // Ensure user_id is always set for app compatibility
    if (isset($_SESSION['id'])) {
        $_SESSION['user_id'] = $_SESSION['id'];
    }

    // Redirect based on user role
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../html/home.php");
    } elseif ($_SESSION['role'] === 'doctor') {
        header("Location: ../html/doctor.php");
    } elseif ($_SESSION['role'] === 'patient') {
        header("Location: ../html/pdashboard.php");
    } else {
        header("Location: ../html/home.php");
    }
    exit();
}

exit();
?>
