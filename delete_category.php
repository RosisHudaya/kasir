<?php
session_start();
include "connect.php";

$category_id = $_POST['delete_category_id'];

$query = "DELETE FROM categories WHERE category_id = $category_id";
$result = mysqli_query($connect, $query);

if ($result) {
    $_SESSION['success_message'] = 'Data berhasil dihapus';
} else {
    $_SESSION['error_message'] = 'Terjadi kesalahan dalam menghapus data.';
}

mysqli_close($connect);

header('Location: category-admin.php');
exit;
?>