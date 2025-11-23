<?php
session_start();
include 'koneksi.php';

// Anti-cache (biar tidak balik ke dashboard setelah logout)
header("Cache-Control: no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Cek login
if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

$display_name = $_SESSION['username'];
$role = $_SESSION['role'];

// Ambil semua produk
$result = $conn->query("SELECT * FROM photo");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Template Canva</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
/* ===== Background lebih jernih dan elegan ===== */
body{
    margin:0;
    padding:0;
    font-family:Poppins,sans-serif;
    background:linear-gradient(135deg,#141a33,#1b2647,#101b2c);
    background-size:200% 200%;
    animation:bgMove 8s infinite alternate;
    color:#eefaff;
}
@keyframes bgMove{
    from{background-position:20% 0%;}
    to{background-position:80% 100%;}
}

/* ===== Header modern glassmorphism ===== */
header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:18px 32px;
    background:rgba(255,255,255,0.12);
    border-bottom:1px solid rgba(0,255,255,0.25);
    backdrop-filter:blur(12px);
    box-shadow:0 4px 15px rgba(0,0,0,0.25);
}
header h2{
    margin:0;
    font-weight:600;
    color:#00eaff;
    text-shadow:0 0 8px #00eaff;
    font-size:22px;
}
.user-name{
    font-weight:500;
    color:#bffcff;
    margin-right:20px;
    font-size:15px;
}

/* ===== Logout button ===== */
.logout-btn{
    background:#ff003c;
    color:white;
    padding:9px 15px;
    border:none;
    border-radius:9px;
    font-weight:600;
    cursor:pointer;
    transition:0.25s;
}
.logout-btn:hover{
    transform:scale(1.08);
    box-shadow:0 0 12px #ff003c,0 0 22px #ff003c70;
}

/* ===== Grid produk ===== */
.gallery{
    padding:40px;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:28px;
}

/* ===== Card lebih soft, terang, premium ===== */
.card{
    background:rgba(255,255,255,0.17);
    border-radius:18px;
    text-align:center;
    overflow:hidden;
    border:1px solid rgba(0,255,255,0.25);
    backdrop-filter:blur(14px);
    box-shadow:0 4px 20px rgba(0,0,0,0.25);
    transition:0.3s;
}
.card:hover{
    transform:translateY(-7px);
    box-shadow:0 8px 25px rgba(0,255,255,0.3);
}

/* gambar produk */
.product-img{
    width:100%;
    height:240px;
    object-fit:contain;
    background:#0d162b;
    border-bottom:1px solid rgba(0,255,255,0.15);
    transition:0.3s;
}
.card:hover .product-img{
    transform:scale(1.07);
}

/* ===== Nama Produk ===== */
.title{
    font-size:17px;
    font-weight:600;
    margin:10px 0;
    color:#e6fbff;
    text-shadow:0 0 6px #00eaff50;
}

/* ===== Harga ===== */
.price{
    font-size:16px;
    font-weight:700;
    color:#00eaff;
    margin-bottom:10px;
    text-shadow:0 0 6px #00eaff80;
}

/* ===== Tombol tambah ===== */
.btn-add{
    margin:12px 0 20px 0;
    padding:10px 15px;
    border:none;
    border-radius:9px;
    background:#06c258;
    color:white;
    cursor:pointer;
    font-weight:600;
    font-size:14px;
    transition:0.25s;
}
.btn-add:hover{
    transform:scale(1.1);
    box-shadow:0 0 14px #06c258;
}

/* ===== tombol keranjang ===== */
.cart-btn{
    display:inline-block;
    margin:25px;
    padding:10px 15px;
    background:#00C853;
    color:white;
    border-radius:8px;
    text-decoration:none;
    font-weight:600;
    transition:.25s;
}
.cart-btn:hover{
    transform:scale(1.08);
    box-shadow:0 0 14px #00FF80;
}

/* Footer */
footer{
    text-align:center;
    padding:18px;
    font-size:14px;
    font-weight:500;
    color:#dffbfd;
    opacity:.85;
}
</style>
</head>
<body>

<header>
    <h2>Template Canva</h2>

    <div style="display:flex;align-items:center;">
        <div class="user-name">Hai, <?= htmlspecialchars($display_name) ?>!</div>
        <form method="post" action="logout.php">
            <button class="logout-btn">Logout</button>
        </form>
    </div>
</header>

<a href="keranjang.php" class="cart-btn">🛒 Lihat Keranjang</a>

<h2 style="text-align:center;text-shadow:0 0 8px #00eaff;color:#bffcff;">🛍️ Produk</h2>

<div class="gallery">
<?php while($row = $result->fetch_assoc()): ?>
<div class="card">
    <img src="images/<?= htmlspecialchars($row['foto']) ?>" class="product-img">

    <h3 class="title"><?= htmlspecialchars($row['nama']) ?></h3>
    <p class="price">Rp <?= number_format($row['harga'],0,',','.') ?></p>

    <form method="POST" action="keranjang.php">
        <input type="hidden" name="add_id" value="<?= $row['id'] ?>">
        <button class="btn-add">+ Tambah ke Keranjang</button>
    </form>
</div>
<?php endwhile; ?>
</div>

<footer>&copy; <?= date('Y') ?> Template Canva — <b>Taufiq Ikhsan Muzaky</b></footer>

</body>
</html>
