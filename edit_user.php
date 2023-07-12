<?php
session_start();
include "connect.php";

$user_id = $_POST['user_id'];
$username = $_POST['username'];
$email = $_POST['email'];

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