<?php
    include "../config/database.php";
    
    //ambil data
    $id = $_GET['id'];
    $data = mysqli_query($conn, "
        SELECT t.*, p.name AS product_name, p.category
        FROM transactions t
        JOIN products p ON t.product_id = p.id
        ORDER BY t.id DESC
    ");
    $row = mysqli_fetch_assoc($data);
?>

<!DOCTYPE html>
<html>
    <head>
    <title>Receipt</title>
    <style>
        body{
            font-family:Arial;
            padding:40px;
        }
        .receipt{
            border:1px solid #000;
            padding:20px;
            width:400px;
        }
        h2{
            text-align:center;
        }
    </style>
    </head>
    <body onload="window.print()">

    <div class="receipt">
        <h2>Theomart</h2>
        <hr>
        <p><strong>Customer:</strong> <?= $row['customer_name']; ?></p>
        <p><strong>Product:</strong> <?= $row['product_name']; ?></p>
        <p><strong>Category:</strong> <?= $row['category']; ?></p>
        <p><strong>Price:</strong> Rp <?= number_format($row['total_price'],0,',','.'); ?></p>
        <p><strong>Date:</strong> <?= $row['created_at']; ?></p>
        <hr>
        <p style="text-align:center;">Thank you for your purchase</p>
    </div>

    </body>
</html>
