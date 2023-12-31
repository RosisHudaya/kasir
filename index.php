<?php
session_start();
include "connect.php";

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) && empty($password)) {
        $error_message = 'Email dan password harus diisi.';
    } elseif (empty($email)) {
        $error_message = 'Email harus diisi.';
    } elseif (empty($password)) {
        $error_message = 'Password harus diisi.';
    } else {
        $password = md5($password);

        $query = "SELECT * FROM users WHERE email='$email' and password='$password'";
        $result = mysqli_query($connect, $query);
        $cek = mysqli_fetch_assoc($result);

        if ($cek) {
            $_SESSION['user_id'] = $cek['user_id'];
            $_SESSION['username'] = $cek['username'];
            $_SESSION['type'] = $cek['type'];

            if ($cek['type'] == 1) {
                header("Location: home-kasir.php");
                exit;
            } else if ($cek['type'] == 2) {
                header("Location: home-admin.php");
                exit;
            }
        } else {
            $error_message = 'Email atau password salah.';
        }
    }
}

mysqli_close($connect);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="d-flex justify-content-center mt-2">
        <div class="col-md-5 d-flex align-items-center">
            <div class="col-md-12 py-5">
                <div class="form-group col-md-10">
                    <h4 class="title-wel mb-0">
                        WELCOME TO
                    </h4>
                    <p class="title-p">Cashier Website with PHP <span class="title-span">native</span></p>
                </div>
                <hr class="my-4 hr-color">
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="index.php">
                    <div class="form-group col-md-12">
                        <label for="email">Email</label>
                        <input type="text" class="form-control form-login" id="email" name="email"
                            placeholder="masukkan email" />
                    </div>
                    <div class="form-group col-md-12">
                        <label for="password">Password</label>
                        <input type="password" class="form-control form-login mb-5" id="password" name="password"
                            placeholder="masukkan password" />
                    </div>
                    <div class="form-group col-md-12">
                        <input type="submit" class="btn btn-block btn-login" value="LOGIN" />
                    </div>
                </form>
                <hr class="my-4 hr-color">
                <hr class="col-md-8 my-4 hr-color">
                <hr class="col-md-4 my-4 hr-color">
            </div>
        </div>
        <div class="col-md-5 mt-4">
            <img class="animated-image" src="assets/kasir.jpg" alt="bg-login">
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>