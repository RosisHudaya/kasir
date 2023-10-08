<?php
session_start();
include "connect.php";

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $type = $_POST['type'];

    $checkQuery = "SELECT * FROM users WHERE email = '$email'";
    $checkResult = mysqli_query($connect, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $_SESSION['error_message'] = "Email sudah digunakan!";
        header("Location: home-admin.php");
        exit();
    } else {
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
}

mysqli_close($connect);
?>