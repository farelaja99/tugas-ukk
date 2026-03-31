CREATE DATABASE IF NOT EXISTS `ecommerce` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ecommerce`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `stock`, `photo`) VALUES
(13, 'Green T Shirt', 'T-shirt', 90000, 96, '1771685308_green tshirt.png'),
(14, 'hoodie zipper', 'Hoodie', 100000, 90, '1771808471_hoodie.jpg'),
(15, 'Blue Baggy Pants', 'Pants', 120000, 88, '1771983412_baggy pants.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_price` int(11) NOT NULL,
  `proof_photo` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Paid','Cancelled') DEFAULT 'Pending',
  `created_at` date DEFAULT curdate(),
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `payment_method` enum('cod','transfer') NOT NULL DEFAULT 'cod',
  `checkout_status` enum('cart','checkout') DEFAULT 'cart'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transactions`
--

INSERT INTO `transactions` (`id`, `product_id`, `customer_name`, `quantity`, `total_price`, `proof_photo`, `status`, `created_at`, `phone`, `address`, `payment_method`, `checkout_status`) VALUES
(18, 15, 'farel', 7, 840000, '1771986112_dana.jpg', 'Cancelled', '2026-02-25', '087766991122', 'jalan kasih no.1', 'transfer', 'checkout'),
(19, 14, 'farel', 4, 400000, NULL, 'Paid', '2026-02-25', '087766991122', 'jalan kasih no.1', 'cod', 'checkout'),
(20, 13, 'farel', 2, 180000, NULL, 'Paid', '2026-02-25', '087766991122', 'jalan kasih no.1', 'cod', 'checkout'),
(21, 15, 'hanip', 1, 120000, NULL, 'Pending', '2026-02-25', '087766991122', 'jalan mawar', 'cod', 'checkout');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','petugas','user') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@theomart.com', '0192023a7bbd73250516f069df18b500', 'admin', NULL, NULL),
(2, 'hanip', 'nip@gmail.com', '202cb962ac59075b964b07152d234b70', 'user', NULL, NULL),
(5, 'ojan', 'jan@gmail.com', '202cb962ac59075b964b07152d234b70', 'petugas', NULL, NULL),
(9, 'farel', 'farel1@gmmail.com', '202cb962ac59075b964b07152d234b70', 'user', NULL, NULL),
(10, 'danu', 'dan1@gmail.com', '202cb962ac59075b964b07152d234b70', 'petugas', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
--
-- Database: `example`
--
