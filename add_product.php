<?php
session_start();
include "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $namaBarang = $_POST['namaBarang'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $gambar = $_FILES['gambar'];
    $namaFile = $gambar['name'];
    $ukuranFile = $gambar['size'];
    $tmpFile = $gambar['tmp_name'];
    $error = $gambar['error'];

    $ekstensiValid = ['jpg', 'jpeg'];
    $ekstensiFile = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    if (!in_array($ekstensiFile, $ekstensiValid)) {
        $_SESSION['error_message'] = "Jenis file gambar yang diperbolehkan JPG dan JPEG.";
        header("Location: product-admin.php");
        exit;
    }

    if ($error === 4) {
        $_SESSION['error_message'] = "Silakan pilih gambar barang.";
        header("Location: product-admin.php");
        exit;
    }

    $maxFileSize = 2097152;
    if ($ukuranFile > $maxFileSize) {
        $_SESSION['error_message'] = "Ukuran file gambar tidak boleh melebihi 2MB.";
        header("Location: product-admin.php");
        exit;
    }

    $query_check_product = "SELECT * FROM products WHERE product_name = '$namaBarang'";
    $result_check_product = mysqli_query($connect, $query_check_product);
    if (mysqli_num_rows($result_check_product) > 0) {
        $_SESSION['error_message'] = 'Nama produk sudah digunakan!';
        header('Location: product-admin.php');
        exit;
    }

    $namaFileBaru = uniqid() . '_' . $namaFile;
    $tujuan = "uploads/" . $namaFileBaru;
    if (!move_uploaded_file($tmpFile, $tujuan)) {
        $_SESSION['error_message'] = "Gagal mengupload gambar barang.";
        header("Location: product-admin.php");
        exit;
    }

    $query = "INSERT INTO products (category_id, product_name, image, price, stok) VALUES ('$kategori', '$namaBarang', '$namaFileBaru', '$harga', '$stok')";
    if (mysqli_query($connect, $query)) {
        $_SESSION['success_message'] = "Berhasil menambahkan barang.";
        header("Location: product-admin.php");
        exit();
    } else {
        unlink($tujuan);

        $_SESSION['error_message'] = "Gagal menambahkan barang.";
        header("Location: product-admin.php");
        exit();
    }
} else {
    header("Location: product-admin.php");
    exit();
}
?>