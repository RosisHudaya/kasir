<?php
session_start();
include "connect.php";

$category_id = $_POST['delete-category-id'];

$query_check_product = "SELECT * FROM products WHERE category_id = '$category_id'";
$result_check_product = mysqli_query($connect, $query_check_product);

if (mysqli_num_rows($result_check_product) > 0) {
    $_SESSION['error_message'] = 'Kategori tidak dapat dihapus karena sedang digunakan pada produk.';
} else {
    $query = "DELETE FROM categories WHERE category_id = '$category_id'";
    $result = mysqli_query($connect, $query);

    if ($result) {
        $_SESSION['success_message'] = 'Data berhasil dihapus';
    } else {
        $_SESSION['error_message'] = 'Terjadi kesalahan dalam menghapus data.';
    }
}

mysqli_close($connect);

header('Location: category-admin.php');