<?php
session_start();
include "connect.php";

if (isset($_POST['submit'])) {
    $kategori = $_POST['kategori'];

    $query_check_category = "SELECT * FROM categories WHERE category_name = '$kategori'";
    $result_check_category = mysqli_query($connect, $query_check_category);
    if (mysqli_num_rows($result_check_category) > 0) {
        $_SESSION['error_message'] = 'Kategori sudah digunakan!';
        header('Location: category-admin.php');
        exit;
    }

    $query = "INSERT INTO categories (category_name) VALUES ('$kategori')";
    $result = mysqli_query($connect, $query);

    if ($result) {
        $_SESSION['success_message'] = "Data baru berhasil ditambahkan!";
        header("Location: category-admin.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Terjadi kesalahan saat menambahkan data baru!";
        header("Location: category-admin.php");
        exit();
    }
}

mysqli_close($connect);
?>