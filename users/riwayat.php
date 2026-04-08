<?php
    session_start();
    include "../config/database.php";

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
        header("Location: ../auth/login.php");
        exit();
    }

    $username = $_SESSION['username'];

// CANCEL ORDER

    if (isset($_GET['cancel'])) {

        $id = intval($_GET['cancel']);

        // ambil data buat balikin stock
        $data = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT product_id, quantity 
            FROM transactions 
            WHERE id='$id'
            AND customer_name='$username'
        "));

        if ($data) {

            // update status jadi cancelled
            mysqli_query($conn, "
                UPDATE transactions
                SET status='Cancelled'
                WHERE id='$id'
                AND status IN ('Pending','Paid')
            ");

            // balikin stock
            mysqli_query($conn, "
                UPDATE products
                SET stock = stock + {$data['quantity']}
                WHERE id = {$data['product_id']}
            ");
        }

        header("Location: riwayat.php");
        exit();
    }


    //ambil data
    $query = mysqli_query($conn, "
        SELECT 
            t.id,
            t.quantity,
            t.total_price,
            t.status,
            t.created_at,
            t.refund_status,
            t.payment_method,
            t.shipping_status,
            t.shipping_courier,
            t.tracking_number,
            p.name,
            p.photo
        FROM transactions t
        JOIN products p ON p.id = t.product_id
        WHERE t.customer_name = '$username'
        ORDER BY t.id DESC
    ");

    $transactions = [];

    while ($row = mysqli_fetch_assoc($query)) {
        $transactions[] = $row;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Riwayat Transaksi - Theomart</title>
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <style>

            body {
                margin: 0;
                font-family: 'Segoe UI', sans-serif;
                background: #338C91;
            }


            /* SIDEBAR */
            .sidebar {
                width: 200px;
                height: 100vh;
                background: #215E61;
                position: fixed;
                padding: 25px;
                color: white;
            }

            .logo {
                font-size: 28px;
                font-weight: bold;
                margin-bottom: 30px;
            }

            .back {
                background: #e5e9d5;
                color: #215E61;
                padding: 10px;
                border-radius: 8px;
                text-decoration: none;
                display: block;
                width: 100px;
                text-align: center;
            }


            /* CONTENT */
            .content {
                margin-left: 260px;
                padding: 40px;
            }

            .title {
                color: white;
                font-size: 40px;
                font-weight: bold;
                margin-bottom: 30px;
            }


            /* CARD */
            .card {
                background: #e5e9d5;
                border-radius: 12px;
                padding: 15px;
                margin-bottom: 20px;

                display: flex;
                justify-content: space-between;
                align-items: center;
            }


            /* LEFT */
            .card-left {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .card img {
                width: 200px;
                height: 220px
                border-radius: 8px;
            }

            .product-name {
                font-weight: bold;
                font-size: 18px;
            }

            .price {
                color: #333;
            }


            /* RIGHT */
            .card-right {
                display: flex;
                flex-direction: column;
                align-items: flex-end; /* tetep rata kanan */
                gap: 5px;
            }

            .card-right a {
                font-size: 13px;
                padding: 6px 10px;
                white-space: nowrap;
            }


            /* STATUS */
            .status {
                padding: 8px 16px;
                border-radius: 8px;
                color: white;
                font-weight: bold;
                display: inline-block;
                margin-bottom: 8px;
            }

            .Pending {
                background: #f39c12;
            }

            .Paid {
                background: #27ae60;
            }

            .Cancelled {
                background: #c0392b;
            }

            .date {
                font-size: 14px;
                color: #333;
            }

            .item {
                font-size: 14px;
                color: #333;
            }

            .empty {
                background: #e5e9d5;
                padding: 20px;
                border-radius: 10px;
                text-align: center;
            }

            .shipping {
                padding: 6px 12px;
                border-radius: 6px;
                color: white;
                font-size: 12px;
                margin-top: 5px;
            }

            .diproses {
                background: orange;
            }

            .dikirim {
                background: #3498db;
            }

            .selesai {
                background: #27ae60;
            }

        </style>
    </head>
    <body>

        <!-- SIDEBAR -->
        <div class="sidebar">

            <div class="logo">
                Theomart
            </div>

            <a href="dashboard.php" class="back">
                <i class="fa fa-arrow-left"></i> Back
            </a>

        </div>


        <!-- CONTENT -->
        <div class="content">

            <div class="title">
                History Order
            </div>

            <?php if (!empty($transactions)): ?>
                <?php foreach ($transactions as $t): ?>
                    <?php
                    $status_class = "";

                    if ($t['status'] == "Pending") {
                        $status_class = "Pending";
                    } elseif ($t['status'] == "Paid") {
                        $status_class = "Paid";
                    } elseif ($t['status'] == "Cancelled") {
                        $status_class = "Cancelled";
                    }
                    ?>
                    <div class="card">
                        <div class="card-left">
                            <img src="../uploads/<?= htmlspecialchars($t['photo']) ?>">
                            <div>
                                <div class="product-name">
                                    <?= htmlspecialchars($t['name']) ?>
                                </div>
                                <div class="price">
                                    Rp <?= number_format($t['total_price'], 0, ',', '.') ?>
                                </div>
                                <div class="payment_method">
                                    <? htmlspecialchars($t['payment_method'])?>
                                </div>
                            </div>
                        </div>


                        <div class="card-right">
                            <div class="status <?= $status_class ?>">
                                <?= $t['status'] ?>
                            </div>
                            <div class="item">
                                Item: <?= $t['quantity'] ?>
                            </div>
                            <div class="date">
                                <?= date("M d, Y", strtotime($t['created_at'])) ?>
                            </div>
                              <!-- REFUND STATUS -->
                            <?php if ($t['refund_status'] == 'requested'): ?>
                                <div style="color:orange; font-weight:bold;">
                                    Refund Requested
                                </div>
                            <?php elseif ($t['refund_status'] == 'approved'): ?>
                                <div style="color:green; font-weight:bold;">
                                    Refund Approved
                                </div>
                            <?php elseif ($t['refund_status'] == 'rejected'): ?>
                                <div style="color:red; font-weight:bold;">
                                    Refund Rejected
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($t['shipping_status'])): ?>
                                <div class="item">
                                    Shipping: <?= ucfirst($t['shipping_status']) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($t['shipping_courier'])): ?>
                                <div class="item">
                                    Courier: <?= $t['shipping_courier'] ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($t['tracking_number'])): ?>
                                <div class="item">
                                    Tracking No: <?= $t['tracking_number'] ?>
                                </div>
                            <?php endif; ?>

                            <!-- BUTTON REFUND -->
                            <?php if ($t['payment_method'] == 'transfer' && $t['refund_status'] == 'none'): ?>
                                <a href="refund.php?id=<?= $t['id'] ?>" 
                                style="display:inline-block; margin-top:8px; background:#c0392b; color:white; padding:6px 10px; border-radius:6px; text-decoration:none;">
                                    request a refund
                                </a>
                            <?php endif; ?>

                            <?php if (
                                ($t['status'] == 'Pending' || $t['status'] == 'Paid') 
                                && ($t['shipping_status'] == 'processed' || $t['shipping_status'] == 'pending')
                            ): ?>
                                <a href="?cancel=<?= $t['id'] ?>"
                                    onclick="return confirm('Cancel this order?')"
                                    style="background:#e74c3c; color:white; padding:6px 10px; border-radius:6px; text-decoration:none; margin-top:5px;">
                                    Cancel Order
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty">
                    There are no transactions yet
                </div>
            <?php endif; ?>
        </div>
        
    </body>
</html>