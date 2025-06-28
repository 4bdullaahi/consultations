<?php
session_start();
require "../lib/conn.php";




extract($_POST);

if ($id) {
    // Update
    $sql = "UPDATE users SET name='$name', role='$role', email='$email' , gender='$gender',  password='$password', phone='$phone' WHERE id='$id'";
} else {
    // Insert
    $sql = "INSERT INTO users (name, role, email,gender,password, phone) VALUES ('$name', '$role', '$email','$gender','$password', '$phone')";
}

$conn->query($sql);

?>
