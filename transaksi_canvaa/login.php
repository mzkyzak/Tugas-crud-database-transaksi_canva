<?php
session_start();
include 'koneksi.php';

// Jika sudah login, langsung redirect
if(isset($_SESSION['role'])){
    if($_SESSION['role'] == 'admin'){
        header("Location: dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// Login process
$error = '';
if(isset($_POST['login'])){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Query user
    $q = $conn->query("SELECT * FROM users WHERE username='$username' AND password=MD5('$password')");
    if($q->num_rows > 0){
        $user = $q->fetch_assoc();

        // Simpan session
        $_SESSION['id_user'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect sesuai role
        if($user['role'] == 'admin'){
            header("Location: dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login</title>
<style>
body {
    margin:0;
    padding:0;
    font-family:"Poppins", sans-serif;
    background: linear-gradient(135deg,#0a1024,#121a35,#0c1e32);
    background-size:250% 250%;
    animation:bgMove 7s infinite alternate;
    color:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
@keyframes bgMove{
    from{background-position:0% 30%;}
    to{background-position:100% 70%;}
}
.login-box{
    background: rgba(14,23,46,0.85);
    padding: 30px;
    border-radius: 18px;
    box-shadow: 0 0 25px rgba(0,224,255,0.3);
    text-align:center;
}
h2{
    margin-bottom:20px;
    color:#00E0FF;
    text-shadow:0 0 6px #00E0FF;
}
input[type=text], input[type=password]{
    width:250px;
    padding:10px;
    margin:10px 0;
    border-radius:8px;
    border:1px solid #00E0FF;
    background:#10182C;
    color:#00E0FF;
}
button{
    padding:10px 20px;
    border:none;
    border-radius:8px;
    background:#06c258;
    color:#fff;
    font-weight:600;
    cursor:pointer;
    transition:0.25s;
}
button:hover{
    transform: scale(1.05);
    box-shadow:0 0 12px #06c258,0 0 24px #06c25880;
}
.error{
    color:#FF003C;
    margin-bottom:10px;
}
</style>
</head>
<body>
<div class="login-box">
    <h2>Login</h2>
    <?php if($error) echo "<div class='error'>$error</div>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="login">Login</button>
    </form>
</div>
</body>
</html>