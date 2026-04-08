<?php
    session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        header("Location: ../auth/login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Admin Dashboard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <style>
           *{
                box-sizing:border-box;
            }

            body{
                margin:0;
                font-family:Arial, sans-serif;
                background: #338C91;
            }

            /*navbar*/
            .header{
                background: #215E61;
                padding:18px 50px;
                display:flex;
                justify-content:space-between;
                align-items:center;
                color: #F5FBE6;
            }

            .logo{
                font-size:38px;
                font-weight:bold;
                font-style:italic;
                color: #F5FBE6;
            }

            .logout{
                color: #F5FBE6;
                text-decoration:none;
                font-size:16px;
            }

            .logout i{
                margin-right:8px;
            }

            /*content*/
            .container{
                padding:40px 60px;
            }

            .container h2{
                font-size:36px;
                margin-bottom:40px;
                color: #F5FBE6;
            }

            /*card*/
            .cards{
                display:grid;
                grid-template-columns:repeat(3, 1fr);
                gap:40px;
            }

            .card{
                background: #F5FBE6;
                padding:40px;
                border-radius:15px;
                text-align:center;
                transition:0.3s;
                cursor:pointer;
            }

            .card:hover{
                transform:translateY(-5px);
                background: #215E61;
            }

            .card:hover i,
            .card:hover h3,
            .card:hover p{
                color: #F5FBE6;
            }

            .card i{
                font-size:60px;
                color: #215E61;
                margin-bottom:20px;
            }

            .card h3{
                color: #215E61;
                margin-bottom:10px;
            }

            .card p{
                font-size:14px;
                color: #215E61;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="logo">Theomart</div>
            <a href="../auth/logout.php" class="logout">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>

        <div class="container">
            <h2>Welcome <?php echo $_SESSION['username']; ?></h2>

            <div class="cards">

                <a href="user_management.php" class="card">
                    <i class="fa-solid fa-users-gear"></i>
                    <h3>User Management</h3>
                    <p>Manage data user and officer</p>
                </a>

                <a href="product_management.php" class="card">
                    <i class="fa-solid fa-box"></i>
                    <h3>Product Management</h3>
                    <p>Manage and organize product</p>
                </a>

                <a href="transaction_management.php" class="card">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <h3>Transaction Management</h3>
                    <p>Manage customer transaction</p>
                </a>

                <a href="backup_restore.php" class="card">
                    <i class="fa-solid fa-database"></i>
                    <h3>Backup and Restore</h3>
                    <p>Backup and restore database</p>
                </a>

                <a href="report_management.php" class="card">
                    <i class="fa-solid fa-file-lines"></i>
                    <h3>Report Management</h3>
                    <p>Manage and review report</p>
                </a>

                 <a href="refund.php" class="card">
                    <i class="fa-solid fa-rotate-left"></i>
                    <h3>Refund Management</h3>
                    <p>Process and manage refund requests</p>
                </a>

                <a href="shipping.php" class="card">
                    <i class="fa-solid fa-truck"></i>
                    <h3>Shipping Management</h3>
                    <p>Handle product delivery and tracking</p>
                </a>
            </div>
        </div>
    </body>
</html>
