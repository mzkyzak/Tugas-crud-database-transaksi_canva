<?php
session_start();
include 'koneksi.php';

// Pastikan user login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$display_name = $_SESSION['username'];

// Inisialisasi keranjang
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Tambah barang ke keranjang
if (isset($_POST['add_id'])) {
    $id = $_POST['add_id'];
    $q = $conn->query("SELECT * FROM photo WHERE id='$id'");
    $data = $q->fetch_assoc();
    if(!isset($_SESSION['cart'][$id])){
        $_SESSION['cart'][$id] = [
            'nama'=>$data['nama'],
            'harga'=>(int)$data['harga'],
            'foto'=>$data['foto'],
            'qty'=>1
        ];
    } else {
        $_SESSION['cart'][$id]['qty']++;
    }
    header("Location:keranjang.php"); 
    exit();
}

// Update qty
if (isset($_POST['update_cart'])) {
    foreach($_POST['qty'] as $id=>$jumlah){
        $_SESSION['cart'][$id]['qty'] = max(1,(int)$jumlah);
    }
    $updated = true;
}

// Hapus item
if(isset($_GET['hapus'])){
    unset($_SESSION['cart'][$_GET['hapus']]);
    header("Location:keranjang.php"); 
    exit();
}

// Checkout
if (isset($_POST['checkout']) && !empty($_SESSION['cart'])) {
    $id_user = $_SESSION['id_user'];
    foreach($_SESSION['cart'] as $id_barang=>$item){
        $jumlah = $item['qty'];
        $total = $item['harga']*$jumlah;
        $conn->query("INSERT INTO keranjang(id_user,id_barang,jumlah,total_harga) VALUES('$id_user','$id_barang','$jumlah','$total')");
    }
    $_SESSION['cart'] = [];
    header("Location:keranjang.php?checkout_success=1"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Keranjang Belanja</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
body{margin:0;padding:20px;font-family:Poppins,sans-serif;background:linear-gradient(135deg,#0a0f24,#0e162e,#152544);color:#fff;}
h2{text-align:center;font-weight:600;color:#00E0FF;font-size:28px;letter-spacing:1px;text-shadow:0 0 8px #00E0FF;margin-bottom:20px;}
.cart-box{background:rgba(14,23,46,0.75);border:1px solid rgba(0,255,255,0.2);backdrop-filter:blur(12px);padding:20px;border-radius:18px;box-shadow:0 0 25px rgba(0,255,255,0.12);max-width:900px;margin:0 auto;}
table{width:100%;border-collapse:collapse;}
th,td{padding:12px;color:#E6F4F1;border-bottom:1px solid rgba(255,255,255,0.06);text-align:center;}
th{color:#00E0FF;background:rgba(0,224,255,0.08);}
td img{width:80px;height:80px;border-radius:12px;border:2px solid #00E0FF;object-fit:cover;transition:.25s;}
td img:hover{transform:scale(1.07);box-shadow:0 0 10px #00E0FF;}
input[type=number]{width:70px;padding:6px;border-radius:6px;text-align:center;background:#10182C;border:1px solid #00E0FF;color:#00E0FF;}
.btn{padding:7px 13px;border-radius:6px;font-weight:500;cursor:pointer;transition:.25s;border:none;}
.update-btn{background:#008BFF;color:white;} .update-btn:hover{transform:scale(1.07);}
.delete-btn{background:#FF003C;color:white;} .delete-btn:hover{transform:scale(1.07);}
.checkout-btn{background:#00C853;color:white;padding:10px 18px;border-radius:8px;font-weight:600;text-decoration:none;transition:.25s;} .checkout-btn:hover{transform:scale(1.07);box-shadow:0 0 12px #00FF80;}
.back-btn{display:inline-block;margin-top:18px;padding:10px 15px;border-radius:10px;text-decoration:none;background:#162544;color:#00E0FF;border:1px solid #00E0FF;transition:.25s;} .back-btn:hover{transform:translateX(-6px);box-shadow:0 0 12px #00E0FF;}
.total-box{text-align:right;margin-top:15px;font-size:18px;font-weight:bold;color:#00E0FF;text-shadow:0 0 6px #00E0FF;}
.success-msg{background:#00FF80;color:#001f0a;padding:10px 15px;border-radius:10px;text-align:center;margin-bottom:15px;font-weight:600;}
</style>
</head>
<body>

<h2>🛒 Keranjang Belanja</h2>

<div class="cart-box">
<?php if(isset($updated) && $updated): ?>
<div class="success-msg">✅ Keranjang berhasil diperbarui!</div>
<?php endif; ?>

<?php if(isset($_GET['checkout_success'])): ?>
<div class="success-msg">🎉 Checkout berhasil!</div>
<?php endif; ?>

<form method="POST">
<table>
<tr>
<th>No</th>
<th>Foto</th>
<th>Nama</th>
<th>Harga</th>
<th>Jumlah</th>
<th>Subtotal</th>
<th>Aksi</th>
</tr>

<?php
$total=0; $no=1;
if(!empty($_SESSION['cart'])):
foreach($_SESSION['cart'] as $id=>$item):
    $subtotal = $item['harga']*$item['qty']; 
    $total += $subtotal;
?>
<tr>
<td><?= $no++ ?></td>
<td><img src="images/<?= $item['foto'] ?>"></td>
<td><?= htmlspecialchars($item['nama']) ?></td>
<td>Rp <?= number_format($item['harga'],0,',','.') ?></td>
<td><input type="number" name="qty[<?= $id ?>]" min="1" value="<?= $item['qty'] ?>"></td>
<td>Rp <?= number_format($subtotal,0,',','.') ?></td>
<td>
<form method="GET" style="display:inline-block;" onsubmit="return confirm('Hapus item?')">
<input type="hidden" name="hapus" value="<?= $id ?>">
<button type="submit" class="btn delete-btn">Hapus</button>
</form>
</td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="7" style="text-align:center;color:#FF6B6B;">Keranjang kosong!</td></tr>
<?php endif; ?>
</table>

<?php if(!empty($_SESSION['cart'])): ?>
<br>
<button type="submit" name="update_cart" class="btn update-btn">Update Keranjang</button>
<button type="submit" name="checkout" class="checkout-btn">Checkout</button>
<?php endif; ?>
</form>

<div class="total-box">Total Bayar: Rp <?= number_format($total,0,',','.') ?></div>
</div>

<a href="index.php" class="back-btn">⬅ Kembali Belanja</a>

</body>
</html>
