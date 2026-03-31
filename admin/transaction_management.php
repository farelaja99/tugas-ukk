<?php
    session_start();
    include "../config/database.php";

    //ambil data
    $data = mysqli_query($conn, "
        SELECT 
            t.*,
            p.name AS product_name,
            p.category
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

            /* HEADER */
            .header{
                background:#215E61;
                padding:18px 50px;
                display:flex;
                justify-content:space-between;
                align-items:center;
                color:#F5FBE6;
            }

            .logo{
                font-size:38px;
                font-weight:bold;
                font-style:italic;
                color:#F5FBE6;
            }

            .back-btn{
                background:#F5FBE6;
                color:#215E61;
                padding:8px 18px;
                border-radius:8px;
                text-decoration:none;
                display:flex;
                align-items:center;
                gap:8px;
                font-weight:600;
            }

            /* CONTENT */
            .container{
                padding:40px 60px;
                color:#F5FBE6;
            }

            .title{
                font-size:30px;
                margin-bottom:20px;
                color:#F5FBE6;
            }

            /* TABLE */
            .table-wrapper{
                background:#F5FBE6;
                border-radius:12px;
                overflow:hidden;
            }

            table{
                width:100%;
                border-collapse:collapse;
            }

            th{
                background:#215E61;
                color:#F5FBE6;
                padding:15px;
                text-align:center;
                font-size:14px;
            }

            td{
                padding:12px;
                text-align:center;
                border-bottom:1px solid #338C91;
                color:#215E61;
                font-size:14px;
                background:#F5FBE6;
            }

            /* ACTION BUTTON */
            .action-btn{
                padding:7px 14px;
                border-radius:8px;
                font-size:13px;
                text-decoration:none;
                color:#F5FBE6;
                margin:0 3px;
                display:inline-block;
                cursor:pointer;
            }

            .view{
                background: #215E61;
            }

            .print{
                background: #215E61;
            }

            /* STATUS */
            .status{
                padding:5px 10px;
                border-radius:6px;
                font-size:12px;
                font-weight:bold;
            }

            .pending{
                background: #ffdd00;
                color: #F5FBE6;
            }

            .paid{
                background: #2bff00;
                color: #F5FBE6;
            }

            .cancelled{
                background: #ff0000;
                color: #F5FBE6;
            }

            /* MODAL */
            .modal{
                position:fixed;
                top:0;
                left:0;
                width:100%;
                height:100%;
                background:#215E61;
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
                color:#215E61;
            }

            .modal-content img{
                max-width:500px;
                max-height:500px;
                border-radius:10px;
            }

            .close-btn{
                margin-top:15px;
                background: #ff0000;
                color: #F5FBE6;
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
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Price</th>
                            <th>Proof</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while($row=mysqli_fetch_assoc($data)) { ?>
                            <tr>
                                <td><?= $row['customer_name']; ?></td>
                                <td><?= $row['phone']; ?></td>
                                <td style="max-width:200px;text-align:left;">
                                <?= $row['address']; ?>
                                </td>
                                <td><?= $row['product_name']; ?></td>
                                <td><?= $row['category']; ?></td>
                                <td>
                                <?php
                                    if($row['payment_method']=="cod"){
                                    echo "COD";
                                    }else if($row['payment_method']=="transfer"){
                                    echo "Transfer";
                                    }else{
                                    echo "-";
                                    }
                                ?>
                                </td>
                                <td>
                                <?php
                                    $status = $row['status'];

                                    if($status=="Pending"){
                                    echo "<span class='status pending'>Pending</span>";
                                    }
                                    else if($status=="Paid"){
                                    echo "<span class='status paid'>Paid</span>";
                                    }
                                    else if ($status=="Cancelled"){
                                    echo "<span class='status cancelled'>Cancelled</span>";
                                    }
                                ?>
                                </td>
                                <td>Rp <?= number_format($row['total_price'],0,',','.'); ?></td>
                                <td>
                                    <?php if($row['proof_photo']) { ?>
                                    <a class="action-btn view"
                                    onclick="openModal('../uploads/<?= $row['proof_photo']; ?>')">
                                    View
                                    </a>
                                    <?php } else { ?>
                                    -
                                    <?php } ?>
                                </td>
                                <td>
                                    <a class="action-btn print"
                                    href="print_receipt.php?id=<?= $row['id']; ?>"
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
                <img id="proofImage">
                <br>
                <button class="close-btn" onclick="closeModal()">
                Close
                </button>
            </div>
        </div>

        <script>

        function openModal(imagePath){
        document.getElementById('proofImage').src=imagePath;
        document.getElementById('proofModal').classList.add('active');
        }

        function closeModal(){
        document.getElementById('proofModal').classList.remove('active');
        }

        window.onclick=function(e){
        let modal=document.getElementById('proofModal');
        if(e.target==modal){
        closeModal();
        }
        }

        </script>

    </body>
</html>