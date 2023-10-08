<?php
session_start();
include "connect.php";

$delete_id = $_POST['delete-id'];
$delete_order = $_POST['delete-order'];

$query_payments = "DELETE FROM payments WHERE order_id = $delete_order";
$result_payments = mysqli_query($connect, $query_payments);

$query_orderitems = "DELETE FROM orderitems WHERE order_id = $delete_order";
$result_orderitems = mysqli_query($connect, $query_orderitems);

$query_orders = "DELETE FROM orders WHERE order_id = $delete_order";
$result_orders = mysqli_query($connect, $query_orders);

if ($result_payments && $result_orderitems && $result_orders) {
    $_SESSION['success_message'] = 'Data berhasil dihapus';
} else {
    $_SESSION['error_message'] = 'Terjadi kesalahan dalam menghapus data.';
}

mysqli_close($connect);

header('Location: payment-admin.php');
exit;

?>