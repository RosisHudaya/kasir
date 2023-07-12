<?php
session_start();
include "connect.php";

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $type = $_POST['type'];

    $query = "INSERT INTO users (username, email, password, type) VALUES ('$username', '$email', '$password', '$type')";
    $result = mysqli_query($connect, $query);

    if ($result) {
        $_SESSION['success_message'] = "Data baru berhasil ditambahkan!";
        header("Location: home-admin.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Terjadi kesalahan saat menambahkan data baru!";
        header("Location: home-admin.php");
        exit();
    }
}

mysqli_close($connect);
?>