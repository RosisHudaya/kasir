<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

function terjemahkanHari($hari)
{
    $namaHari = array(
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    );

    return $namaHari[$hari];
}

function terjemahkanBulan($bulan)
{
    $namaBulan = array(
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    );

    return $namaBulan[$bulan];
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

            <div class="col-md-10 offset-md-2 content">
                <div class="row justify-content-end my-5">
                    <div class="col-md-6 text-right my-1">
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include 'connect.php';

                                $limit = 5;
                                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                $start = ($page - 1) * $limit;
                                $queryCount = "SELECT COUNT(*) as total FROM payments";
                                $resultCount = mysqli_query($connect, $queryCount);
                                $dataCount = mysqli_fetch_assoc($resultCount);
                                $totalData = $dataCount['total'];
                                $totalPages = ceil($totalData / $limit);

                                $query = "SELECT * FROM payments ORDER BY payment_id DESC LIMIT $start, $limit";
                                $result = mysqli_query($connect, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $tanggal = $row['payment_date'];
                                    $total = $row['payment_amount'];

                                    $tanggalIndonesia = terjemahkanHari(date('l', strtotime($tanggal))) . ', ' . date('d', strtotime($tanggal)) . ' ' . terjemahkanBulan(date('F', strtotime($tanggal))) . ' ' . date('Y', strtotime($tanggal));
                                    ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php echo $tanggalIndonesia; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php echo "Rp " . number_format($total, 2, ',', '.'); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            echo '<button class="btn btn-warning mr-2 text-white" data-toggle="modal" data-target="#detailPaymentModal" data-id="' . $row['payment_id'] . '">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-layers-fill" viewBox="0 0 16 16">
                                                <path d="M7.765 1.559a.5.5 0 0 1 .47 0l7.5 4a.5.5 0 0 1 0 .882l-7.5 4a.5.5 0 0 1-.47 0l-7.5-4a.5.5 0 0 1 0-.882l7.5-4z"/>
                                                <path d="m2.125 8.567-1.86.992a.5.5 0 0 0 0 .882l7.5 4a.5.5 0 0 0 .47 0l7.5-4a.5.5 0 0 0 0-.882l-1.86-.992-5.17 2.756a1.5 1.5 0 0 1-1.41 0l-5.17-2.756z"/>
                                                </svg>
                                                </button>';
                                            echo '<button class="btn btn-danger ml-1 delete-user-btn" data-toggle="modal" data-target="#deletePaymentModal" data-delete-id="' . $row['payment_id'] . '" data-delete-order="' . $row['order_id'] . '"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z"/>
                                                </svg>
                                                </button>';
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
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

    <!-- Modal Detail Barang -->
    <div class="modal fade" id="detailPaymentModal" tabindex="-1" role="dialog"
        aria-labelledby="detailPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailPaymentModalLabel">Detail Transaksi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table" id="detailPaymentContent">
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-body">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal delete user-->
    <div class="modal fade" id="deletePaymentModal" tabindex="-1" role="dialog"
        aria-labelledby="deletePaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePaymentModalLabel">Hapus User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data ini?</p>
                </div>
                <form method="post" action="delete_payment.php">
                    <div class="modal-footer">
                        <input type="hidden" name="delete-id" id="delete-id" value="">
                        <input type="hidden" name="delete-order" id="delete-order" value="">
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
        $('#detailPaymentModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var transaksiId = button.data('id');

            $.ajax({
                url: 'get_payment.php',
                type: 'GET',
                data: { id: transaksiId },
                success: function (response) {
                    $('#detailPaymentContent').html(response);
                },
                error: function () {
                    alert('Gagal memuat detail transaksi.');
                }
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#deletePaymentModal').on('show.bs.modal', function (event) {
                var deletePaymentId = $(event.relatedTarget).data('delete-id');
                var deleteOrderId = $(event.relatedTarget).data('delete-order');
                $('#delete-id').val(deletePaymentId);
                $('#delete-order').val(deleteOrderId);
                console.log(deletePaymentId);
                console.log(deleteOrderId);
            });
        });
    </script>
</body>

</html>