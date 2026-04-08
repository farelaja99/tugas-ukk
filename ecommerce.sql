CREATE DATABASE IF NOT EXISTS `ecommerce` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ecommerce`;
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Apr 2026 pada 17.58
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce`
--

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
(13, 'Green T Shirt-Size M', 'T-shirt', 90000, 25, '1771685308_green tshirt.png'),
(14, 'Hoodie Zipper-Size XL', 'Hoodie', 100000, 30, '1771808471_hoodie.jpg'),
(15, 'Blue Baggy Pants-Size L', 'Pants', 120000, 43, '1771983412_baggy pants.jpg'),
(16, 'Stone Island Jacket-Size L', 'Hoodie', 350000, 4, '1775520096_jaket stone.jpg'),
(17, 'Dikies Canvas Shirt-Size XL', 'T-shirt', 250000, 9, '1775520155_dikies canvas.jpg'),
(18, 'Cargo Pants-Size XL', 'Pants', 150000, 15, '1775520292_cargo.jpg');

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
  `checkout_status` enum('cart','checkout') DEFAULT 'cart',
  `refund_status` enum('none','requested','approved','rejected') DEFAULT 'none',
  `refund_reason` text DEFAULT NULL,
  `refund_date` datetime DEFAULT NULL,
  `refund_target` varchar(255) DEFAULT NULL,
  `refund_proof` varchar(255) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `shipping_status` varchar(50) DEFAULT 'pending',
  `shipping_courier` varchar(50) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transactions`
--

INSERT INTO `transactions` (`id`, `product_id`, `customer_name`, `quantity`, `total_price`, `proof_photo`, `status`, `created_at`, `phone`, `address`, `payment_method`, `checkout_status`, `refund_status`, `refund_reason`, `refund_date`, `refund_target`, `refund_proof`, `shipping_address`, `shipping_status`, `shipping_courier`, `tracking_number`) VALUES
(32, 16, 'farel', 1, 350000, '1775629555_db.jpg', 'Paid', '2026-04-08', '089988776655', 'jalan jalan aja no.99', 'transfer', 'checkout', 'none', NULL, NULL, NULL, NULL, NULL, 'shipped', 'JNE', '4044004043044'),
(33, 18, 'farel', 2, 300000, NULL, 'Pending', '2026-04-08', '089988776655', 'jalan jalan aja no.99', 'cod', 'checkout', 'none', NULL, NULL, NULL, NULL, NULL, 'processed', 'J&T', NULL),
(34, 17, 'farel', 1, 250000, '1775660590_WhatsApp Image 2026-01-22 at 11.05.08.jpeg', 'Pending', '2026-04-08', '089988776655', 'jalan jalan aja no.99', 'transfer', 'checkout', 'none', NULL, NULL, NULL, NULL, NULL, 'processed', 'SiCepat', NULL),
(35, 18, 'farel', 1, 150000, NULL, 'Pending', '2026-04-08', NULL, NULL, 'cod', 'cart', 'none', NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL);

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
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`, `phone`, `address`) VALUES
(1, 'admin', 'admin@theomart.com', '0192023a7bbd73250516f069df18b500', 'admin', NULL, NULL, NULL, NULL),
(2, 'hanip', 'nip@gmail.com', '202cb962ac59075b964b07152d234b70', 'user', NULL, NULL, NULL, NULL),
(5, 'ojan', 'jan@gmail.com', '202cb962ac59075b964b07152d234b70', 'petugas', NULL, NULL, NULL, NULL),
(9, 'farel', 'farel1@gmmail.com', '202cb962ac59075b964b07152d234b70', 'user', NULL, NULL, '089988776655', 'jalan jalan aja no.99'),
(10, 'danu', 'dan1@gmail.com', '202cb962ac59075b964b07152d234b70', 'petugas', NULL, NULL, NULL, NULL),
(11, 'beni', 'ben@gmail.com', '202cb962ac59075b964b07152d234b70', 'petugas', NULL, NULL, NULL, NULL),
(12, 'bayu', 'bayu@gmail.com', '202cb962ac59075b964b07152d234b70', 'petugas', NULL, NULL, NULL, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
