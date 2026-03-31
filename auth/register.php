<?php
    session_start();
    include "../config/database.php";

    if (isset($_POST['register'])) {

        $username = $_POST['username'];
        $email    = $_POST['email'];
        $password = md5($_POST['password']);
        $role     = "user";

        // cek username sudah ada atau belum
        $cek = mysqli_query($conn,"SELECT * FROM users WHERE username='$username' OR email='$email'");

        if (mysqli_num_rows($cek) > 0) {
            $error = "Username atau Email sudah digunakan";
        } else {

            $insert = mysqli_query($conn,"INSERT INTO users (username,email,password,role) 
                                        VALUES ('$username','$email','$password','$role')");

            if ($insert) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Registrasi gagal";
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
    <title>Register</title>
        <style>
            *{
                box-sizing:border-box;
            }

            body{
                margin:0;
                font-family:Arial, sans-serif;
                background:#1E1E1E;
            }

            .wrapper{
                display:flex;
                width:100%;
                height:100vh;
            }

            /* KIRI */
            .left{
                width:45%;
                background:#F5FBE6;
                padding:60px 50px;
                display:flex;
                flex-direction:column;
                justify-content:center;
            }

            .left h1{
                font-size:40px;
                color:#215E61;
            }

            .left img{
                width:500px;
                margin-top:20px;
            }

            /* KANAN */
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
                font-size:36px;
                margin-bottom:20px;
                border-bottom:2px solid #F5FBE6;
                display:inline-block;
                padding-bottom:10px;
            }

            .right h2{
                font-size:28px;
                margin-bottom:30px;
            }

            /* FORM */
            form{
                width:100%;
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
                background: #F5FBE6;
                font-weight:bold;
                cursor:pointer;
            }

            button:hover{
                background: #BCC0B3;
            }

            .error{
                color: #ffb3b3;
                margin-bottom:15px;
            }

            .login-text{
                margin-top:15px;
                font-size:14px;
            }

            .login-text a{
                color: #ffffff;
                text-decoration:underline;
            }
        </style>
    </head>

    <body>
        <div class="wrapper">

            <div class="left">
                <h1>E-COMMERCE</h1>
                <img src="../uploads/loginuser.png" alt="image">
            </div>

            <div class="right">
                <h1>Please Sign Up</h1>
                <h2>Sign up</h2>

                <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

                <form method="POST">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email Address" required>
                    <input type="password" name="password" placeholder="Password" required>

                    <button name="register">Sign up</button>
                </form>

                <div class="login-text">
                    Sudah punya akun? <a href="login.php">Login di sini</a>
                </div>
            </div>

        </div>
    </body>
</html>
