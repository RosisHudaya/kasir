<?php
session_start();
include "connect.php";

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email' and password='$password'";
    $result = mysqli_query($connect, $query);
    $cek = mysqli_fetch_assoc($result);

    if ($cek) {
        $_SESSION['user_id'] = $cek['user_id'];
        $_SESSION['username'] = $cek['username'];
        $_SESSION['type'] = $cek['type'];

        if ($cek['type'] == 0) {
            header("Location: home-kasir.html");
            exit;
        } else if ($cek['type'] == 1) {
            header("Location: home-admin.php");
            exit;
        }
    } else {
        $_SESSION['error_message'] = 'Email atau password salah.';
        header("Location: index.php");
        exit;
    }
}

mysqli_close($connect);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet" />
    <style>
        body {
            font-family: "Nunito", sans-serif;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-center">Login</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="index.php">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Masukkan email" />
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Masukkan password" />
                            </div>
                            <input type="submit" class="btn btn-primary btn-block" value="Login" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>