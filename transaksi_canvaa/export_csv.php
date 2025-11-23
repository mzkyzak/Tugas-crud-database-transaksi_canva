<?php
session_start();
include 'koneksi.php';

// Cek admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    die("Akses ditolak");
}

// Header Excel
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=laporan_pesanan_".date('Ymd_His').".xls");

echo '
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 13px;
        }
        th {
            background: #2196F3;
            color: white;
            padding: 8px;
            text-align: center;
            border: 1px solid #444;
        }
        td {
            padding: 6px;
            border: 1px solid #888;
        }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .right { text-align: right; }
        .center { text-align: center; }
        .title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="title">LAPORAN DATA PESANAN</div>

<table>
    <tr>
        <th width="5%">No</th>
        <th width="15%">Username</th>
        <th width="20%">Produk</th>
        <th width="10%">Jumlah</th>
        <th width="15%">Harga</th>
        <th width="15%">Total Harga</th>
        <th width="20%">Tanggal</th>
    </tr>';

$query = $conn->query("SELECT k.id, u.username, p.nama AS produk, 
                              k.jumlah, p.harga, k.total_harga, k.tanggal
                       FROM keranjang k
                       JOIN users u ON k.id_user = u.id
                       JOIN photo p ON k.id_barang = p.id
                       ORDER BY k.tanggal DESC");

$no = 1;
while($row = $query->fetch_assoc()){

    $harga = number_format($row['harga'],0,',','.');
    $total = number_format($row['total_harga'],0,',','.');

    echo '
    <tr>
        <td class="center">'.$no++.'</td>
        <td>'.$row['username'].'</td>
        <td>'.$row['produk'].'</td>
        <td class="center">'.$row['jumlah'].'</td>
        <td class="right">Rp '.$harga.'</td>
        <td class="right">Rp '.$total.'</td>
        <td>'.$row['tanggal'].'</td>
    </tr>';
}

echo '
</table>
</body>
</html>';

exit();
?>
