<?php
include "connect.php";

if (isset($_GET['id'])) {
    $paymentId = $_GET['id'];

    $query = "SELECT p.product_name, oi.quantity, oi.price 
    FROM payments py JOIN orders o ON py.order_id = o.order_id 
    JOIN orderitems oi ON o.order_id = oi.order_id 
    JOIN products p ON oi.product_id = p.product_id 
    WHERE py.payment_id = '$paymentId';";
    $result = mysqli_query($connect, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo '<td>' . $row['product_name'] . '</td>';
        echo '<td class="text-left">' . $row['quantity'] . '</td>';
        echo '<td class="text-left">Rp ' . number_format($row['price'], 2, ',', '.') . '</td>';
        echo "</tr>";
    }

} else {
    echo 'Data barang tidak ditemukan.';
}

mysqli_close($connect);
?>