<?php
    session_start();
    include "../config/database.php";

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
        header("Location: ../auth/login.php");
        exit();
    }

    $username = $_SESSION['username'];

    // ambil data user
    $user_query = mysqli_query($conn, "
        SELECT username, email, phone, address
        FROM users
        WHERE username = '$username'
    ");

    $user = mysqli_fetch_assoc($user_query);


    //ambil data
    $query = mysqli_query($conn, "
        SELECT 
            t.id,
            t.product_id,
            t.quantity,
            t.total_price,
            t.proof_photo,
            p.name,
            p.photo
        FROM transactions t
        JOIN products p ON p.id = t.product_id
        WHERE t.customer_name = '$username'
        AND t.checkout_status = 'Cart'
        ORDER BY t.id DESC
    ");

    $cart_items = [];
    $total      = 0;

    while ($row = mysqli_fetch_assoc($query)) {
        $cart_items[] = $row;
        $total       += $row['total_price'];
    }

    if (empty($cart_items)) {
        header("Location: cart.php");
        exit();
    }


    //proses checkout
    $success = false;

    if (isset($_POST['checkout'])) {

        $phone   = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $method  = mysqli_real_escape_string($conn, $_POST['method']);
        $courier = mysqli_real_escape_string($conn, $_POST['courier']);

        $proof = NULL;

        //upload proof jika transfer
        if ($method == "transfer" && !empty($_FILES['proof']['name'])) {

            $proof = time() . '_' . $_FILES['proof']['name'];

            move_uploaded_file(
                $_FILES['proof']['tmp_name'],
                "../uploads/" . $proof
            );
        }

        if ($method == "transfer") {

            if (!isset($_FILES['proof']) || $_FILES['proof']['error'] == 4) {
                echo "<script>alert('Bukti transfer wajib diupload!'); window.history.back();</script>";
                exit();
            }

            // validasi format file
            $allowed = ['jpg','jpeg','png'];
            $ext = strtolower(pathinfo($_FILES['proof']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                echo "<script>alert('Format file harus JPG / PNG!'); window.history.back();</script>";
                exit();
            }
        }


        // START TRANSACTION (biar aman)
        mysqli_begin_transaction($conn);

        try {

            foreach ($cart_items as $item) {

                $id         = $item['id'];
                $product_id = $item['product_id'];
                $qty        = $item['quantity'];
                $check = mysqli_fetch_assoc(mysqli_query($conn, "
                    SELECT stock FROM products
                    WHERE id='$product_id'
                "));

                if ($check['stock'] < $qty) {
                    throw new Exception("Stock tidak cukup");
                }

                mysqli_query($conn, "
                    UPDATE transactions SET
                        phone = '$phone',
                        address = '$address',
                        shipping_courier = '$courier',
                        shipping_status = 'processed',
                        payment_method = '$method',
                        proof_photo = " . ($proof ? "'$proof'" : "NULL") . ",
                        checkout_status = 'checkout'
                    WHERE id = '$id'
                ");


                // =========================
                // KURANGI STOCK PRODUK
                // =========================
                mysqli_query($conn, "
                    UPDATE products SET
                        stock = stock - $qty
                    WHERE id = '$product_id'
                ");
            }

            mysqli_commit($conn);

            unset($_SESSION['cart']);

            $success = true;

        } catch (Exception $e) {

            mysqli_rollback($conn);

            echo "<script>alert('Stock tidak cukup');</script>";

        }
    }
?>

<!DOCTYPE html>
<html>

    <head>

        <title>Checkout - Theomart</title>

        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <style>

            body {
                margin: 0;
                font-family: 'Segoe UI', sans-serif;
                background: #338C91;
            }


            /* SIDEBAR */
            .sidebar {
                width: 240px;
                background: #215E61;
                height: 100vh;
                position: fixed;
                color: white;
                padding: 25px;
            }

            .logo {
                font-size: 28px;
                font-weight: bold;
                margin-bottom: 30px;
            }

            .back-btn {
                background: #e5e9d5;
                color: #215E61;
                padding: 10px;
                border-radius: 8px;
                text-decoration: none;
                display: block;
                text-align: center;
            }


            /* CONTENT */
            .content {
                margin-left: 260px;
                padding: 40px;
            }

            .title {
                color: white;
                font-size: 32px;
                font-weight: bold;
                margin-bottom: 20px;
            }


            /* BOX */
            .box {
                background: #e5e9d5;
                padding: 20px;
                border-radius: 12px;
                margin-bottom: 20px;
            }


            /* PRODUCT */
            .product {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 15px;
                padding-bottom: 10px;
                border-bottom: 1px solid #ccc;
            }

            .product-left {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .product img {
                width: 70px;
                border-radius: 8px;
            }


            /* FORM */
            label {
                font-weight: bold;
                color: #215E61;
            }

            input,
            textarea {
                width: 100%;
                padding: 10px;
                border: none;
                border-radius: 8px;
                margin-top: 5px;
                margin-bottom: 15px;
                background: #f4f4f4;
            }

            .row {
                display: flex;
                gap: 15px;
            }

            .col {
                flex: 1;
            }


            /* PAYMENT */
            .payment-method {
                margin-bottom: 15px;
            }

            .payment-method label {
                margin-right: 20px;
                font-weight: normal;
            }

            select {
                width: 100%;
                padding: 10px;
                border-radius: 8px;
                border: none;
                background: #f4f4f4;
                margin-bottom: 15px;
            }


            /* BUTTON */
            .btn {
                background: #215E61;
                color: white;
                border: none;
                padding: 12px 25px;
                border-radius: 8px;
                cursor: pointer;
                font-size: 16px;
            }

            .btn:hover {
                background: #17484a;
            }


            /* TOTAL */
            .total {
                font-size: 20px;
                font-weight: bold;
                color: #215E61;
                margin-top: 10px;
            }


            /* MODAL */
            .modal {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.6);
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .modal-box {
                background: white;
                padding: 30px;
                border-radius: 12px;
                text-align: center;
            }

            .modal-box button {
                margin-top: 15px;
            }

        </style>
        <script>
            function showTransfer() {
                document.getElementById("proof-box").style.display = "block";
            }

            function showCOD() {
                document.getElementById("proof-box").style.display = "none";
            }

            document.addEventListener("DOMContentLoaded", function () {
                const radios = document.querySelectorAll("input[name='method']");
                radios.forEach(radio => {
                    radio.addEventListener("change", function () {
                        if (this.value == "transfer") {
                            showTransfer();
                        } else {
                            showCOD();
                        }
                    });
                });
            });
        </script>
    </head>

    <body>

        <div class="sidebar">
            <div class="logo">
                <i class="fa fa-store"></i> Theomart
            </div>
            <a href="cart.php" class="back-btn">
                <i class="fa fa-arrow-left"></i> Back to Cart
            </a>
        </div>


        <div class="content">

            <div class="title">
                Checkout
            </div>
            <form method="POST" enctype="multipart/form-data">

                <!-- PRODUCT LIST -->
                <div class="box">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="product">
                            <div class="product-left">
                                <img src="../uploads/<?= $item['photo'] ?>">
                                <div>
                                    <b><?= $item['name'] ?></b><br>
                                    Qty : <?= $item['quantity'] ?>
                                </div>
                            </div>
                            <div>
                                Rp <?= number_format($item['total_price'], 0, ',', '.') ?>
                            </div>
                        </div>

                    <?php endforeach; ?>

                    <div class="total">
                        Total : Rp <?= number_format($total, 0, ',', '.') ?>
                    </div>
                </div>


                <!-- CUSTOMER FORM -->
                <div class="box">
                   <label>Phone Number</label>
                    <input 
                        type="text" 
                        name="phone" 
                        value="<?= $user['phone'] ?? '' ?>" 
                        required
                    >
                    <label>Address</label>
                    <textarea name="address" required><?= $user['address'] ?? '' ?></textarea>
                    <label>delivery courier</label>
                    <select name="courier" required>
                        <option value="">select courier</option>
                        <option value="JNE">JNE</option>
                        <option value="J&T">J&T</option>
                        <option value="SiCepat">SiCepat</option>
                    </select>
                    <label>payment method</label>

                    <?php if (empty($user['phone']) || empty($user['address'])): ?>
                        <p style="color:red;">
                            Please complete your profile data first to avoid complications when checking out.    
                        </p>
                    <?php endif; ?>
                    
                    <div class="payment-method">
                        <label>
                            <input type="radio" name="method" value="cod" checked>
                            COD
                        </label>
                        <label>
                            <input type="radio" name="method" value="transfer">
                            Transfer
                        </label>
                    </div>
                   <div id="proof-box" style="display:none;">
                        <p id="transfer-text" style="color:#215E61; font-weight:bold; margin-bottom:10px;">
                            Please transfer to the account number: 999999999999
                        </p>

                        <label>Upload Proof of Transfer</label>
                        <input type="file" name="proof">
                         <?php if (empty($user['phone']) || empty($user['address'])): ?>
                            <p style="color:red;">
                                Please complete your profile data first to avoid complications when checking out.
                            </p>
                        <?php endif; ?>

                    </div>
                    <button class="btn" name="checkout">
                        Checkout Now
                    </button>
                </div>
            </form>
        </div>

        <?php if ($success): ?>

            <div class="modal">
                <div class="modal-box">
                    <h2>Thank you for ordering!</h2>
                    <p>Your order is being processed.</p>
                    <a href="dashboard.php">
                        <button class="btn">
                            Back to Dashboard
                        </button>
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </body>
</html>