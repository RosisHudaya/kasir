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
                    <a class="nav-link my-3" href="home-admin.php">User</a>
                    <a class="nav-link my-3" href="product-admin.php">Barang</a>
                    <a class="nav-link my-3" href="category-admin.php">Kategori</a>
                    <a class="nav-link my-3" href="#">Laporan</a>
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
                            Tambah Barang
                        </button>

                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Barang</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center" style="width: 35%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include "connect.php";

                                $query = "SELECT p.product_id, p.category_id, p.product_name, p.image, p.price FROM products p JOIN categories c ON p.category_id = c.category_id";
                                $result = mysqli_query($connect, $query);

                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td class='text-center'>" . $row['product_name'] . "</td>";
                                    echo "<td class='text-center'>Rp " . number_format($row['price'], 0, ',', '.') . "</td>";
                                    echo "<td class='text-center'>";
                                    echo '<button class="btn btn-info mr-2" data-toggle="modal" data-target="#detailBarangModal" data-barangid="' . $row['product_id'] . '">Detail</button>';
                                    echo '<button class="btn btn-primary mr-1" >Ubah</button>';
                                    echo '<button class="btn btn-danger ml-1" >Hapus</button>';
                                    echo "</td>";
                                    echo "</tr>";
                                }

                                mysqli_close($connect);
                                ?>
                            </tbody>
                        </table>
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
                            <label for="gambar">Gambar</label>
                            <input type="file" class="form-control-file" id="gambar" name="gambar" required>
                        </div>
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

</body>

</html>