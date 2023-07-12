<?php
session_start();
include "connect.php";

$delete_user_id = $_POST['delete_user_id'];

$query = "DELETE FROM users WHERE user_id = $delete_user_id";
$result = mysqli_query($connect, $query);

if ($result) {
    $_SESSION['success_message'] = 'Data berhasil dihapus';
} else {
    $_SESSION['error_message'] = 'Terjadi kesalahan dalam menghapus data.';
}

mysqli_close($connect);

header('Location: home-admin.php');
exit;
?>