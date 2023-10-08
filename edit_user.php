<?php
session_start();
include "connect.php";

$user_id = $_POST['user_id'];
$username = $_POST['username'];
$email = $_POST['email'];

$query_check_email = "SELECT * FROM users WHERE email = '$email' AND user_id <> '$user_id'";
$result_check_email = mysqli_query($connect, $query_check_email);

if (mysqli_num_rows($result_check_email) > 0) {
    $_SESSION['error_message'] = 'Email sudah digunakan!';
    mysqli_close($connect);
    header('Location: home-admin.php');
    exit;
}

$query = "UPDATE users SET username = '$username', email = '$email' WHERE user_id = '$user_id'";
$result = mysqli_query($connect, $query);

if ($result) {
    $_SESSION['success_message'] = 'Data berhasil diperbarui.';
} else {
    $_SESSION['error_message'] = 'Terjadi kesalahan saat memperbarui data.';
}

mysqli_close($connect);

header('Location: home-admin.php');
exit;

?>