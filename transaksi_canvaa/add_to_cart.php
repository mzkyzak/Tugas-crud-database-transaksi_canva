<?php
session_start();
include "koneksi.php";

$id = $_GET['id'];
$produk = $conn->query("SELECT * FROM photo WHERE id=$id")->fetch_assoc();

if (!$produk) {
    header("Location: index.php");
    exit;
}

// Jika keranjang kosong, buat baru
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Jika produk sudah ada → tambah qty
if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]['qty'] += 1;
} else {
    $_SESSION['cart'][$id] = [
        "nama"  => $produk['nama'],
        "foto"  => $produk['foto'],
        "harga" => $produk['harga'],
        "qty"   => 1
    ];
}

header("Location: keranjang.php");