<?php
    session_start();
    include "../config/database.php";

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
        header("Location: ../auth/login.php");
        exit();
    }

    // PROSES KIRIM
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $id = intval($_POST['id']);
        $resi = mysqli_real_escape_string($conn, $_POST['resi']);

        mysqli_query($conn, "
            UPDATE transactions 
            SET shipping_status='shipped', tracking_number='$resi'
            WHERE id='$id'
        ");

        header("Location: shipping.php");
        exit();
    }

    // AMBIL DATA
    $query = mysqli_query($conn, "
        SELECT t.*, p.name, p.photo 
        FROM transactions t
        JOIN products p ON p.id = t.product_id
        WHERE t.status='Paid' 
        AND t.shipping_status='diproses'
        ORDER BY t.id DESC
    ");

    $history = mysqli_query($conn, "
        SELECT t.*, p.name, p.photo 
        FROM transactions t
        JOIN products p ON p.id = t.product_id
        WHERE t.status='Paid' 
        AND t.shipping_status='shipped'
        ORDER BY t.id DESC
    ");
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Shipping Management</title>

        <style>
            body {
                font-family: Arial;
                background: #338C91;
                margin:0;
            }

            .header {
                background: #215E61;
                color: white;
                padding: 18px 40px;
                font-size: 20px;
                font-weight: bold;
            }

            .container {
                padding: 30px 60px;
            }

            .back-btn a {
                display: inline-block;
                margin-bottom: 20px;
                background: #215E61;
                color: white;
                padding: 10px 18px;
                border-radius: 10px;
                text-decoration: none;
                transition: 0.3s;
            }

            .back-btn a:hover {
                background: #174446;
            }

            h2 {
                color: #F5FBE6;
            }

            .card {
                background: #F5FBE6;
                padding: 20px;
                border-radius: 15px;
                margin-bottom: 20px;
                display:flex;
                justify-content:space-between;
                align-items:center;
            }

            input {
                padding: 8px;
                width: 200px;
                margin-top: 10px;
            }

            .btn {
                padding: 8px 14px;
                background: #215E61;
                color: white;
                border: none;
                border-radius: 8px;
                cursor: pointer;
            }

            .btn:hover {
                background: #174446;
            }

            img {
                width:160px;
                height:180px;
                object-fit:cover;
                border-radius:10px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            Shipping Management
        </div>

        <div class="container">
            <div class="back-btn">
                <a href="dashboard.php">← Back</a>
            </div>
            <h2>Orders Ready to Ship</h2>

            <?php if(mysqli_num_rows($query) == 0): ?>
                <p>No orders to ship.</p>
            <?php endif; ?>

            <?php while($r = mysqli_fetch_assoc($query)): ?>
                <div class="card">

                    <!-- KIRI -->
                    <div>
                        <b>Product:</b> <?= $r['name'] ?><br>
                        <b>Customer:</b> <?= $r['customer_name'] ?><br>
                        <b>Address:</b> <?= $r['address'] ?><br>
                        <b>Courier:</b> <?= $r['shipping_courier'] ?><br>
                        <b>Qty:</b> <?= $r['quantity'] ?><br>

                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>">
                            <br>
                            <input type="text" name="resi" placeholder="Input tracking number" required>
                            <br><br>
                            <button class="btn">Ship Now</button>
                        </form>
                    </div>

                    <!-- KANAN -->
                    <div>
                        <img src="../uploads/<?= $r['photo'] ?>">
                    </div>
                </div>
            <?php endwhile; ?>

            <h2 style="margin-top:50px;">Shipping History</h2>

            <?php if(mysqli_num_rows($history) == 0): ?>
                <p>No shipping history.</p>
            <?php endif; ?>

            <?php while($h = mysqli_fetch_assoc($history)): ?>
                <div class="card">

                    <!-- KIRI -->
                    <div>
                        <b>Product:</b> <?= $h['name'] ?><br>
                        <b>Customer:</b> <?= $h['customer_name'] ?><br>
                        <b>Address:</b> <?= $h['address'] ?><br>
                        <b>Courier:</b> <?= $h['shipping_courier'] ?><br>
                        <b>Tracking:</b> <?= $h['tracking_number'] ?><br>
                        <b>Qty:</b> <?= $h['quantity'] ?><br>
                    </div>

                    <!-- KANAN -->
                    <div>
                        <img src="../uploads/<?= $h['photo'] ?>">
                    </div>
                </div>
            <?php endwhile; ?>

        </div>
    </body>
</html>