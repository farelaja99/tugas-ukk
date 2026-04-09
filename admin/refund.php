<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
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

$history = mysqli_query($conn, "
    SELECT t.*, p.name 
    FROM transactions t
    JOIN products p ON p.id = t.product_id
    WHERE t.refund_status IN ('approved','rejected')
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
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 20px;
            }

            .card-left {
                flex: 1;
            }

            .card-right {
                width: 140px;
                text-align: center;
            }

            .card-right img {
                width: 100%;
                border-radius: 10px;
                cursor: pointer;
                transition: 0.2s;
            }

            .card-right img:hover {
                transform: scale(1.05);
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

            /* BUTTON */
            .btn {
                padding: 6px 10px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                color: white;
                margin: 2px;
            }

            .approve { background: #215E61; }
            .approve:hover { background: #174446; }

            .reject { background: #999; }
            .reject:hover { background: #777; }

            /* BADGE */
            .badge {
                padding: 5px 10px;
                border-radius: 8px;
                font-size: 12px;
                font-weight: bold;
            }

            .success {
                background: #d4edda;
                color: #155724;
            }

            .danger {
                background: #f8d7da;
                color: #721c24;
            }

            .modal {
                display: none;
                position: fixed;
                z-index: 999;
                padding-top: 60px;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.8);
            }

            .modal-content {
                display: block;
                margin: auto;
                max-width: 70%;
                max-height: 80vh;
                border-radius: 10px;
            }

            .close {
                position: absolute;
                top: 20px;
                right: 40px;
                color: white;
                font-size: 35px;
                font-weight: bold;
                cursor: pointer;
            }

        </style>
    </head>
    <body>
        <div class="header">
            Refund Management
        </div>
        <div class="container">

            <div class="back-btn">
                <a href="dashboard.php">← Back</a>
            </div>

            <h2>Refund Requests</h2>

            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>User</th>
                        <th>Qty</th>
                        <th>Reason</th>
                        <th>Refund To</th>
                        <th>Proof</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($query) == 0): ?>
                        <tr><td colspan="7">No refund request</td></tr>
                    <?php endif; ?>

                    <?php while($r = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $r['name'] ?></td>
                        <td><?= $r['customer_name'] ?></td>
                        <td><?= $r['quantity'] ?></td>
                        <td><?= $r['refund_reason'] ?></td>
                        <td><?= $r['refund_target'] ?></td>
                        <td>
                            <?php if(!empty($r['refund_proof'])): ?>
                                <img src="../uploads/<?= $r['refund_proof'] ?>" onclick="previewImage(this.src)">
                            <?php else: ?>
                                <i>No proof</i>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                <button class="btn approve" name="approve">Approve</button>
                                <button class="btn reject" name="reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2 style="margin-top:40px;">Refund History</h2>

            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>User</th>
                        <th>Qty</th>
                        <th>Reason</th>
                        <th>Refund To</th>
                        <th>Status</th>
                        <th>Proof</th>
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
                        <td><?= $h['quantity'] ?></td>
                        <td><?= $h['refund_reason'] ?></td>
                        <td><?= $h['refund_target'] ?></td>
                        <td>
                            <?php if($h['refund_status'] == 'approved'): ?>
                                <span class="badge success">Approved</span>
                            <?php else: ?>
                                <span class="badge danger">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(!empty($h['refund_proof'])): ?>
                                <img src="../uploads/<?= $h['refund_proof'] ?>" onclick="previewImage(this.src)">
                            <?php else: ?>
                                <i>No proof</i>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div id="imageModal" class="modal">
            <span class="close" onclick="closeModal()">&times;</span>
            <img class="modal-content" id="imgPreview">
        </div>

        <script>
        function previewImage(src){
            document.getElementById("imageModal").style.display = "block";
            document.getElementById("imgPreview").src = src;
        }

        function closeModal(){
            document.getElementById("imageModal").style.display = "none";
        }
        </script>
    </body>
</html>