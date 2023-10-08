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
            <button class="btn btn-success" data-toggle="modal" data-target="#addUserModal">
              Tambah
            </button>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-md-12">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th class="text-center">Username</th>
                  <th class="text-center">Email</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                include "connect.php";

                $limit = 5;
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $start = ($page - 1) * $limit;
                $queryCount = "SELECT COUNT(*) as total FROM users WHERE type = 1";
                $resultCount = mysqli_query($connect, $queryCount);
                $dataCount = mysqli_fetch_assoc($resultCount);
                $totalData = $dataCount['total'];
                $totalPages = ceil($totalData / $limit);

                $query = "SELECT user_id, username, email FROM users WHERE type = 1 LIMIT $start, $limit";
                $result = mysqli_query($connect, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr>";
                  echo "<td>" . $row['username'] . "</td>";
                  echo "<td>" . $row['email'] . "</td>";
                  echo "<td class='text-center'>";
                  echo '<button class="btn btn-primary mr-1 edit-user-btn" data-toggle="modal" data-target="#editUserModal" data-edit-user-id="' . $row['user_id'] . '" data-edit-username="' . $row['username'] . '" data-edit-email="' . $row['email'] . '"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pen-fill" viewBox="0 0 16 16">
                    <path d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001z"/>
                    </svg></button>';
                  echo '<button class="btn btn-danger ml-1 delete-user-btn" data-toggle="modal" data-target="#deleteUserModal" data-delete-user-id="' . $row['user_id'] . '"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
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

  <!-- Modal add user-->
  <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Tambah User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="post" action="add_user.php">
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan Username"
                required />
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Email" required />
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Password"
                required />
            </div>
            <input type="hidden" name="type" value="1" />
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

  <!--Modal edit user-->
  <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="post" action="edit_user.php">
            <div class="form-group">
              <label for="edit-username">Username</label>
              <input type="text" class="form-control" id="edit-username" name="username" placeholder="Masukkan Username"
                required />
            </div>
            <div class="form-group">
              <label for="edit-email">Email</label>
              <input type="email" class="form-control" id="edit-email" name="email" placeholder="Masukkan Email"
                required />
            </div>
            <input type="hidden" name="type" value="1" />
            <input type="hidden" name="user_id" value="" id="edit-user-id" />
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

  <!--Modal delete user-->
  <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel"
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
          <p>Apakah Anda yakin ingin menghapus user ini?</p>
        </div>
        <form method="post" action="delete_user.php">
          <div class="modal-footer">
            <input type="hidden" name="delete_user_id" id="delete-user-id" value="">
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
      $('#deleteUserModal').on('show.bs.modal', function (event) {
        var deleteUserId = $(event.relatedTarget).data('delete-user-id');
        $('#delete-user-id').val(deleteUserId);
      });
    });
  </script>

  <script>
    $(document).ready(function () {
      $('.edit-user-btn').click(function () {
        var userId = $(this).data('edit-user-id');
        var username = $(this).data('edit-username');
        var email = $(this).data('edit-email');
        console.log(userId);
        $('#edit-user-id').val(userId);
        $('#edit-username').val(username);
        $('#edit-email').val(email);
      });
    });
  </script>
</body>

</html>