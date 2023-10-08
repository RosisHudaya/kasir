<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Selamat Datang</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="home-admin.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-info bg-info">
        <div class="container">
            <h2 class="text-white">Selamat Datang</h2>
        </div>
        <div class="dropdown ml-auto">
            <button class="btn dropdown-toggle text-white" type="button" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                <?php
                if (isset($_SESSION['username'])) {
                    echo $_SESSION['username'];
                }
                ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="logout.php">Logout</a>
            </div>
        </div>

    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <nav class="nav flex-column mt-5 ml-4">
                    <a class="nav-link my-3" href="home-admin.php">Kasir</a>
                    <a class="nav-link my-3" href="product-admin.php">Barang</a>
                    <a class="nav-link my-3" href="category-admin.php">Kategori</a>
                    <a class="nav-link my-3" href="payment-admin.php">Transaksi</a>
                </nav>
            </div>

            <?php
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
              ' . $_SESSION['success_message'] . '
            </div>';
                unset($_SESSION['success_message']);
            }
            ?>

            <?php
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
              ' . $_SESSION['error_message'] . '
            </div>';
                unset($_SESSION['error_message']);
            }
            ?>

            <div class="col-md-10 offset-md-2 content">
                <div class="row justify-content-end mt-5 mb-3">
                    <div class="col-md-6 text-right">
                        <button class="btn btn-success" data-toggle="modal" data-target="#tambahBarangModal">
                            Tambah
                        </button>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Barang</th>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-center">Gambar</th>
                                    <th class="text-center">Stok</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include "connect.php";

                                $limit = 5;
                                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                $start = ($page - 1) * $limit;
                                $queryCount = "SELECT COUNT(*) as total FROM products";
                                $resultCount = mysqli_query($connect, $queryCount);
                                $dataCount = mysqli_fetch_assoc($resultCount);
                                $totalData = $dataCount['total'];
                                $totalPages = ceil($totalData / $limit);

                                $query = "SELECT p.product_id, p.category_id, p.product_name, p.image, p.price, p.stok, c.category_name FROM products p JOIN categories c ON p.category_id = c.category_id LIMIT $start, $limit";
                                $result = mysqli_query($connect, $query);

                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td class='text-center'>" . $row['product_name'] . "</td>";
                                    echo "<td class='text-center'>" . $row['category_name'] . "</td>";
                                    echo '<td class="text-center"><img src="uploads/' . $row['image'] . '" alt="Gambar Barang" class="img-fluid" style="max-width: 50px;"></td>';
                                    echo "<td class='text-center'>" . $row['stok'] . "</td>";
                                    echo "<td class='text-center'>Rp " . number_format($row['price'], 2, ',', '.') . "</td>";
                                    echo "<td class='text-center'>";
                                    echo '<button class="btn btn-warning mr-2 text-white" data-toggle="modal" data-target="#detailBarangModal" data-barangid="' . $row['product_id'] . '"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-layers-fill" viewBox="0 0 16 16">
                                        <path d="M7.765 1.559a.5.5 0 0 1 .47 0l7.5 4a.5.5 0 0 1 0 .882l-7.5 4a.5.5 0 0 1-.47 0l-7.5-4a.5.5 0 0 1 0-.882l7.5-4z"/>
                                        <path d="m2.125 8.567-1.86.992a.5.5 0 0 0 0 .882l7.5 4a.5.5 0 0 0 .47 0l7.5-4a.5.5 0 0 0 0-.882l-1.86-.992-5.17 2.756a1.5 1.5 0 0 1-1.41 0l-5.17-2.756z"/>
                                        </svg></button>';
                                    echo '<button class="btn btn-primary mr-1 edit-produk-btn" data-toggle="modal" data-target="#editBarangModal" data-barang-id="' . $row['product_id'] . '" data-categori-id="' . $row['category_id'] . '" data-produk-name="' . $row['product_name'] . '" data-produk-price="' . $row['price'] . '" data-produk-image="' . $row['image'] . '" data-produk-stok="' . $row['stok'] . '"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                                        <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001z"/>
                                        </svg></button>';
                                    echo '<button class="btn btn-danger ml-1 delete-produk-btn" data-toggle="modal" data-target="#deleteBarangModal" data-delete-barang-id="' . $row['product_id'] . '"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                        <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z"/>
                                        </svg></button>';
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                mysqli_close($connect);
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <?php if ($totalData > $limit): ?>
                            <nav aria-label="Page navigation example">
                                <ul class="pagination">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php if ($i == $page)
                                            echo 'active'; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Barang -->
    <div class=" modal fade" id="tambahBarangModal" tabindex="-1" role="dialog" aria-labelledby="tambahBarangModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahBarangModalLabel">Tambah Barang
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="add_product.php" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="namaBarang">Nama Barang</label>
                            <input type="text" class="form-control" id="namaBarang" name="namaBarang" required>
                        </div>
                        <div class="form-group">
                            <label for="kategori">Kategori</label>
                            <select class="form-control" id="kategori" name="kategori" required>
                                <?php
                                include "connect.php";
                                $query = "SELECT * FROM categories";
                                $result = mysqli_query($connect, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="' . $row['category_id'] . '">' . $row['category_name'] . '</option>';
                                }
                                mysqli_close($connect);
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga</label>
                            <input type="number" class="form-control" id="harga" name="harga" required>
                        </div>
                        <div class="form-group">
                            <label for="stok">Stok</label>
                            <input type="number" class="form-control" id="stok" name="stok" required>
                        </div>
                        <div class="form-group">
                            <label for="gambar">Gambar</label>
                            <input type="file" class="form-control-file" id="gambar" name="gambar" accept=".jpg, .jpeg"
                                required>
                        </div>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Barang -->
    <div class="modal fade" id="detailBarangModal" tabindex="-1" role="dialog" aria-labelledby="detailBarangModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailBarangModalLabel">Detail Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="detailBarangContent"></div>
                </div>
                <div class="modal-body">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Barang -->
    <div class="modal fade" id="editBarangModal" tabindex="-1" role="dialog" aria-labelledby="editBarangModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBarangModalLabel">Edit Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="edit_product.php" enctype="multipart/form-data">
                        <input type="hidden" id="editProductId" name="product_id">
                        <div class="form-group">
                            <label for="editNamaBarang">Nama Barang</label>
                            <input type="text" class="form-control" id="editNamaBarang" name="namaBarang" required>
                        </div>
                        <div class="form-group">
                            <label for="editKategori">Kategori</label>
                            <select class="form-control" id="editKategori" name="kategori" required>
                                <?php
                                include "connect.php";
                                $query = "SELECT * FROM categories";
                                $result = mysqli_query($connect, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="' . $row['category_id'] . '">' . $row['category_name'] . '</option>';
                                }
                                mysqli_close($connect);
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editHarga">Harga</label>
                            <input type="number" class="form-control" id="editHarga" name="harga" required>
                        </div>
                        <div class="form-group">
                            <label for="editStok">Stok</label>
                            <input type="number" class="form-control" id="editStok" name="stok" required>
                        </div>
                        <div class="form-group">
                            <label for="editGambar">Gambar</label>
                            <input type="file" class="form-control-file" id="editGambar" name="gambar"
                                accept=".jpg, .jpeg">
                            <img class="mt-2" id="previewGambar" src="" alt="Preview Gambar" style="max-width: 100px;">
                        </div>
                        <input type="hidden" name="produkId" value="" id="produkId" />
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--Modal delete barang-->
    <div class="modal fade" id="deleteBarangModal" tabindex="-1" role="dialog" aria-labelledby="deleteProdukModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">Hapus User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus produk ini?</p>
                </div>
                <form method="post" action="delete_product.php">
                    <div class="modal-footer">
                        <input type="hidden" name="deleteProdukId" id="deleteProdukId" value="">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger" id="confirmDeleteUserBtn">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function hideAlert(alertId) {
            var alert = document.getElementById(alertId);
            if (alert) {
                setTimeout(function () {
                    alert.style.display = 'none';
                }, 2000);
            }
        }

        hideAlert('successAlert');
        hideAlert('errorAlert');
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $('#detailBarangModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var barangId = button.data('barangid');

            $.ajax({
                url: 'get_product.php',
                type: 'GET',
                data: { id: barangId },
                success: function (response) {
                    $('#detailBarangContent').html(response);
                },
                error: function () {
                    alert('Gagal memuat detail barang.');
                }
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#deleteBarangModal').on('show.bs.modal', function (event) {
                var deleteBarangId = $(event.relatedTarget).data('delete-barang-id');
                $('#deleteProdukId').val(deleteBarangId);
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('.edit-produk-btn').click(function () {
                var produkId = $(this).data('barang-id');
                var kategoriId = $(this).data('categori-id');
                var nama = $(this).data('produk-name');
                var harga = $(this).data('produk-price');
                var stok = $(this).data('produk-stok');
                var gambar = $(this).data('produk-image');
                console.log(produkId);
                console.log(kategoriId);
                console.log(nama);
                console.log(harga);
                console.log(stok);
                console.log(gambar);
                $('#produkId').val(produkId);
                $('#editKategori').val(kategoriId);
                $('#editNamaBarang').val(nama);
                $('#editHarga').val(harga);
                $('#editStok').val(stok);
                $('#previewGambar').attr('src', 'uploads/' + gambar);
            });
        });
    </script>
</body>

</html>