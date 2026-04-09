<?php
    session_start();
    include "../config/database.php";

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
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
        AND t.shipping_status='processed'
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
                font-family: 'Segoe UI', sans-serif;
                background: #338C91;
                margin:0;
            }

            /* HEADER */
            .header {
                background: #215E61;
                color: white;
                padding: 18px 40px;
                font-size: 22px;
                font-weight: bold;
                letter-spacing: 1px;
            }

            /* CONTAINER */
            .container {
                padding: 30px 60px;
            }

            /* BACK BUTTON */
            .back-btn a {
                display: inline-block;
                margin-bottom: 25px;
                background: #e5e9d5;
                color: #215E61;
                padding: 10px 20px;
                border-radius: 10px;
                text-decoration: none;
                font-weight: 600;
                transition: 0.3s;
            }
            .back-btn a:hover {
                background: #d1d8bd;
            }

            /* TITLE */
            h2 {
                color: #F5FBE6;
                margin-bottom: 15px;
            }

            /* CARD */
            .card {
                background: #F5FBE6;
                padding: 20px;
                border-radius: 16px;
                margin-bottom: 20px;
                display:flex;
                justify-content:space-between;
                align-items:center;
                gap:20px;
                box-shadow: 0 8px 20px rgba(0,0,0,0.15);
                transition: 0.3s;
            }

            .card:hover {
                transform: translateY(-5px);
            }

            /* TEXT */
            .card b {
                color: #215E61;
            }

            /* INPUT */
            input {
                padding: 10px;
                width: 220px;
                margin-top: 10px;
                border-radius: 8px;
                border: 1px solid #ccc;
                outline: none;
            }

            input:focus {
                border-color: #215E61;
            }

            /* BUTTON */
            .btn {
                padding: 10px 16px;
                background: #215E61;
                color: white;
                border: none;
                border-radius: 10px;
                cursor: pointer;
                transition: 0.3s;
                font-weight: 600;
            }

            .btn:hover {
                background: #174446;
                transform: scale(1.05);
            }

            /* IMAGE */
            img {
                width:140px;
                height:160px;
                object-fit:cover;
                border-radius:12px;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
                background: #F5FBE6;
                border-radius: 12px;
                overflow: hidden;
            }

            .table th {
                background: #215E61;
                color: white;
                padding: 12px;
                text-align: center;
            }

            .table td {
                padding: 12px;
                text-align: center;
                border-bottom: 1px solid #ddd;
                color: #215E61;
            }

            .table tr:hover {
                background: #e5e9d5;
            }

            .table img {
                width: 70px;
                height: 80px;
                object-fit: cover;
                border-radius: 8px;
            }

            .table input {
                width: 120px;
                padding: 6px;
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

            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Address</th>
                        <th>Courier</th>
                        <th>Qty</th>
                        <th>Image</th>
                        <th>Tracking</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($query) == 0): ?>
                        <tr><td colspan="8">No orders to ship</td></tr>
                    <?php endif; ?>

                    <?php while($r = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $r['name'] ?></td>
                        <td><?= $r['customer_name'] ?></td>
                        <td><?= $r['address'] ?></td>
                        <td><?= $r['shipping_courier'] ?></td>
                        <td><?= $r['quantity'] ?></td>
                        <td><img src="../uploads/<?= $r['photo'] ?>"></td>
                        <td>
                            <form method="POST" style="display:flex; gap:5px;">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <input type="text" name="resi" placeholder="Tracking" required>
                        </td>
                        <td>
                                <button class="btn">Ship</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2 style="margin-top:40px;">Shipping History</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Address</th>
                        <th>Courier</th>
                        <th>Tracking</th>
                        <th>Qty</th>
                        <th>Image</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($history) == 0): ?>
                        <tr><td colspan="7">No history</td></tr>
                    <?php endif; ?>

                    <?php while($h = mysqli_fetch_assoc($history)): ?>
                    <tr>
                        <td><?= $h['name'] ?></td>
                        <td><?= $h['customer_name'] ?></td>
                        <td><?= $h['address'] ?></td>
                        <td><?= $h['shipping_courier'] ?></td>
                        <td><?= $h['tracking_number'] ?></td>
                        <td><?= $h['quantity'] ?></td>
                        <td><img src="../uploads/<?= $h['photo'] ?>"></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>