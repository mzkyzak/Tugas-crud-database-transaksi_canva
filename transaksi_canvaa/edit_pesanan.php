<?php
include 'koneksi.php';

$id = $_GET['id'];
$data = mysqli_query($conn, "SELECT * FROM pesanan WHERE id='$id'");
$row = mysqli_fetch_assoc($data);

if(isset($_POST['update'])){
    $produk = $_POST['produk'];
    $jumlah = $_POST['jumlah'];
    $total  = $_POST['total'];

    mysqli_query($conn, "UPDATE pesanan SET produk='$produk', jumlah='$jumlah', total='$total' WHERE id='$id'");

    header("Location: dashboard_admin.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Pesanan</title></head>
<body>

<h2>Edit Pesanan</h2>

<form method="POST">
    Produk: <br>
    <input type="text" name="produk" value="<?= $row['produk'] ?>"><br><br>

    Jumlah: <br>
    <input type="number" name="jumlah" value="<?= $row['jumlah'] ?>"><br><br>

    Total Harga: <br>
    <input type="number" name="total" value="<?= $row['total'] ?>"><br><br>

    <button type="submit" name="update">Simpan</button>
</form>

</body>
</html>
