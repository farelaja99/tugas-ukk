<?php
    session_start();
    include "../config/database.php";
    
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
        header("Location: ../auth/login.php");
        exit();
    }

    $search   = "";
    $category = "";
    $where    = [];

    //search produk
    if (!empty($_GET['search'])) {
        $search = mysqli_real_escape_string($conn, $_GET['search']);
        $where[] = "name LIKE '%$search%'";
    }

    //category produk
    if (!empty($_GET['category'])) {
        $category = mysqli_real_escape_string($conn, $_GET['category']);
        $where[] = "category = '$category'";
    }

    $query = "SELECT * FROM products";

    if (!empty($where)) {
        $query .= " WHERE " . implode(" AND ", $where);
    }

    $query .= " ORDER BY id DESC";

    $products = mysqli_query($conn, $query);

    //tambah kan ke keranjang
    if (isset($_POST['add_to_cart'])) {

        $product_id = intval($_POST['product_id']);
        $username   = $_SESSION['username']; 

        //ambil harga produk
        $product = mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT price FROM products WHERE id='$product_id'")
        );

        if ($product) {

            $price    = $product['price'];
            $quantity = 1;
            $total    = $price * $quantity;

            mysqli_query($conn, "
                INSERT INTO transactions
                (product_id, customer_name, quantity, total_price, status, created_at)
                VALUES
                ('$product_id', '$username', '$quantity', '$total', 'Pending', NOW())
            ");

        }

        header("Location: dashboard.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>User Dashboard</title>

        <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <style>

       body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #338C91;
            overflow: hidden; /* biar body ga ikut scroll */
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
            font-size: 32px;
            font-weight: bold;
            font-style: italic;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-box {
            flex: 1;
            display: flex;
            align-items: center;
            background: #e5e9d5;
            border-radius: 25px;
            padding: 8px 15px;
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
            height: calc(100vh - 70px); /* 70px kira2 tinggi header */
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            background: #2a7579;
            padding: 30px 20px;
            height: 100%;
            overflow: hidden; /* sidebar ga scroll */
            position: sticky;
            top: 0;
        }

        .sidebar .menu-title {
            color: #e5e9d5;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            opacity: 0.8;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            text-decoration: none;
            padding: 8px 10px;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: 0.2s;
        }

        .sidebar a:hover {
            background: rgba(255,255,255,0.15);
        }

        /* PRODUCT AREA */
         .product-area {
            flex: 1;
            padding: 40px;
            overflow-y: auto; /* INI KUNCINYA */
            height: 100%;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
        }

        .card {
            background: #e5e9d5;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
        }

        .card h4 {
            margin: 10px 0 5px;
            color: #215E61;
        }

        .card p {
            color: #215E61;
            font-weight: bold;
        }

        .card button {
            margin-top: 8px;
            background: #215E61;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
        }

        </style>
    </head>

    <body>
        <!-- HEADER -->
        <div class="header">
            <div class="logo">
                Theomart
            </div>
            <form method="GET" class="search-box">
                <i class="fa fa-search"></i>
                <input
                    type="text"
                    name="search"
                    placeholder="Search product..."
                    value="<?= $search ?>"
                >
            </form>
            <div class="history-btn"
                onclick="location.href='riwayat.php'">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>
        <div class="container">

            <!-- SIDEBAR -->
            <div class="sidebar">
                <a href="profile.php">
                    <i class="fa-solid fa-user"></i>
                    Profile
                </a>
                <a href="dashboard.php">
                    <i class="fa-solid fa-house"></i>
                    Dashboard
                </a>
                <a href="cart.php">
                    <i class="fa-solid fa-cart-shopping"></i>
                    Cart
                </a>
                <div class="menu-title">Categories</div>
                <a href="?">
                    <i class="fa-solid fa-layer-group"></i>
                    All
                </a>
                <a href="?category=T-shirt&search=<?= $search ?>">
                    T-Shirt
                </a>
                <a href="?category=Pants&search=<?= $search ?>">
                    Pants
                </a>
                <a href="?category=Hoodie&search=<?= $search ?>">
                    Hoodie
                </a>
                <div class="menu-title">Account</div>
                <a href="../auth/logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>
            </div>

            <!-- PRODUCT AREA -->
            <div class="product-area">
                <div class="product-grid">
                    <?php if (mysqli_num_rows($products) > 0): ?>

                        <?php while ($row = mysqli_fetch_assoc($products)): ?>
                            <div class="card">
                                <img src="../uploads/<?= $row['photo']; ?>">
                                <h4><?= $row['name']; ?></h4>
                                <p>
                                    Rp <?= number_format($row['price'], 0, ',', '.'); ?>
                                </p>
                                <form method="POST">
                                    <input
                                        type="hidden"
                                        name="product_id"
                                        value="<?= $row['id']; ?>"
                                    >
                                    <button type="submit" name="add_to_cart">
                                        <i class="fa-solid fa-cart-plus"></i>
                                        Add to cart
                                    </button>
                                </form>
                            </div>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <p style="color:white;">No product found</p>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </body>
</html>