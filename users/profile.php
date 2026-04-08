<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit();
}

$username = $_SESSION['username'];

// ambil data user
$users = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT username, email, phone, address 
    FROM users
    WHERE username = '$username'
"));

// update profile
if (isset($_POST['update'])) {

    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $phone   = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    mysqli_query($conn, "
        UPDATE users SET
            email = '$email',
            phone = '$phone',
            address = '$address'
        WHERE username = '$username'
    ");

    header("Location: profile.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #338C91, #215E61);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        * {
            box-sizing: border-box;
        }

        /* CONTAINER */
        .container {
            width: 100%;
            max-width: 420px;
        }

        /* BOX */
        .box {
            background: #e5e9d5;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        /* TITLE */
        h2 {
            text-align: center;
            color: #215E61;
            margin-bottom: 20px;
        }

        form {
            width: 100%;
        }

        /* LABEL */
        label {
            font-size: 14px;
            font-weight: 600;
            color: #215E61;
        }

        /* INPUT */
        input, textarea {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            margin-bottom: 18px;
            border-radius: 10px;
            border: 1px solid #ddd;
            background: #f9f9f9;
            transition: 0.2s;
            font-size: 14px;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #338C91;
            background: #fff;
        }

        /* READONLY */
        input[readonly] {
            background: #eee;
            cursor: not-allowed;
        }

        /* BUTTON */
        button {
            width: 100%;
            background: #215E61;
            color: white;
            border: none;
            padding: 13px;
            border-radius: 10px;
            font-size: 15px;
            cursor: pointer;
            transition: 0.2s;
        }

        button:hover {
            background: #17484a;
        }

        /* BACK LINK */
        .back {
            display: block;
            margin-top: 18px;
            text-align: center;
            text-decoration: none;
            color: #215E61;
            font-weight: 600;
            transition: 0.2s;
        }

        .back:hover {
            text-decoration: underline;
        }

        /* SUCCESS */
        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 18px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="container">
    <div class="box">

        <h2><i class="fa fa-user"></i> Profile</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="success">Profile updated successfully!</div>
        <?php endif; ?>

        <form method="POST">

            <label>Username</label>
            <input type="text" value="<?= $users['username'] ?>" readonly>

            <label>Email</label>
            <input type="email" name="email" 
                value="<?= $users['email'] ?? '' ?>" required>

            <label>No HP</label>
            <input type="text" name="phone" 
                value="<?= $users['phone'] ?? '' ?>">

            <label>Alamat</label>
            <textarea name="address"><?= $users['address'] ?? '' ?></textarea>

            <button name="update">
                <i class="fa fa-save"></i> Update Profile
            </button>

        </form>

        <a href="dashboard.php" class="back">
            <i class="fa fa-arrow-left"></i> Back to Dashboard
        </a>

    </div>
</div>

</body>
</html>