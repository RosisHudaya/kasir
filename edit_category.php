<?php
session_start();
include "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST['edit-category-id'];
    $category = $_POST['edit-category'];

    $query_check_category = "SELECT * FROM categories WHERE category_name = '$category' AND category_id != '$category_id'";
    $result_check_category = mysqli_query($connect, $query_check_category);
    if (mysqli_num_rows($result_check_category) > 0) {
        $_SESSION['error_message'] = 'Kategori sudah digunakan!';
        header('Location: category-admin.php');
        exit;
    }

    $query = "UPDATE categories SET category_name = '$category' WHERE category_id = '$category_id'";
    $result = mysqli_query($connect, $query);

    if ($result) {
        $_SESSION['success_message'] = 'Data berhasil diperbarui.';
    } else {
        $_SESSION['error_message'] = 'Terjadi kesalahan saat memperbarui data.';
    }

    mysqli_close($connect);
}

header('Location: category-admin.php');
exit;
?>