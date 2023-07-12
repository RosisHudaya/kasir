<?php
include "connect.php";

if (isset($_GET['id'])) {
    $barangId = $_GET['id'];

    $query = "SELECT p.product_name, p.price, c.category_name, p.image FROM products p
            INNER JOIN categories c ON p.category_id = c.category_id
            WHERE p.product_id = '$barangId'";
    $result = mysqli_query($connect, $query);
    $barang = mysqli_fetch_assoc($result);

    echo '<div class="mb-2">Nama Barang : ' . $barang['product_name'] . '</div>';
    echo '<div class="mb-2">Harga: Rp ' . number_format($barang['price'], 0, ',', '.') . '</div>';
    echo '<div class="mb-2">Kategori : ' . $barang['category_name'] . '</div>';
    echo '<div><img src="uploads/' . $barang['image'] . '" alt="Gambar Barang" class="img-fluid"></div>';
} else {
    echo 'Data barang tidak ditemukan.';
}

mysqli_close($connect);
?>