<?php
    session_start();
    include "../config/database.php";

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        header("Location: ../auth/login.php");
        exit();
    }

    //hapus user
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
        header("Location: user_management.php");
        exit();
    }

    //tambah petugas
    if (isset($_POST['add_petugas'])) {
        $username = $_POST['username'];
        $email    = $_POST['email'];
        $password = md5($_POST['password']);
        $role     = "petugas";

        mysqli_query($conn,"INSERT INTO users(username,email,password,role)
                            VALUES('$username','$email','$password','$role')");

        header("Location: user_management.php");
        exit();
    }

    $data = mysqli_query($conn, "SELECT * FROM users 
                                WHERE role='user' OR role='petugas'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body{
            margin:0;
            font-family:'Segoe UI',sans-serif;
            background: #338C91;
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
            background:#F5FBE6;
            color:#1b4e50;
            padding:8px 18px;
            border-radius:8px;
            text-decoration:none;
            display:flex;
            align-items:center;
            gap:8px;
            font-weight:600;
        }

        .back-btn:hover{
            opacity:0.8;
        }

        /* CONTAINER */
        .container{
            max-width:1100px;
            margin:40px auto;
            background:#F5FBE6;
            padding:30px;
            border-radius:12px;
            box-shadow:0 10px 25px rgba(0,0,0,0.2);
        }

        .title-row{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:25px;
        }

        .title-row h2{
            margin:0;
            color:#215E61;
        }

        .btn-add{
            background: #215E61;
            color:white;
            padding:8px 15px;
            border-radius:6px;
            cursor:pointer;
            font-size:13px;
        }

        .btn-add:hover{
            background: #174143;
        }

        /* TABLE */
        table{
            width:100%;
            border-collapse:collapse;
            table-layout:fixed;
            background-color: #F5FBE6;
            border-radius:10px;
        }

        th, td{
            padding:12px 15px;
            text-align:center;
            font-size:14px;
        }

        th{
            background: #215E61;
            color:white;
            font-weight:600;
        }

        td{
            border-bottom:1px solid #e0e0e0;
        }

        tr:hover{
            background:#F5FBE6;
        }

        .btn-delete{
            background:#c0392b;
            color:white;
            padding:5px 10px;
            border-radius:5px;
            text-decoration:none;
            font-size:12px;
        }

        .btn-delete:hover{
            background:#a93226;
        }

        /* MODAL */
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
            transition:0.3s ease;
        }

        .modal.active{
            opacity:1;
            pointer-events:auto;
        }

        .modal-content{
            background:#F5FBE6;
            width:400px;
            padding:25px;
            border-radius:12px;
            transform:translateY(40px);
            transition:0.3s ease;
        }

        .modal.active .modal-content{
            transform:translateY(0);
        }


        .modal-content h3{
            margin-top:0;
            color: #215E61;
        }

        input{
            width:95%;
            padding:10px;
            margin-bottom:15px;
            border-radius:6px;
            border:1px solid #ccc;
        }

        button{
            width:50%;
            padding:10px;
            border:none;
            border-radius:6px;
            background: #215E61;
            color:white;
            cursor:pointer;
        }

        button:hover{
            background: #174143;
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

            <div class="title-row">
                <h2><i class="fa-solid fa-users-gear"></i> User Management</h2>
                <div class="btn-add" onclick="openModal()">
                    <i class="fa-solid fa-user-plus"></i> Add Officer
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($data)) { ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['username']; ?></td>
                        <td><?= $row['email']; ?></td>
                        <td><?= ucfirst($row['role']); ?></td>
                        <td>
                            <a class="btn-delete"
                            href="?delete=<?= $row['id']; ?>"
                            onclick="return confirm('Yakin hapus user ini?')">
                            <i class="fa-solid fa-trash"></i> Remove
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

        </div>

        <!-- MODAL -->
        <div class="modal" id="modal">
            <div class="modal-content">
                <h3><i class="fa-solid fa-user-plus"></i> Add Officer</h3>
                <form method="POST">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button name="add_petugas">Register Officer</button>
                </form>
            </div>
        </div>

        <script>
        function openModal(){
            document.getElementById('modal').classList.add('active');
        }

        window.onclick = function(e){
            const modal = document.getElementById('modal');
            if(e.target == modal){
                modal.classList.remove('active');
            }
        }
        </script>
    </body>
</html>
