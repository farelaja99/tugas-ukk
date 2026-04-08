<?php
session_start();
include "../config/database.php";

$id = intval($_GET['id']);

if (isset($_POST['refund'])) {

    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $target = mysqli_real_escape_string($conn, $_POST['refund_target']);
    $id     = intval($_GET['id']);

    mysqli_query($conn, "
        UPDATE transactions SET
            refund_status = 'requested',
            refund_reason = '$reason',
            refund_target = '$target',
            refund_date = NOW()
        WHERE id = '$id'
    ");

    echo "<script>alert('Refund diajukan'); location='riwayat.php';</script>";
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Refund - Theomart</title>

        <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <style>

            body {
                margin: 0;
                font-family: 'Segoe UI', sans-serif;
                background: #338C91;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }

            .refund-box {
                background: #e5e9d5;
                padding: 30px;
                border-radius: 16px;
                width: 400px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                animation: fadeIn 0.4s ease;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(15px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .title {
                font-size: 22px;
                font-weight: bold;
                color: #215E61;
                margin-bottom: 10px;
            }

            .subtitle {
                font-size: 14px;
                color: #555;
                margin-bottom: 20px;
            }

            textarea {
                width: 100%;
                height: 100px;
                padding: 10px;
                border-radius: 10px;
                border: none;
                background: #f4f4f4;
                resize: none;
                outline: none;
                margin-bottom: 20px;
            }

            textarea:focus {
                background: #fff;
                box-shadow: 0 0 0 2px #215E61;
            }

            input {
                width: 100%;
                padding: 10px;
                border-radius: 10px;
                border: none;
                background: #f4f4f4;
                margin-bottom: 20px;
                outline: none;
            }

            input:focus {
                background: #fff;
                box-shadow: 0 0 0 2px #215E61;
            }

            .btn {
                width: 100%;
                background: #c0392b;
                color: white;
                border: none;
                padding: 12px;
                border-radius: 10px;
                cursor: pointer;
                font-size: 15px;
                transition: 0.2s;
            }

            .btn:hover {
                background: #922b21;
            }

            .back {
                display: block;
                text-align: center;
                margin-top: 15px;
                text-decoration: none;
                color: #215E61;
                font-size: 14px;
            }

        </style>
    </head>
    <body>
        <div class="refund-box">
            <div class="title">
                <i class="fa-solid fa-rotate-left"></i> Ajukan Refund
            </div>
            <div class="subtitle">
                Jelaskan alasan kamu biar admin ga nebak-nebak sendiri
            </div>

            <form method="POST">
                <label style="font-weight:bold; color:#215E61;">
                    Alasan Refund
                </label>
                <textarea name="reason" placeholder="Contoh: Barang rusak, salah ukuran, dll..." required></textarea>

                <label style="font-weight:bold; color:#215E61;">
                    Refund Dikirim Ke
                </label>
                <input 
                    type="text" 
                    name="refund_target" 
                    placeholder="Contoh: BCA - 123456789 a/n Nama Kamu / Dana / OVO"
                    required
                >
                <button class="btn" name="refund">
                    Kirim Permintaan Refund
                </button>

            </form>
            <a href="riwayat.php" class="back">
                ← Kembali ke Riwayat
            </a>
        </div>
    </body>
</html>