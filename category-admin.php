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
                        <button class="btn btn-success" data-toggle="modal" data-target="#addKategori">
                            Tambah Kategori
                        </button>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include "connect.php";

                                $query = "SELECT category_id, category_name FROM categories";
                                $result = mysqli_query($connect, $query);

                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['category_name'] . "</td>";
                                    echo "<td class='text-center'>";
                                    echo '<button class="btn btn-primary mr-1 edit-kategori-btn" data-toggle="modal" data-target="#editKategoriModal" data-category-id="' . $row['category_id'] . '" data-category="' . $row['category_name'] . '">Ubah</button>';
                                    echo '<button class="btn btn-danger ml-1 delete-kategori-btn" data-toggle="modal" data-target="#deleteKategori" data-delete-category-id="' . $row['category_id'] . '">Hapus</button>';
                                    echo "</td>";
                                    echo "</tr>";
                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal add kategori -->
    <div class="modal fade" id="addKategori" tabindex="-1" role="dialog" aria-labelledby="addKategoriLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addKategoriLabel">Tambah Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="add_category.php">
                        <div class="form-group">
                            <label for="kategori">Kategori</label>
                            <input type="text" class="form-control" id="kategori" name="kategori"
                                placeholder="Masukkan kategori" required />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal edit kategori -->
    <div class="modal fade" id="editKategoriModal" tabindex="-1" role="dialog" aria-labelledby="editKategoriLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editKategoriModalLabel">Edit Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="edit_category.php">
                        <div class="form-group">
                            <label for="editKategori">Kategori</label>
                            <input type="text" class="form-control" id="edit-category" name="edit-category"
                                placeholder="Masukkan kategori" required />
                            <input type="hidden" name="edit-category-id" id="edit-category-id" value="" />
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal delete kategori -->
    <div class="modal fade" id="deleteKategori" tabindex="-1" role="dialog" aria-labelledby="deleteKategoriLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteKategoriLabel">Hapus Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus kategori ini?</p>
                </div>
                <form method="post" action="#">
                    <div class="modal-footer">
                        <input type="hidden" name="delete-category-id" id="delete-category-id" value="">
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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#deleteKategori').on('show.bs.modal', function (event) {
                var deleteCategoryId = $(event.relatedTarget).data('delete-category-id');
                $('#delete-category-id').val(deleteCategoryId);
                console.log(deleteCategoryId)
            });
        });
    </script>

    <script>
        $(document).on('click', '.edit-kategori-btn', function () {
            var categoryId = $(this).data('category-id');
            var category = $(this).data('category');
            $('#edit-category').val(category);
            $('#edit-category-id').val(categoryId);
        });

    </script>

</body>

</html>