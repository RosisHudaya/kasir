<?php
session_start();
include "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST['edit-categoty-id'];
    $category = $_POST['edit-category'];

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