<?php
session_start();
include "../config/database.php";


if (isset($_POST['login'])) {

    $u = $_POST['username'];
    $p = md5($_POST['password']);

    //cek username dan password
    $q = mysqli_query($conn,"
        SELECT * FROM users 
        WHERE username='$u' 
        AND password='$p'
    ");

    if (mysqli_num_rows($q) > 0) {

        $data = mysqli_fetch_assoc($q); 

        $_SESSION['username'] = $data['username']; 
        $_SESSION['role'] = $data['role']; 

        // Redirect sesuai role user
        if ($data['role'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        } elseif ($data['role'] == 'petugas') {
            header("Location: ../petugas/dashboard.php");
        } else {
            header("Location: ../users/dashboard.php");
        }

        exit(); 

    } else {
        $error = "Login gagal";
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Admin Login</title>
        <style>
            *{
                box-sizing: border-box;
            }

            body{
                margin:0;
                font-family:Arial, sans-serif;
                background:#1e1e1e;
            }

            .wrapper{
                display:flex;
                width:100%;
                height:100vh;
            }

            .left{
                width:45%;
                background:#F5FBE6;
                padding:60px 50px;
                display:flex;
                flex-direction:column;
                justify-content:center;
            }

            .left h1{
                font-size:32px;
                color: #215E61;
            }

            .left img{
                width:500px;
                margin-top:20px;
                position: relative;
            }

            .right{
                width:55%;
                background: #338C91;
                padding:60px 80px;
                color:white;
                display:flex;
                flex-direction:column;
                justify-content:center;
                text-align:center;
            }

            .right h1{
                font-size:50px;
                position: relative;
                bottom:60px;
            }

            .right h2{
                font-size:30px;
                position: relative;
                bottom:50px;
            }

            input{
                width:100%;
                height:55px;
                padding:12px;
                margin-bottom:18px;
                border-radius:10px;
                border:none;
                background:#F5FBE6;
                font-size:14px;
            }

            button{
                width:200px;
                height:50px;
                border:none;
                border-radius:10px;
                background:#F5FBE6;
                font-weight:bold;
                cursor:pointer;
            }

            button:hover{
                background:#BCC0B3;
            }

            .error{
                color:#ffb3b3;
                margin-bottom:10px;
            }

            /* REGISTER TEXT */
            .register-text{
                margin-top:15px;
                font-size:14px;
            }

            .register-text a{
                color: #ffffff;
                text-decoration:underline;
                font-weight:bold;
            }
        </style>
    </head>

    <body>
        <div class="wrapper">
            <div class="left">
                <h1>E-COMMERCE</h1>
                <img src="../uploads/loginadmin.png" alt="admin">
            </div>

            <div class="right">
                <h1><u>Welcome to Theomart</u></h1>
                <h2>Sign in</h2>

                <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

                <form method="POST">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button name="login">Sign in</button>
                </form>

                <div class="register-text">
                    Belum punya akun? <a href="register.php">Daftar di sini</a>
                </div>
            </div>

        </div>
    </body>
</html>
