<?php
session_start();
include "connect.php";

if (isset($_POST['submit'])) {
    $kategori = $_POST['kategori'];

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