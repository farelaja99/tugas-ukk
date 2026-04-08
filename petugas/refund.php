<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../auth/login.php");
    exit();
}

// PROSES ACTION
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = intval($_POST['id']);
    $data = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT product_id, quantity 
        FROM transactions 
        WHERE id='$id'
    "));

    if (isset($_POST['approve'])) {

        mysqli_query($conn, "
            UPDATE transactions 
            SET refund_status='approved' 
            WHERE id='$id'
        ");

        mysqli_query($conn, "
            UPDATE products 
            SET stock = stock + {$data['quantity']}
            WHERE id = {$data['product_id']}
        ");

    } elseif (isset($_POST['reject'])) {

        mysqli_query($conn, "
            UPDATE transactions 
            SET refund_status='rejected' 
            WHERE id='$id'
        ");
    }

    header("Location: refund.php");
    exit();
}

// AMBIL DATA REFUND
$query = mysqli_query($conn, "
    SELECT t.*, p.name 
    FROM transactions t
    JOIN products p ON p.id = t.product_id
    WHERE t.refund_status = 'requested'
    ORDER BY t.id DESC
");
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Kelola Refund</title>
        <style>
            body {
                font-family: Arial;
                background: #338C91;
                padding: 0;
                margin: 0;
            }

            /* HEADER */
            .header {
                background: #215E61;
                padding: 18px 40px;
                color: white;
                font-size: 20px;
                font-weight: bold;
            }

            .container {
                padding: 30px 60px;
            }

            h2 {
                color: #F5FBE6;
                margin-bottom: 20px;
            }

            /* BUTTON KEMBALI */
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

            /* CARD */
            .card {
                background: #F5FBE6;
                border-radius: 15px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            }

            .card b {
                color: #215E61;
            }

            /* BUTTON */
            .btn {
                padding: 8px 14px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                color: white;
                margin-right: 10px;
                transition: 0.3s;
            }

            .approve {
                background: #215E61;
            }

            .approve:hover {
                background: #174446;
            }

            .reject {
                background: #999;
            }

            .reject:hover {
                background: #777;
            }

        </style>
    </head>
    <body>
        <div class="header">
            Refund Management
        </div>
        <div class="container">

            <div class="back-btn">
                <a href="dashboard.php">← Kembali</a>
            </div>

            <h2>Permintaan Refund</h2>

            <?php if(mysqli_num_rows($query) == 0): ?>
                <p>Tidak ada request refund.</p>
            <?php endif; ?>

            <?php while($r = mysqli_fetch_assoc($query)): ?>

            <div class="card">
                <b><?= $r['name'] ?></b><br><br>

                <b>User:</b> <?= $r['customer_name'] ?><br>
                <b>Qty:</b> <?= $r['quantity'] ?><br>
                <b>Alasan:</b> <?= $r['refund_reason'] ?><br>
                <b>Tujuan Refund:</b> <?= $r['refund_target'] ?><br>

                <form method="POST" style="margin-top:10px;">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">

                    <button class="btn approve" name="approve">Approve</button>
                    <button class="btn reject" name="reject">Reject</button>
                </form>
            </div>

            <?php endwhile; ?>
        </div>        
    </body>
</html>