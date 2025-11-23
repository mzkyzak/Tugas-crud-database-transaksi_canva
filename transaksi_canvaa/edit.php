<?php
session_start();
include 'koneksi.php';

// Cek admin
if(!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$display_name = $_SESSION['username'];

// Ambil data produk
if(!isset($_GET['id'])){
    header("Location: dashboard.php");
    exit;
}

$id = $_GET['id'];
$data = $conn->query("SELECT * FROM photo WHERE id='$id'")->fetch_assoc();

// Proses update
if(isset($_POST['update'])){
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $foto = $data['foto']; // default: foto lama

    if(!empty($_FILES['foto']['name'])){
        $foto = $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], "images/".$foto);
    }

    $conn->query("UPDATE photo SET nama='$nama', harga='$harga', foto='$foto' WHERE id='$id'");
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Produk</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
body {
    margin:0;
    padding:20px;
    font-family:Poppins,sans-serif;
    background:linear-gradient(135deg,#0a0f24,#0e162e,#152544);
    color:#fff;
}
.container {
    max-width:500px;
    margin:50px auto;
    background:rgba(14,23,46,0.85);
    padding:25px;
    border-radius:15px;
    box-shadow:0 12px 30px rgba(0,255,255,0.2);
    transition:0.3s;
}
.container:hover {
    box-shadow:0 15px 35px rgba(0,255,255,0.35);
}
h2 {
    text-align:center;
    color:#00E0FF;
    text-shadow:0 0 6px #00E0FF;
    margin-bottom:20px;
}
label {
    display:block;
    margin-top:12px;
    margin-bottom:6px;
    color:#00E0FF;
}
input[type=text], input[type=number], input[type=file] {
    width:100%;
    padding:10px;
    border-radius:8px;
    border:1px solid #00E0FF;
    background:#10182C;
    color:#00E0FF;
    transition:0.3s;
}
input[type=text]:focus, input[type=number]:focus, input[type=file]:focus {
    border-color:#00FFF5;
    box-shadow:0 0 8px #00FFF5;
    outline:none;
}
img {
    border:2px solid #00E0FF;
    border-radius:10px;
    margin-bottom:10px;
    display:block;
}
.btn-group {
    display:flex;
    justify-content:space-between;
    margin-top:20px;
}
.btn {
    flex:1;
    padding:12px 0;
    border-radius:10px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
    text-align:center;
    text-decoration:none;
    display:inline-block;
    margin-right:10px;
}
.btn:last-child {
    margin-right:0;
}
.btn-update {
    background: linear-gradient(90deg,#00BFFF,#008BFF);
    color:white;
    box-shadow:0 4px 12px rgba(0,191,255,0.4);
}
.btn-update:hover {
    background: linear-gradient(90deg,#008BFF,#00E0FF);
    transform:scale(1.05);
    box-shadow:0 6px 18px rgba(0,191,255,0.6);
}
.btn-back {
    background: linear-gradient(90deg,#FF416C,#FF003C);
    color:white;
    box-shadow:0 4px 12px rgba(255,65,108,0.4);
}
.btn-back:hover {
    background: linear-gradient(90deg,#FF003C,#FF416C);
    transform:scale(1.05);
    box-shadow:0 6px 18px rgba(255,65,108,0.6);
}
</style>
</head>
<body>

<div class="container">
    <h2>Edit Produk</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Nama Produk</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>

        <label>Harga Produk</label>
        <input type="number" name="harga" value="<?= $data['harga'] ?>" required>

        <label>Foto Sekarang</label>
        <img src="images/<?= $data['foto'] ?>" width="150">
        <input type="file" name="foto">

        <div class="btn-group">
            <button type="submit" name="update" class="btn btn-update">Update</button>
            <a href="dashboard.php" class="btn btn-back">Batal</a>
        </div>
    </form>
</div>

</body>
</html>
