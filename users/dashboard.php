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
            overflow: aoto; 
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
            min-height: calc(100vh - 70px); 
        }

        /* SIDEBAR */
        .sidebar {
            width: 200px;
            background: #2a7579;
            padding: 30px 20px;
            height: 100%;
            overflow: hidden; 
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

        .user-box{
            text-align:center;
            margin-bottom:25px;
        }

        .user-avatar{
            width:60px;
            height:60px;
            border-radius:50%;
            background:#e5e9d5;
            color:#215E61;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:25px;
            margin:0 auto 10px;
        }

        .user-name{
            color:#e5e9d5;
            font-weight:600;
        }

        .sidebar a{
            display:flex;
            align-items:center;
            gap:12px;
            color:white;
            text-decoration:none;
            padding:10px 12px;
            border-radius:10px;
            margin-bottom:8px;
            transition:0.25s;
            font-size:17px;
        }

        .sidebar a:hover{
            background:rgba(255,255,255,0.2);
            transform:translateX(5px);
        }

        .sidebar a.active{
            background:#e5e9d5;
            color:#215E61;
            font-weight:bold;
        }

        .menu-title{
            font-size:12px;
            letter-spacing:1px;
        }

        /* PRODUCT AREA */
         .product-area {
            flex: 1;
            padding: 40px;
            overflow-y: auto; 
            height: calc(100vh-70px);
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }

        .card {
            background: #e5e9d5;
            border-radius: 16px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* ini kuncinya */
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 12px;
            transition: 0.3s;
        }

        .card:hover img {
            transform: scale(1.05);
        }

        /* CATEGORY BADGE */
        .category-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: #215E61;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        /* TEXT */
        .card h4 {
            margin: 12px 0 5px;
            color: #215E61;
            font-size: 16px;
        }

        .price {
            color: #215E61;
            font-weight: bold;
            font-size: 15px;
        }

        /* BUTTON */
        .card button {
            margin-top: 10px;
            width: 100%;
            background: #215E61;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.2s;
        }

        .card button:hover {
            background: #184548;
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
                <div class="user-box">
                    <div class="user-avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="user-name">
                        <?= $_SESSION['username']; ?>
                    </div>
                </div>
                <a href="profile.php" class="<?= basename($_SERVER['PHP_SELF'])=='profile.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-user"></i>
                    Profile
                </a>
                <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php' ? 'active' : '' ?>">
                    <i class="fa-solid fa-house"></i>
                    Dashboard
                </a>
                <a href="cart.php" class="<?= basename($_SERVER['PHP_SELF'])=='cart.php' ? 'active' : '' ?>">
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
                                <div>
                                    <img src="../uploads/<?= $row['photo']; ?>">

                                    <h4><?= $row['name']; ?></h4>

                                    <div class="price">
                                        Rp <?= number_format($row['price'], 0, ',', '.'); ?>
                                    </div>
                                </div>

                                <form method="POST">
                                    <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                                    <button type="submit" name="add_to_cart">
                                        <i class="fa-solid fa-cart-plus"></i> Add to Cart
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