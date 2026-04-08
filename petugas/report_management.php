<?php
    include '../config/database.php';

    //hapus produk
    if(isset($_GET['delete_product'])){
        $id = $_GET['delete_product'];
        mysqli_query($conn, "DELETE FROM products WHERE id=$id");
        header("Location: report_management.php");
        exit;
    }
    
    //hapus transaksi
    if(isset($_GET['delete_transaction'])){
        $id = $_GET['delete_transaction'];
        mysqli_query($conn, "DELETE FROM transactions WHERE id=$id");
        header("Location: report_management.php");
        exit;
    }
    
    //ambil data
    $queryStock = mysqli_query($conn, "SELECT * FROM products");
    $querySales = mysqli_query($conn, "
        SELECT 
            p.name AS product_name,
            p.category,
            SUM(t.quantity) AS total_sold,
            SUM(t.quantity * p.price) AS total_revenue
        FROM transactions t
        JOIN products p ON t.product_id = p.id
        WHERE t.checkout_status = 'checkout'
        GROUP BY t.product_id
    ");
   $queryTransaction = mysqli_query($conn, "
        SELECT 
            t.id,
            t.customer_name,
            t.phone,
            t.address,
            t.payment_method,
            t.quantity,
            t.total_price,
            t.created_at,
            p.name AS product_name,
            p.category
        FROM transactions t
        JOIN products p ON t.product_id = p.id
        WHERE t.checkout_status = 'checkout'
    ");


?>

<!DOCTYPE html>
<html>
    <head>
        <title>Management Laporan</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <style>
            *{
                margin:0;
                padding:0;
                box-sizing:border-box;
                font-family:'Poppins',sans-serif;
            }

            body{
                background:#1E1E1E;
            }

            /* HEADER */
            .header{
                background: #215E61;
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

            .content h2{
                font-size:20px;
                margin-top:5px;
                color:#F5FBE6;
            }

            /* CONTENT */
            .content{
                background:#3C8D8B;
                padding:40px;
                min-height:100vh;
            }

            /* TABS */
            .tabs{
                display:flex;
                gap:15px;
                margin-bottom:25px;
            }

            .tab-btn{
                padding:10px 20px;
                border:none;
                cursor:pointer;
                background:#2F6F6D;
                color:white;
                border-radius:6px;
                font-weight:600;
            }

            .tab-btn.active{
                background:#E6EAD7;
                color:#2F6F6D;
            }

            /* TABLE */
            table{
                width:100%;
                border-collapse:collapse;
                background:white;
                border-radius:6px;
                overflow:hidden;
            }

            th{
                background:#2F6F6D;
                color:white;
                padding:12px;
            }

            td{
                padding:10px;
                text-align:center;
                border-bottom:1px solid #ddd;
            }

            tr:hover{
                background:#f2f2f2;
            }

            .btn-group {
                display:flex;
                justify-content:center;
                gap:8px;
            }

            .btn {
                padding:6px 12px;
                border-radius:6px;
                text-decoration:none;
                font-size:13px;
                display:inline-flex;
                align-items:center;
                gap:6px;
                font-weight:600;
                transition:0.2s;
            }

            /* DELETE (danger) */
            .btn-delete {
                background:#e74c3c;
                color:white;
            }

            .btn-delete:hover {
                background:#c0392b;
            }

            /* VIEW / PRINT */
            .btn-view {
                background:#215E61;
                color:white;
            }

            .btn-view:hover {
                background:#174446;
            }

            .tab-content{
                display:none;
            }

            .tab-content.active{
                display:block;
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

        <div class="content">
            <h2>Report Management</h2>
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab(event,'stock')">
                    <i class="fa-solid fa-box"></i> Stock
                </button>
                <button class="tab-btn" onclick="showTab(event,'sales')">
                    <i class="fa-solid fa-chart-line"></i> Sales
                </button>
                <button class="tab-btn" onclick="showTab(event,'transaction')">
                    <i class="fa-solid fa-receipt"></i> Transaction
                </button>
            </div>

            <!-- STOCK -->
            <div id="stock" class="tab-content active">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($row=mysqli_fetch_assoc($queryStock)){ ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['name'] ?></td>
                                <td><?= $row['category'] ?></td>
                                <td><?= $row['stock'] ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="?delete_product=<?= $row['id'] ?>"
                                            onclick="return confirm('Delete this product?')"
                                            class="btn btn-delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- SALES -->
            <div id="sales" class="tab-content">
                <table>
                   <thead>
                        <tr>
                            <th>No</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Total Terjual</th>
                            <th>Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($row=mysqli_fetch_assoc($querySales)){ ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['product_name'] ?></td>
                                <td><?= $row['category'] ?></td>
                                <td><?= $row['total_sold'] ?></td>
                                <td>Rp<?= number_format($row['total_revenue'],0,',','.') ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- TRANSACTION -->
            <div id="transaction" class="tab-content">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Payment</th>
                            <th>Date</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no=1; while($row=mysqli_fetch_assoc($queryTransaction)){ ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['customer_name'] ?></td>
                                <td><?= $row['product_name'] ?></td>
                                <td><?= $row['category'] ?></td>

                                <td><?= $row['phone'] ?></td>

                                <td style="text-align:left; max-width:200px;">
                                    <?= $row['address'] ?>
                                </td>

                                <td>
                                    <?php
                                    if($row['payment_method'] == 'transfer'){
                                        echo "Transfer";
                                    } elseif($row['payment_method'] == 'cod'){
                                        echo "COD";
                                    } else {
                                        echo "-";
                                    }
                                    ?>
                                </td>

                                <td><?= $row['created_at'] ?></td>
                                <td><?= $row['quantity'] ?></td>
                                <td>Rp<?= number_format($row['total_price'],0,',','.') ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="../admin/print_receipt.php?id=<?= $row['id'] ?>" 
                                            target="_blank"
                                            class="btn btn-view">
                                            <i class="fa-solid fa-receipt"></i>
                                        </a>

                                        <a href="?delete_transaction=<?= $row['id'] ?>"
                                            onclick="return confirm('Delete this transaction?')"
                                            class="btn btn-delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>

        <script>
            function showTab(evt, tabId){
                const tabs=document.querySelectorAll('.tab-content');
                const buttons=document.querySelectorAll('.tab-btn');

                tabs.forEach(tab=>tab.classList.remove('active'));
                buttons.forEach(btn=>btn.classList.remove('active'));

                document.getElementById(tabId).classList.add('active');
                evt.currentTarget.classList.add('active');
            }
        </script>

    </body>
</html>
