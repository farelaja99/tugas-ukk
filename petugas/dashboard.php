<?php
    session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
        header("Location: ../auth/login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Petugas Dashboard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <style>
            *{
                box-sizing:border-box;
            }

            body{
                margin:0;
                font-family:Arial, sans-serif;
                background:#338C91;
            }

            /*navbar*/
            .header{
                background: #215E61;
                padding:18px 50px;
                display:flex;
                justify-content:space-between;
                align-items:center;
                color:white;
            }

            .logo{
                font-size:32px;
                font-weight:bold;
                font-style:italic;
            }

            .logout{
                color:white;
                text-decoration:none;
                font-size:16px;
            }

            .logout i{
                margin-right:8px;
            }

            .container{
                padding:40px 60px;
            }

            .container h2{
                color:#F5FBE6;
                font-size:32px;
                margin-bottom:40px;
            }

            .cards{
                display:grid;
                grid-template-columns:repeat(3, 1fr);
                gap:40px;
            }

            .card{
                background:#F5FBE6;
                padding:40px;
                border-radius:15px;
                text-align:center;
                transition:0.3s;
                cursor:pointer;
                text-decoration:none;
            }

            .card:hover{
                transform:translateY(-5px);
            }

            .card i{
                font-size:60px;
                color:#215E61;
                margin-bottom:20px;
            }

            .card h3{
                color:#215E61;
                margin-bottom:10px;
            }

            .card p{
                font-size:14px;
                color:#333;
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
            <h2>Welcome Officer <?php echo $_SESSION['username']; ?></h2>

            <div class="cards">

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

                <a href="report_management.php" class="card">
                    <i class="fa-solid fa-file-lines"></i>
                    <h3>Report Management</h3>
                    <p>Manage and review report</p>
                </a>

            </div>

        </div>
    </body>
</html>
