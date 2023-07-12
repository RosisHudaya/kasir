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
            <button class="btn btn-success" data-toggle="modal" data-target="#addUserModal">
              Tambah User
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

                $query = "SELECT user_id, username, email FROM users WHERE type = 0";
                $result = mysqli_query($connect, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr>";
                  echo "<td>" . $row['username'] . "</td>";
                  echo "<td>" . $row['email'] . "</td>";
                  echo "<td class='text-center'>";
                  echo '<button class="btn btn-primary mr-1 edit-user-btn" data-toggle="modal" data-target="#editUserModal" data-edit-user-id="' . $row['user_id'] . '" data-edit-username="' . $row['username'] . '" data-edit-email="' . $row['email'] . '">Ubah</button>';
                  echo '<button class="btn btn-danger ml-1 delete-user-btn" data-toggle="modal" data-target="#deleteUserModal" data-delete-user-id="' . $row['user_id'] . '">Hapus</button>';
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
            <input type="hidden" name="type" value="0" />
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
            <input type="hidden" name="type" value="0" />
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
        $('#edit-user-id').val(userId);
        $('#edit-username').val(username);
        $('#edit-email').val(email);
      });
    });
  </script>
</body>

</html>