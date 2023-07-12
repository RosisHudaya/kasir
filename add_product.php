<?php
include "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $namaBarang = $_POST['namaBarang'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];

    $gambar = $_FILES['gambar'];
    $namaFile = $gambar['name'];
    $ukuranFile = $gambar['size'];
    $tmpFile = $gambar['tmp_name'];
    $error = $gambar['error'];

    if ($error === 4) {
        $_SESSION['error_message'] = "Silakan pilih gambar barang.";
        header("Location: product-admin.php");
        exit;
    }

    $namaFileBaru = uniqid() . '_' . $namaFile;
    $tujuan = "uploads/" . $namaFileBaru;
    if (!move_uploaded_file($tmpFile, $tujuan)) {
        $_SESSION['error_message'] = "Gagal mengupload gambar barang.";
        header("Location: product-admin.php");
        exit;
    }

    $query = "INSERT INTO products (category_id, product_name, image, price) VALUES ('$kategori', '$namaBarang', '$namaFileBaru', '$harga')";
    if (mysqli_query($connect, $query)) {
        $_SESSION['success_message'] = "Berhasil menambahkan barang.";
        header("Location: product-admin.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan barang.";
        header("Location: product-admin.php");
        exit;
    }
} else {
    header("Location: product-admin.php");
    exit;
}
?>