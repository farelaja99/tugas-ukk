<?php
    session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
        header("Location: ../auth/login.php");
        exit();
    }

    include "../config/database.php";

    //update status
    if(isset($_GET['action']) && isset($_GET['id'])){

        $id = intval($_GET['id']);
        $action = $_GET['action'];

        if($action == 'confirm'){
            $status = 'Paid';
        } elseif($action == 'cancel'){
            $status = 'Cancelled';
        } else {
            $status = 'Pending';
        }

        mysqli_query($conn,"UPDATE transactions 
                            SET status='$status' 
                            WHERE id='$id'");

        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }

    //ambil data
    $data = mysqli_query($conn, "
        SELECT t.*, p.name AS product_name, p.category
        FROM transactions t
        JOIN products p ON t.product_id = p.id
        ORDER BY t.id DESC
    ");
?>
<!DOCTYPE html>
<html>
    <head>
    <title>Transaction Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body{
            margin:0;
            font-family:'Segoe UI',sans-serif;
            background:#338C91;
        }

        .header{
            background:#215E61;
            padding:18px 50px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            color:white;
        }

        .logo{
            font-size:38px;
            font-weight:bold;
            font-style:italic;
        }

        .back-btn{
            background:#e5e9d5;
            color:#1b4e50;
            padding:8px 18px;
            border-radius:8px;
            text-decoration:none;
            display:flex;
            align-items:center;
            gap:8px;
            font-weight:600;
        }

        .container{
            padding:40px 60px;
            color:white;
        }

        .title{
            font-size:30px;
            margin-bottom:20px;
            color:#F5FBE6;
        }

        .table-wrapper{
            background:#e5e9d5;
            border-radius:12px;
            overflow:hidden;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th{
            background:#215E61;
            color:white;
            padding:15px;
            text-align:center;
        }

        td{
            padding:15px;
            text-align:center;
            border-bottom:1px solid #ccc;
            color:#215E61;
        }

        .action-btn{
            padding:7px 14px;
            border-radius:8px;
            font-size:13px;
            text-decoration:none;
            color:white;
            margin:0 3px;
            display:inline-block;
            cursor:pointer;
        }

        .view{ background:#215E61; }
        .print{ background:#215E61; }

        .modal{
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.6);
            display:flex;
            justify-content:center;
            align-items:center;
            opacity:0;
            pointer-events:none;
            transition:0.3s;
        }

        .modal.active{
            opacity:1;
            pointer-events:auto;
        }

        .modal-content{
            background:#F5FBE6;
            padding:20px;
            border-radius:12px;
            text-align:center;
        }

        .modal-content img{
            max-width:500px;
            border-radius:10px;
        }

        .close-btn{
            margin-top:15px;
            background:#c0392b;
            color:white;
            border:none;
            padding:8px 20px;
            border-radius:8px;
            cursor:pointer;
        }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="logo">Theomart</div>
            <a href="dashboard.php" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="container">

            <div class="title">Transaction Management</div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Payment</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Proof</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while($row=mysqli_fetch_assoc($data)) { ?>
                            <tr>
                                <td><?= $row['customer_name']; ?></td>
                                <td><?= $row['product_name']; ?></td>
                                <td><?= $row['category']; ?></td>

                                <td><?= $row['phone']; ?></td>

                                <td style="max-width:200px;text-align:left;">
                                    <?= $row['address']; ?>
                                </td>

                                <td>
                                    <?php
                                    if($row['payment_method'] == 'transfer'){
                                        echo "<span style='color:#2980b9;font-weight:bold;'>Transfer</span>";
                                    } elseif($row['payment_method'] == 'cod'){
                                        echo "<span style='color:#8e44ad;font-weight:bold;'>COD</span>";
                                    } else {
                                        echo "-";
                                    }
                                    ?>
                                </td>

                                <td>
                                    Rp <?= number_format($row['total_price'],0,',','.'); ?>
                                </td>

                                <!-- STATUS -->
                                <td>
                                    <?php 
                                    if($row['status'] == 'Paid'){
                                        echo "<span style='color:green;font-weight:bold;'>Paid</span>";
                                    }
                                    elseif($row['status'] == 'Cancelled'){
                                        echo "<span style='color:#c0392b;font-weight:bold;'>Cancelled</span>";
                                    }
                                    else{
                                        echo "<span style='color:#e67e22;font-weight:bold;'>Pending</span>";
                                    }
                                    ?>
                                </td>

                                <!-- PROOF -->
                                <td>
                                    <a class="action-btn view"
                                    onclick="openModal('../uploads/<?= $row['proof_photo']; ?>')">
                                    View
                                    </a>
                                </td>

                                <!-- ACTION -->
                                <td>
                                    <!-- Confirm (selalu ada) -->
                                    <a class="action-btn"
                                    style="background:#27ae60;"
                                    href="?action=confirm&id=<?= $row['id']; ?>">
                                    Confirm
                                    </a>

                                    <!-- Cancel (selalu ada) -->
                                    <a class="action-btn"
                                    style="background:#c0392b;"
                                    href="?action=cancel&id=<?= $row['id']; ?>">
                                    Cancel
                                    </a>
                                        
                                    <!-- Print -->
                                    <a class="action-btn print"
                                    href="../admin/print_receipt.php?id=<?= $row['id']; ?>" 
                                    target="_blank">
                                    Print
                                    </a>

                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- MODAL -->
        <div class="modal" id="proofModal">
            <div class="modal-content">
                <img id="proofImage" src="">
                <br>
                <button class="close-btn" onclick="closeModal()">Close</button>
            </div>
        </div>

        <script>
        function openModal(imagePath){
            document.getElementById('proofImage').src = imagePath;
            document.getElementById('proofModal').classList.add('active');
        }

        function closeModal(){
            document.getElementById('proofModal').classList.remove('active');
        }

        window.onclick = function(e){
            const modal = document.getElementById('proofModal');
            if(e.target == modal){
                closeModal();
            }
        }
        </script>
    </body>
</html>
