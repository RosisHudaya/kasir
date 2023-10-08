<?php
session_start();
include "connect.php";

$delete_produk_id = $_POST['deleteProdukId'];

$query_check_orderitems = "SELECT * FROM orderitems WHERE product_id = $delete_produk_id";
$result_check_orderitems = mysqli_query($connect, $query_check_orderitems);

if (mysqli_num_rows($result_check_orderitems) > 0) {
    $_SESSION['error_message'] = 'Produk tidak dapat dihapus karena sedang digunakan di order items.';
    mysqli_close($connect);
    header('Location: product-admin.php');
    exit;
}

$query_select_gambar = "SELECT image FROM products WHERE product_id = $delete_produk_id";
$result_select_gambar = mysqli_query($connect, $query_select_gambar);
if ($result_select_gambar) {
    $row_select_gambar = mysqli_fetch_assoc($result_select_gambar);
    $gambar = $row_select_gambar['image'];

    if (!empty($gambar)) {
        $gambar_path = "uploads/" . $gambar;
        if (file_exists($gambar_path)) {
            unlink($gambar_path);
        }
    }
}

$query = "DELETE FROM products WHERE product_id = $delete_produk_id";
$result = mysqli_query($connect, $query);

if ($result) {
    $_SESSION['success_message'] = 'Data berhasil dihapus';
} else {
    $_SESSION['error_message'] = 'Terjadi kesalahan dalam menghapus data.';
}

mysqli_close($connect);

header('Location: product-admin.php');
exit;
?>