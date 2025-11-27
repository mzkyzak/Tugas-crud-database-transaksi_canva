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
$res = $conn->query("SELECT * FROM photo WHERE id='$id'");
if($res->num_rows == 0){
    header("Location: dashboard.php");
    exit;
}
$produk = $res->fetch_assoc();

// Proses update
if(isset($_POST['edit_produk'])){
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];

    if(!empty($_FILES['foto']['name'])){
        $foto = $_FILES['foto']['name'];
        $tmp  = $_FILES['foto']['tmp_name'];
        move_uploaded_file($tmp, "images/".$foto);
        $conn->query("UPDATE photo SET nama='$nama', harga='$harga', foto='$foto' WHERE id='$id'");
    } else {
        $conn->query("UPDATE photo SET nama='$nama', harga='$harga' WHERE id='$id'");
    }

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
body{margin:0;padding:20px;font-family:Poppins,sans-serif;background:linear-gradient(135deg,#0a0f24,#0e162e,#152544);color:#fff;}
h2{text-align:center;color:#00E0FF;text-shadow:0 0 8px #00E0FF;margin-bottom:20px;}
form{max-width:400px;margin:0 auto;background:rgba(14,23,46,0.85);padding:20px;border-radius:12px;}
input[type=text],input[type=number],input[type=file]{width:100%;padding:8px;margin:10px 0;border-radius:6px;border:1px solid #00E0FF;background:#10182C;color:#00E0FF;}
.btn{padding:10px 16px;border-radius:6px;border:none;font-weight:600;cursor:pointer;margin-top:10px;}
.edit-btn{background:#008BFF;color:white;width:48%;} 
.back-btn{background:#FF003C;color:white;width:48%;text-decoration:none;display:inline-block;text-align:center;line-height:32px;}
</style>
</head>
<body>

<h2>Edit Produk</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nama" value="<?= htmlspecialchars($produk['nama']) ?>" required>
    <input type="number" name="harga" value="<?= $produk['harga'] ?>" required>
    <input type="file" name="foto">
    <button type="submit" name="edit_produk" class="btn edit-btn">Update Produk</button>
    <a href="dashboard.php" class="back-btn">Batal</a>
</form>

</body>
</html>
