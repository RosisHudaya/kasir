<?php
session_start();
include "connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['produkId'];
    $namaBarang = $_POST['namaBarang'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $query_check_product = "SELECT * FROM products WHERE product_name = '$namaBarang' AND product_id != '$product_id'";
    $result_check_product = mysqli_query($connect, $query_check_product);
    if (mysqli_num_rows($result_check_product) > 0) {
        $_SESSION['error_message'] = 'Nama produk sudah digunakan!';
        header('Location: product-admin.php');
        exit;
    }

    $query_select_gambar = "SELECT image FROM products WHERE product_id = '$product_id'";
    $result_select_gambar = mysqli_query($connect, $query_select_gambar);
    if ($result_select_gambar) {
        $row_select_gambar = mysqli_fetch_assoc($result_select_gambar);
        $gambar_sebelumnya = $row_select_gambar['image'];
        if (!empty($gambar_sebelumnya)) {
            $gambar = $_FILES['gambar']['name'];
            $gambar_tmp = $_FILES['gambar']['tmp_name'];

            if ($_FILES['gambar']['name'] !== '') {
                $ekstensiValid = ['jpg', 'jpeg'];
                $ekstensiFile = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));
                if (!in_array($ekstensiFile, $ekstensiValid)) {
                    $_SESSION['error_message'] = "Jenis file gambar yang diperbolehkan hanya JPG dan JPEG.";
                    header("Location: product-admin.php");
                    exit;
                }

                $fileSize = $_FILES['gambar']['size'];
                $maxFileSize = 2097152;

                if ($fileSize > $maxFileSize) {
                    $_SESSION['error_message'] = "Ukuran file gambar tidak boleh melebihi 2MB.";
                    header("Location: product-admin.php");
                    exit;
                }

                $randomName = uniqid() . '_' . $gambar;

                move_uploaded_file($gambar_tmp, "uploads/" . $randomName);

                $query_update_gambar = "UPDATE products SET image = '$randomName' WHERE product_id = '$product_id'";
                mysqli_query($connect, $query_update_gambar);

                unlink("uploads/" . $gambar_sebelumnya);
            }
        }
    }

    $query = "UPDATE products SET product_name = '$namaBarang', category_id = '$kategori', price = '$harga', stok = '$stok' WHERE product_id = '$product_id'";
    $result = mysqli_query($connect, $query);

    if ($result) {
        $_SESSION['success_message'] = "Data barang berhasil diperbarui.";
        header("Location: product-admin.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Gagal memperbarui data barang.";
        header("Location: product-admin.php");
        exit;
    }
}

mysqli_close($connect);
?>