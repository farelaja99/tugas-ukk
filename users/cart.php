<?php
    session_start();
    include "../config/database.php";

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
        header("Location: ../auth/login.php");
        exit();
    }

    $username = $_SESSION['username'];


    // =======================
    // UPDATE QTY
    // =======================
    if (isset($_POST['update_qty'])) {

        $transaction_id = intval($_POST['transaction_id']);
        $qty            = intval($_POST['qty']);

        if ($transaction_id > 0 && $qty > 0) {

            mysqli_query($conn, "
                UPDATE transactions
                SET quantity = '$qty',
                    total_price = (
                        SELECT price * $qty
                        FROM products
                        WHERE products.id = transactions.product_id
                    )
                WHERE id = '$transaction_id'
                AND customer_name = '$username'
                AND checkout_status = 'cart'
            ");
        }

        header("Location: cart.php");
        exit();
    }


    // =======================
    // REMOVE ITEM FROM CART
    // =======================
    if (isset($_GET['remove'])) {

        $transaction_id = intval($_GET['remove']);

        mysqli_query($conn, "
            DELETE FROM transactions
            WHERE id = '$transaction_id'
            AND customer_name = '$username'
            AND checkout_status = 'cart'
        ");

        header("Location: cart.php");
        exit();
    }


    // =======================
    // GET CART DATA
    // =======================
    $query = mysqli_query($conn, "
        SELECT 
            transactions.id AS transaction_id,
            products.name,
            products.price,
            products.photo,
            transactions.quantity,
            transactions.total_price
        FROM transactions
        JOIN products ON products.id = transactions.product_id
        WHERE transactions.customer_name = '$username'
        AND transactions.checkout_status = 'cart'
        ORDER BY transactions.id DESC
    ");

    $cart_items  = [];
    $total_price = 0;

    while ($row = mysqli_fetch_assoc($query)) {
        $cart_items[] = $row;
        $total_price += $row['total_price'];
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Cart - Theomart</title>
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <style>

           body {
                margin: 0;
                font-family: 'Segoe UI', sans-serif;
                background: #338C91;
                overflow: hidden;
            }

            /* HEADER */
            .header {
                background: #215E61;
                padding: 18px 40px;
                display: flex;
                align-items: center;
                gap: 30px;
                color: white;
            }

            .logo {
                font-size: 28px;
                font-weight: bold;
            }

            .search-box {
                flex: 1;
                display: flex;
                background: #e5e9d5;
                padding: 8px 15px;
                border-radius: 25px;
            }

            .search-box input {
                border: none;
                outline: none;
                flex: 1;
                background: transparent;
            }

            .history-btn {
                font-size: 22px;
                cursor: pointer;
            }


            /* LAYOUT */
             .container {
                display: flex;
                height: calc(100vh - 70px);
            }



            /* SIDEBAR */
           .sidebar {
                width: 240px;
                background: #2a7579;
                padding: 30px 20px;
                height: 100%;
                position: sticky;
                top: 0;
                overflow: hidden;
            }

            .sidebar a {
                display: block;
                color: white;
                text-decoration: none;
                padding: 10px;
                margin-bottom: 10px;
            }

            .sidebar a:hover {
                background: rgba(255, 255, 255, 0.1);
            }


            /* CONTENT */
             .content {
                flex: 1;
                padding: 40px;
                height: 100%;
                overflow-y: auto;
            }

            .title {
                color: white;
                font-size: 28px;
                margin-bottom: 20px;
            }


            /* TABLE */
            .table-box {
                background: #e5e9d5;
                border-radius: 10px;
                overflow: hidden;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th {
                background: #215E61;
                color: white;
                padding: 12px;
            }

            td {
                padding: 12px;
                text-align: center;
            }

            td img {
                width: 70px;
                border-radius: 8px;
            }


            /* BUTTON */
            .btn {
                border: none;
                padding: 6px 12px;
                border-radius: 6px;
                cursor: pointer;
            }

            .btn-update {
                background: #215E61;
                color: white;
            }

            .btn-remove {
                background: #c0392b;
                color: white;
            }

            .btn-checkout {
                margin-top: 20px;
                padding: 12px 25px;
                background: #27ae60;
                color: white;
                font-size: 16px;
            }


            /* TOTAL */
            .total {
                margin-top: 20px;
                font-size: 20px;
                color: white;
            }

        </style>
    </head>
    <body>
        <!-- HEADER -->
        <div class="header">
            <div class="logo">
                Theomart
            </div>
            <div class="search-box">
                <input type="text" placeholder="Search product...">
            </div>
            <div class="history-btn"
                onclick="location.href='riwayat.php'">
                <i class="fa fa-clock-rotate-left"></i>
            </div>
        </div>

        <div class="container">

            <!-- SIDEBAR -->
            <div class="sidebar">

                <a href="dashboard.php">
                    <i class="fa fa-house"></i>
                    Dashboard
                </a>
                <a href="cart.php">
                    <i class="fa fa-cart-shopping"></i>
                    Cart
                </a>
                <a href="../auth/logout.php">
                    <i class="fa fa-right-from-bracket"></i>
                    Logout
                </a>
            </div>


            <!-- CONTENT -->
            <div class="content">
                <div class="title">
                    Your Cart
                </div>
                <div class="table-box">
                    <table>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>

                        <?php if (!empty($cart_items)) : ?>

                            <?php foreach ($cart_items as $item) : ?>
                                <tr>
                                    <td>
                                        <img src="../uploads/<?= htmlspecialchars($item['photo']) ?>">
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($item['name']) ?>
                                    </td>
                                    <td>
                                        Rp <?= number_format($item['price'], 0, ',', '.') ?>
                                    </td>
                                    <td>
                                        <form method="POST">

                                            <input type="hidden"
                                                name="transaction_id"
                                                value="<?= $item['transaction_id'] ?>">

                                            <input type="number"
                                                name="qty"
                                                value="<?= $item['quantity'] ?>"
                                                min="1"
                                                style="width:60px;">

                                            <button class="btn btn-update"
                                                name="update_qty">
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        Rp <?= number_format($item['total_price'], 0, ',', '.') ?>
                                    </td>
                                    <td>
                                        <a href="cart.php?remove=<?= $item['transaction_id'] ?>"
                                            class="btn btn-remove">
                                            Remove
                                        </a>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php else : ?>
                            <tr>
                                <td colspan="6">
                                    Cart kosong
                                </td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>

                <div class="total">
                    Total: Rp <?= number_format($total_price, 0, ',', '.') ?>
                </div>

                <?php if ($total_price > 0) : ?>

                    <button class="btn btn-checkout"
                        onclick="location.href='checkout.php'">
                        Checkout
                    </button>

                <?php endif; ?>

            </div>
        </div>

    </body>
</html>