<?php
session_start();
include 'koneksi.php';

// ============================
// CEK ADMIN
// ============================
if(!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$display_name = $_SESSION['username'];

// ============================
// TAMBAH PRODUK
// ============================
if(isset($_POST['tambah_produk'])){
    $nama  = $_POST['nama'];
    $harga = $_POST['harga'];

    // Upload Foto
    $foto = $_FILES['foto']['name'];
    $tmp  = $_FILES['foto']['tmp_name'];

    // Pastikan folder images ada
    if(!is_dir("images")){
        mkdir("images");
    }

    move_uploaded_file($tmp, "images/".$foto);

    $conn->query("INSERT INTO photo(nama, harga, foto) VALUES('$nama', '$harga', '$foto')");
    header("Location: dashboard.php");
    exit;
}

// ============================
// HAPUS PRODUK
// ============================
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];

    $res = $conn->query("SELECT foto FROM photo WHERE id='$id'");
    $row = $res->fetch_assoc();

    if(!empty($row['foto']) && file_exists("images/".$row['foto'])){
        unlink("images/".$row['foto']);
    }

    $conn->query("DELETE FROM photo WHERE id='$id'");
    header("Location: dashboard.php");
    exit;
}

// ============================
// HAPUS PESANAN
// ============================
if(isset($_GET['hapus_pesanan'])){
    $id = $_GET['hapus_pesanan'];
    $conn->query("DELETE FROM keranjang WHERE id='$id'");
    header("Location: dashboard.php");
    exit;
}

// ============================
// EXPORT CSV
// ============================

if(isset($_GET['export'])){

    header("Content-Type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=laporan_pesanan_".date('Ymd_His').".xls");

    echo "<table border='1'>";
    echo "<tr style='background:#4CAF50;color:white;font-weight:bold;'>
            <th>No</th>
            <th>Username</th>
            <th>Produk</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>Total Harga</th>
            <th>Tanggal</th>
          </tr>";

    $q = $conn->query("SELECT k.id, u.username, p.nama AS produk, 
                              k.jumlah, p.harga, k.total_harga, k.tanggal
                       FROM keranjang k
                       JOIN users u ON k.id_user=u.id
                       JOIN photo p ON k.id_barang=p.id
                       ORDER BY k.tanggal DESC");

    $no = 1;
    while($row = $q->fetch_assoc()){
        echo "<tr>
                <td>".$no++."</td>
                <td>".$row['username']."</td>
                <td>".$row['produk']."</td>
                <td>".$row['jumlah']."</td>
                <td>".$row['harga']."</td>
                <td>".$row['total_harga']."</td>
                <td>".$row['tanggal']."</td>
              </tr>";
    }

    echo "</table>";
    exit();
}

// ============================
// AMBIL DATA
// ============================
$produk  = $conn->query("SELECT * FROM photo");
$pesanan = $conn->query("
    SELECT k.id, u.username, p.nama AS produk, k.jumlah, 
           k.total_harga, k.tanggal
    FROM keranjang k
    JOIN users u ON k.id_user=u.id
    JOIN photo p ON k.id_barang=p.id
    ORDER BY k.tanggal DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
/* ====== DARK NEON THEME ====== */
body{
    margin:0; padding:20px;
    font-family:Poppins,sans-serif;
    background:linear-gradient(135deg,#0a0f24,#0e162e,#152544);
    color:#fff;
}
h2{
    text-align:center;
    color:#00E0FF;
    text-shadow:0 0 8px #00E0FF;
    margin-bottom:20px;
}

/* HEADER */
header{
    display:table; width:100%;
    border-radius:12px; overflow:hidden;
    margin-bottom:18px;
    box-shadow:0 6px 18px rgba(0,0,0,0.35);
}
header .cell{
    display:table-cell; vertical-align:middle;
    padding:16px 20px;
    border-right:1px solid rgba(255,255,255,0.04);
}
header .title-cell{
    background:linear-gradient(90deg,#071733,#0b274a); 
    color:#00E0FF;
}
header .stats-cell{
    background:linear-gradient(90deg,#082029,#11343f);
}
header .info-cell{
    background:linear-gradient(90deg,#1a1430,#2a1f45);
}
header .action-cell{
    background:linear-gradient(90deg,#2a1f2b,#3b2636);
    text-align:right;
}

.logout-btn{
    background:#ff003c;
    color:white;
    padding:8px 12px;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

/* CONTENT BOX */
.card-box, .pesanan-box{
    background:rgba(14,23,46,0.75);
    border:1px solid rgba(0,255,255,0.2);
    padding:20px;
    border-radius:18px;
    max-width:1100px;
    margin:20px auto;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
}
th,td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid rgba(255,255,255,0.06);
}
th{
    background:rgba(0,224,255,0.08);
    color:#00E0FF;
}
td img{
    width:80px; height:80px;
    border-radius:12px;
    border:2px solid #00E0FF;
    object-fit:cover;
}

/* INPUT FORM */
input[type=text], input[type=number], input[type=file]{
    padding:6px;
    border-radius:6px;
    border:1px solid #00E0FF;
    background:#10182C;
    color:#00E0FF;
}

/* BUTTON */
.btn{
    padding:7px 13px;
    border-radius:6px;
    font-weight:500;
    cursor:pointer;
    border:none;
    margin:2px;
}
.add-btn{ background:#06c258; color:white; }
.edit-btn{ background:#008BFF; color:white; }
.delete-btn{ background:#FF003C; color:white; }

.export-btn{
    background:#00C853; color:white;
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
}
</style>
</head>

<body>

<header>
    <div class="cell title-cell">
        <h2>Dashboard Admin</h2>
    </div>

    <div class="cell stats-cell">
        <div class="header-stats">
            <div class="stat">
                <div class="num"><?= $produk->num_rows ?></div>
                <div class="label">Total Produk</div>
            </div>
            <div class="stat">
                <div class="num"><?= $pesanan->num_rows ?></div>
                <div class="label">Total Pesanan</div>
            </div>
        </div>
    </div>

    <div class="cell info-cell">
        <div class="user-name">Hai, <?= htmlspecialchars($display_name) ?>!</div>
    </div>

    <div class="cell action-cell">
        <form method="post" action="logout.php">
            <button class="logout-btn">Logout</button>
        </form>
    </div>
</header>

<!-- ====================== PRODUK ====================== -->
<h2>📦 Produk</h2>
<div class="card-box">

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nama" placeholder="Nama Produk" required>
    <input type="number" name="harga" placeholder="Harga" required>
    <input type="file" name="foto" required>
    <button type="submit" name="tambah_produk" class="btn add-btn">Tambah Produk</button>
</form>

<table>
    <tr>
        <th>No</th><th>Foto</th><th>Nama</th><th>Harga</th><th>Aksi</th>
    </tr>

    <?php $no=1; while($row=$produk->fetch_assoc()): ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><img src="images/<?= $row['foto'] ?>"></td>
        <td><?= htmlspecialchars($row['nama']) ?></td>
        <td>Rp <?= number_format($row['harga'],0,',','.') ?></td>
        <td>
            <a href="edit.php?id=<?= $row['id'] ?>" class="btn edit-btn">Edit</a>

            <form method="GET" style="display:inline-block;" 
                 onsubmit="return confirm('Hapus produk ini?')">
                <input type="hidden" name="hapus" value="<?= $row['id'] ?>">
                <button type="submit" class="btn delete-btn">Hapus</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</div>

<!-- ====================== PESANAN ====================== -->
<h2>📋 Pesanan User</h2>
<a href="?export=1" class="export-btn">Export Laporan</a>

<div class="pesanan-box">
<table>
    <tr>
        <th>No</th><th>Username</th><th>Produk</th><th>Jumlah</th>
        <th>Total Harga</th><th>Tanggal</th><th>Aksi</th>
    </tr>

    <?php $no=1; while($row=$pesanan->fetch_assoc()): ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td><?= htmlspecialchars($row['produk']) ?></td>
        <td><?= $row['jumlah'] ?></td>
        <td>Rp <?= number_format($row['total_harga'],0,',','.') ?></td>
        <td><?= $row['tanggal'] ?></td>
        <td>
            <form method="GET" style="display:inline-block;"
                onsubmit="return confirm('Hapus pesanan ini?')">
                <input type="hidden" name="hapus_pesanan" value="<?= $row['id'] ?>">
                <button type="submit" class="btn delete-btn">Hapus</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>

</table>
</div>

</body>
</html>  