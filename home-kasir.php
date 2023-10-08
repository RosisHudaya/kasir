<?php
session_start();
include "connect.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}

function addOrder($userId, $orderDate)
{
  include "connect.php";

  $stmt = $connect->prepare("INSERT INTO orders (user_id, order_date) VALUES (?, ?)");
  $stmt->bind_param("ss", $userId, $orderDate);

  if ($stmt->execute()) {
    return $stmt->insert_id;
  } else {
    return -1;
  }
}

function cancelOrder($orderId)
{
  include "connect.php";

  $stmt = $connect->prepare("DELETE FROM orderitems WHERE order_id = ?");
  $stmt->bind_param("i", $orderId);
  $stmt->execute();

  $stmt = $connect->prepare("DELETE FROM orders WHERE order_id = ?");
  $stmt->bind_param("i", $orderId);

  if ($stmt->execute()) {
    return true;
  } else {
    return false;
  }
}

function addOrderItem($orderId, $productId, $quantity, $price)
{
  include "connect.php";

  $stmt = $connect->prepare("SELECT COUNT(*) FROM orderitems WHERE order_id = ? AND product_id = ?");
  $stmt->bind_param("ii", $orderId, $productId);
  $stmt->execute();
  $stmt->bind_result($count);
  $stmt->fetch();
  $stmt->close();

  if ($count > 0) {
    return true;
  }

  $stmt = $connect->prepare("INSERT INTO orderitems (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("iiid", $orderId, $productId, $quantity, $price);

  if ($stmt->execute()) {
    return true;
  } else {
    return false;
  }
}

function cancelOrderItem($orderId, $productId)
{
  include "connect.php";

  $stmt = $connect->prepare("DELETE FROM orderitems WHERE order_id = ? AND product_id = ?");
  $stmt->bind_param("ii", $orderId, $productId);

  if ($stmt->execute()) {
    return true;
  } else {
    return false;
  }
}

function addPayment($orderId, $paymentDate, $paymentAmount)
{
  include "connect.php";

  $stmt = $connect->prepare("INSERT INTO payments (order_id, payment_date, payment_amount) VALUES (?, ?, ?)");
  $stmt->bind_param("iss", $orderId, $paymentDate, $paymentAmount);

  if ($stmt->execute()) {
    return true;
  } else {
    return false;
  }
}

$query = "SELECT product_id, product_name, price, stok FROM products";
$result = mysqli_query($connect, $query);
$products = array();
while ($row = mysqli_fetch_assoc($result)) {
  $products[$row['product_id']] = $row;
}

$totalHarga = 0;

if (isset($_POST['buat_pesanan'])) {
  if (isset($_SESSION['order_id'])) {
    $orderId = $_SESSION['order_id'];
    unset($_SESSION['order_id']);

    if (!cancelOrder($orderId)) {
      $_SESSION['success_message'] = "Failed to cancel the order.";
    }

    unset($_SESSION['pesanan']);
  }

  $userId = $_SESSION['user_id'];
  $orderDate = date("Y-m-d");

  $orderId = addOrder($userId, $orderDate);

  if ($orderId != -1) {
    $_SESSION['order_id'] = $orderId;
  } else {
    $_SESSION['success_message'] = "Failed to create the order.";
  }
}

if (isset($_POST['batal_pesanan'])) {
  if (isset($_SESSION['order_id'])) {
    $orderId = $_SESSION['order_id'];
    unset($_SESSION['order_id']);

    if (!cancelOrder($orderId)) {
      $_SESSION['success_message'] = "Failed to cancel the order.";
    }

    unset($_SESSION['pesanan']);
  }
}

if (isset($_POST['tambah_produk'])) {
  $produkId = $_POST['produk'];
  $qty = $_POST['qty'];

  if (!isset($_SESSION['pesanan'])) {
    $_SESSION['pesanan'] = array();
  }

  $product = $products[$produkId];

  if ($product === null) {
    echo "Product not found.";
  } else {
    $productExists = false;
    foreach ($_SESSION['pesanan'] as $item) {
      if ($item['produk_id'] == $produkId) {
        $productExists = true;
        break;
      }
    }

    if ($productExists) {
      $_SESSION['error_message'] = "Produk sudah ditambahkan sebelumnya.";
    } elseif ($qty > $product['stok']) {
      $_SESSION['error_message'] = "Jumlah melebihi stok yang tersedia.";
    } else {
      $newStock = $product['stok'] - $qty;
      $updateStmt = $connect->prepare("UPDATE products SET stok = ? WHERE product_id = ?");
      $updateStmt->bind_param("ii", $newStock, $product['product_id']);
      if ($updateStmt->execute()) {
        $_SESSION['pesanan'][] = array(
          'produk_id' => $product['product_id'],
          'qty' => $qty,
          'initial_stock' => $product['stok']
        );

        $orderId = $_SESSION['order_id'];
        $productId = $product['product_id'];
        $price = $product['price'] * $qty;
        if (!addOrderItem($orderId, $productId, $qty, $price)) {
          echo "Failed to add order item.";
        }
      } else {
        echo "Failed to update product stock.";
      }
    }
  }
}

if (isset($_SESSION['pesanan'])) {
  $totalHarga = calculateTotalHarga($_SESSION['pesanan'], $products);
}

if (isset($_POST['delete_produk'])) {
  $index = $_POST['index'];

  if (isset($_SESSION['pesanan'][$index])) {
    $productId = $_SESSION['pesanan'][$index]['produk_id'];
    $quantity = $_SESSION['pesanan'][$index]['qty'];
    $initialStock = $_SESSION['pesanan'][$index]['initial_stock'];

    increaseProductStock($productId, $quantity);

    $orderId = $_SESSION['order_id'];
    $productPrice = $products[$productId]['price'];

    if (cancelOrderItem($orderId, $productId)) {
      unset($_SESSION['pesanan'][$index]);
      $_SESSION['pesanan'] = array_values($_SESSION['pesanan']);

      $totalHarga = calculateTotalHarga($_SESSION['pesanan'], $products);
    } else {
      echo "Failed to cancel the order item.";
    }
  }
}

function updateOrInsertOrderItem($orderId, $productId, $quantity, $price)
{
  include "connect.php";

  $stmt = $connect->prepare("SELECT COUNT(*) FROM orderitems WHERE order_id = ? AND product_id = ?");
  $stmt->bind_param("ii", $orderId, $productId);
  $stmt->execute();
  $stmt->bind_result($count);
  $stmt->fetch();
  $stmt->close();

  if ($count > 0) {
    $stmt = $connect->prepare("UPDATE orderitems SET quantity = ?, price = ? WHERE order_id = ? AND product_id = ?");
    $stmt->bind_param("diii", $quantity, $price, $orderId, $productId);
  } else {
    $stmt = $connect->prepare("INSERT INTO orderitems (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $orderId, $productId, $quantity, $price);
  }

  if ($stmt->execute()) {
    return true;
  } else {
    return false;
  }
}

if (isset($_POST['update_produk'])) {
  $index = $_POST['editIndex'];
  $produkId = $_POST['editProduk'];
  $newQty = $_POST['editQty'];

  if (isset($_SESSION['pesanan'][$index])) {
    $product = $products[$produkId];

    if ($product === null) {
      echo "Product not found.";
    } else {
      $prevQty = $_SESSION['pesanan'][$index]['qty'];
      $diffQty = $newQty - $prevQty;

      $newStock = $product['stok'] - $diffQty;

      if ($newStock >= 0) {
        $updateStmt = $connect->prepare("UPDATE products SET stok = ? WHERE product_id = ?");
        $updateStmt->bind_param("ii", $newStock, $product['product_id']);
        if ($updateStmt->execute()) {
          $_SESSION['pesanan'][$index]['qty'] = $newQty;
          $totalHarga = calculateTotalHarga($_SESSION['pesanan'], $products);

          $orderId = $_SESSION['order_id'];
          $productId = $product['product_id'];
          $price = $product['price'] * $newQty;
          if (!updateOrInsertOrderItem($orderId, $productId, $newQty, $price)) {
            echo "Failed to update order item.";
          }
        } else {
          echo "Failed to update product stock.";
        }
      } else {
        $_SESSION['error_message'] = "Jumlah melebihi stok yang tersedia.";
      }
    }
  }
}

if (isset($_POST['bayar'])) {
  if (isset($_SESSION['order_id'])) {
    $orderId = $_SESSION['order_id'];
    $paymentDate = date("Y-m-d");
    $paymentAmount = $totalHarga;

    if (addPayment($orderId, $paymentDate, $paymentAmount)) {
      unset($_SESSION['pesanan']);
      $totalHarga = 0;
      unset($_SESSION['order_id']);
      $_SESSION['success_message'] = "Berhasil melakukan transaksi.";
    } else {
      echo "Failed to add payment.";
    }
  }
}

function calculateTotalHarga($pesanan, $products)
{
  $totalHarga = 0;
  foreach ($pesanan as $item) {
    $productId = $item['produk_id'];
    $qty = $item['qty'];
    $price = $products[$productId]['price'] * $qty;
    $totalHarga += $price;
  }
  return $totalHarga;
}

function increaseProductStock($productId, $quantity)
{
  include "connect.php";
  $stmt = $connect->prepare("UPDATE products SET stok = stok + ? WHERE product_id = ?");
  $stmt->bind_param("ii", $quantity, $productId);
  $stmt->execute();
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Selamat Datang</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
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

  <div class="container my-1">
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
  </div>

  <div class="container">
    <div class="row justify-content-center mt-4">
      <div class="col-md-10">
        <h3>Kasir</h3>
        <p> Hi saya
          <?php
          if (isset($_SESSION['username'])) {
            echo $_SESSION['username'];
          }
          ?>
        </p>

        <?php if (!isset($_SESSION['order_id'])) { ?>
          <form method="POST">
            <button type="submit" class="btn btn-primary" name="buat_pesanan">Buat Pesanan</button>
          </form>
        <?php } else { ?>
          <form method="POST">
            <button type="submit" class="btn btn-secondary" name="batal_pesanan">Batal</button>
          </form>
        <?php } ?>

        <div class="row mt-3" id="formPesanan" <?php if (!isset($_SESSION['order_id'])) { ?> style="display: none;"
          <?php } ?>>
          <div class="col-md-4">
            <form method="POST">
              <div class="form-group">
                <select class="form-control" name="produk">
                  <?php foreach ($products as $product) { ?>
                    <option value="<?php echo $product['product_id']; ?>"><?php echo $product['product_name']; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <input type="number" class="form-control" name="qty">
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-success" name="tambah_produk">Tambah</button>
              </div>
            </form>
          </div>
          <div class="col-md-8">
            <form method="POST">
              <?php if (isset($_SESSION['order_id'])) { ?>
                <button class="btn btn-warning text-white payment-btn" name="bayar">Bayar</button>
              <?php } else { ?>
                <button class="btn btn-primary" name="buat_pesanan">Buat Pesanan</button>
              <?php } ?>
              <h2 class="float-right tot-harga">Total Harga: Rp
                <?php echo number_format($totalHarga, 2, ",", "."); ?>
              </h2>
              <table class="table mt-3">
                <thead>
                  <tr>
                    <th class="text-center">Produk</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (isset($_SESSION['pesanan'])) {
                    $index = 1;
                    foreach ($_SESSION['pesanan'] as $item) {
                      $productId = $item['produk_id'];
                      $product = $products[$productId];
                      ?>
                      <tr>
                        <td class="text-center">
                          <?php echo $product['product_name']; ?>
                        </td>
                        <td class="text-center">
                          <?php echo $item['qty']; ?>
                        </td>
                        <td class="text-center">
                          <form method="POST" class="d-inline-block">
                            <input type="hidden" name="index" value="<?php echo $index - 1; ?>">
                            <button type="submit" class="btn btn-danger" name="delete_produk">
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                <path
                                  d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z" />
                              </svg>
                            </button>
                          </form>
                          <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#editModal<?php echo $index - 1; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                              class="bi bi-pen-fill" viewBox="0 0 16 16">
                              <path
                                d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001z" />
                            </svg>
                          </button>
                        </td>
                      </tr>

                      <!-- Modal -->
                      <div class="modal fade" id="editModal<?php echo $index - 1; ?>" tabindex="-1" role="dialog"
                        aria-labelledby="editModalLabel<?php echo $index - 1; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="editModalLabel<?php echo $index - 1; ?>">Edit Produk</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <form method="POST">
                              <div class="modal-body">
                                <input type="hidden" name="editIndex" value="<?php echo $index - 1; ?>">
                                <input type="hidden" name="editProduk" value="<?php echo $productId; ?>">
                                <div class="form-group">
                                  <label for="editQty">Qty:</label>
                                  <input type="number" class="form-control" name="editQty"
                                    value="<?php echo $item['qty']; ?>">
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                                <button type="submit" class="btn btn-primary" name="update_produk">Simpan</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <?php
                      $index++;
                    }
                  }
                  ?>
                </tbody>
              </table>
            </form>
          </div>
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
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>