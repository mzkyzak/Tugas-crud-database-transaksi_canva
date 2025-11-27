<?php
session_start();
include 'koneksi.php';

// Jika sudah login, redirect
if(isset($_SESSION['role'])){
    if($_SESSION['role'] == 'admin'){
        header("Location: dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

$error = '';
$success = '';

// =========================
// PROSES LOGIN
// =========================
if(isset($_POST['login'])){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $q = $conn->query("SELECT * FROM users WHERE username='$username' AND password=MD5('$password')");
    if($q->num_rows > 0){
        $user = $q->fetch_assoc();
        $_SESSION['id_user']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        if($user['role'] == 'admin'){
            header("Location: dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "❌ Username atau password salah!";
    }
}

// =========================
// PROSES REGISTER
// =========================
if(isset($_POST['register'])){
    $reg_username = trim($_POST['reg_username']);
    $reg_password = trim($_POST['reg_password']);

    // Cek apakah username sudah ada
    $cek = $conn->query("SELECT * FROM users WHERE username='$reg_username'");
    if($cek->num_rows > 0){
        $error = "❌ Username sudah digunakan!";
    } else {
        // Simpan user baru
        $conn->query("INSERT INTO users (username,password,role) 
                      VALUES('$reg_username', MD5('$reg_password'), 'user')");
        $success = "✅ Akun berhasil dibuat, silakan login!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login & Register</title>
<style>
body{
    margin:0;
    padding:0;
    font-family:"Poppins",sans-serif;
    background:linear-gradient(135deg,#0a1024,#121a35,#0c1e32);
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
    background:rgba(14,23,46,0.9);
    padding:30px;
    border-radius:18px;
    box-shadow:0 0 25px rgba(0,224,255,0.35);
    text-align:center;
    width:320px;
}
h2{
    color:#00E0FF;
    text-shadow:0 0 8px #00E0FF;
    margin-bottom:15px;
}
input{
    width:92%;
    padding:10px;
    margin:8px 0;
    border-radius:8px;
    border:1px solid #00E0FF;
    background:#10182C;
    color:#00E0FF;
}
button{
    padding:10px 20px;
    border:none;
    border-radius:10px;
    background:#06c258;
    color:#fff;
    font-weight:600;
    cursor:pointer;
    margin-top:10px;
    transition:.25s;
}
button:hover{
    transform:scale(1.05);
    box-shadow:0 0 12px #06c258, 0 0 24px #06c25880;
}
.switch{
    margin-top:15px;
    color:#00E0FF;
    cursor:pointer;
    font-size:14px;
}
.error{color:#ff003c;margin-bottom:8px;}
.success{color:#06c258;margin-bottom:8px;}
</style>

<script>
function showRegister(){
    document.getElementById('login-form').style.display='none';
    document.getElementById('register-form').style.display='block';
}
function showLogin(){
    document.getElementById('login-form').style.display='block';
    document.getElementById('register-form').style.display='none';
}
</script>
</head>
<body>

<div class="login-box">

<?php if($error) echo "<div class='error'>$error</div>"; ?>
<?php if($success) echo "<div class='success'>$success</div>"; ?>

<!-- LOGIN FORM -->
<div id="login-form">
    <h2>Login</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
    <div class="switch" onclick="showRegister()">Belum punya akun? Daftar</div>
</div>

<!-- REGISTER FORM -->
<div id="register-form" style="display:none;">
    <h2>Register</h2>
    <form method="POST">
        <input type="text" name="reg_username" placeholder="Username baru" required>
        <input type="password" name="reg_password" placeholder="Password baru" required>
        <button type="submit" name="register">Daftar</button>
    </form>
    <div class="switch" onclick="showLogin()">Sudah punya akun? Login</div>
</div>

</div>
</body>
</html>
