<?php
session_start();

// Jika keranjang kosong
if (!isset($_SESSION['keranjang']) || count($_SESSION['keranjang']) == 0) {
    echo "<script>alert('Keranjang masih kosong!'); window.location='index.php';</script>";
    exit;
}

// Hitung total harga
$total_harga = 0;
foreach ($_SESSION['keranjang'] as $item) {
    $total_harga += $item['harga'] * $item['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Checkout</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #0b0e14;
        color: #fff;
        margin: 0;
        padding: 0;
    }
    .container {
        width: 80%;
        margin: 60px auto;
        background: #1a1f28;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 0 10px #00d9ff;
    }
    h2 { text-align: center; margin-bottom: 20px; color: #00d9ff; }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    th, td {
        border-bottom: 1px solid #333;
        padding: 10px;
        text-align: center;
    }
    th { background: #111823; }
    .total {
        text-align: right; 
        font-size: 18px; 
        font-weight: bold; 
        margin-bottom: 20px;
    }
    .btn {
        display: inline-block;
        padding: 10px 18px;
        margin: 5px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        transition: 0.3s;
        border: none;
        cursor: pointer;
    }
    .btn-back { background: #ff3d3d; color: white; }
    .btn-back:hover { background: #b80000; }
    .btn-pay { background: #00d9ff; color: #000; }
    .btn-pay:hover { background: #00a5c4; }
</style>
</head>
<body>

<div class="container">
    <h2>Checkout Barang</h2>

    <table>
        <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Harga Satuan</th>
            <th>Subtotal</th>
        </tr>

        <?php
        $no = 1;
        foreach ($_SESSION['keranjang'] as $item) :
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $item['nama']; ?></td>
            <td><?= $item['jumlah']; ?></td>
            <td>Rp<?= number_format($item['harga'], 0, ',', '.'); ?></td>
            <td>Rp<?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.'); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="total">Total Pembayaran: <span style="color: #00d9ff;">Rp<?= number_format($total_harga, 0, ',', '.'); ?></span></div>

    <a href="index.php" class="btn btn-back">Kembali</a>
    <a href="success.php" class="btn btn-pay">Bayar Sekarang</a>

</div>

</body>
</html>