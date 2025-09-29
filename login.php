<?php
include 'config.php'; // Include DB connection


$message = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statement for security
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ? LIMIT 1");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        header("Location: index.php"); // Redirect to dashboard
        exit();
    } else {
        $message = "<p class='error'>Invalid username or password!</p>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SLIATE Login</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
html,body{height:100%;background:#f0f2f5;}
body{
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    align-items:center;
    padding:0 20px;
}

/* Header */
header{
    display:flex;
    align-items:center;
    justify-content:center;
    flex-wrap:wrap;
    gap:15px;
    margin-bottom:30px;
    text-align:center;
    padding:20px 0;
}
header img{width:80px;height:80px;}
header h1{font-size:1rem;font-weight:700;color:#1a1a1a;letter-spacing:0.5px;}
header h1{font-size:2rem;font-weight:700;color:#2575fc;text-align:center;}

footer {
    width: 100%;
    text-align: center;
    padding: 15px 0;
    font-size: 1rem;
    color: #fff;
    background: linear-gradient(90deg, #2575fc, #6a11cb);
    border-radius: 10px;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    font-weight: 500;
}
footer a {
    color: #fff;
    font-weight: 600;
    text-decoration: none;
    transition: 0.3s;
}
footer a:hover {
    text-decoration: underline;
    color: #ffd700;
}
@media(max-width:480px){
    footer { font-size: 0.85rem; padding: 10px 0; }
}

/* Login Container */
.login-container{
    background:#fff;
    border-radius:15px;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
    width:100%;
    max-width:400px;
    padding:60px 30px 30px 30px;
    text-align:center;
    position:relative;
}
.login-container .logo{
    position:absolute;
    top:-40px;
    left:50%;
    transform:translateX(-50%);
    width:80px;height:80px;
    background:#fff;
    border-radius:50%;
    display:flex;justify-content:center;align-items:center;
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}
.login-container .logo img{width:70px;height:70px;border-radius:50%;}
.login-container h2{margin-top:10px;margin-bottom:25px;font-weight:700;color:#333;}
.login-container label{display:block;text-align:left;margin-bottom:5px;font-weight:500;color:#555;}

.input-group{position:relative;margin-bottom:20px;}
.input-group i{
    position:absolute;top:50%;left:12px;transform:translateY(-50%);
    color:#999;
}
.login-container input[type="text"],
.login-container input[type="password"]{
    width:100%;padding:12px 15px 12px 40px;
    border-radius:10px;border:1px solid #ccc;
    font-size:1rem;transition:0.3s;
}
.login-container input:focus{
    border-color:#2575fc;
    box-shadow:0 0 8px rgba(37,117,252,0.2);
    outline:none;
}
.login-container input[type="submit"]{
    width:100%;padding:12px;
    font-size:1rem;font-weight:600;
    border:none;border-radius:10px;
    cursor:pointer;
    background:linear-gradient(45deg,#2575fc,#6a11cb);
    color:#fff;transition:0.3s;
}
.login-container input[type="submit"]:hover{
    background:linear-gradient(45deg,#6a11cb,#2575fc);
    box-shadow:0 5px 15px rgba(37,117,252,0.4);
}
.forgot-password{
    display:inline-block;margin-top:15px;
    font-size:0.9rem;color:#2575fc;font-weight:500;
    text-decoration:none;transition:0.3s;
}
.forgot-password:hover{color:#6a11cb;transform:translateY(-2px);}
.error{color:red;margin-bottom:15px;}

/* Responsive */
@media(max-width:768px){
    header h1{font-size:1rem;}
    header img{width:70px;height:70px;}
}
@media(max-width:480px){
    header{flex-direction:column;gap:10px;margin-bottom:20px;}
    header h1{font-size:0.95rem;line-height:1.4;}
    footer{font-size:0.8rem;}
    .login-container{padding:50px 20px 25px;}
    .login-container h2{font-size:1.3rem;}
    .login-container input[type="text"],
    .login-container input[type="password"]{padding:10px 15px 10px 35px;}
    .login-container input[type="submit"]{padding:10px;font-size:0.9rem;}
    .login-container .logo{width:70px;height:70px;top:-35px;}
    .login-container .logo img{width:60px;height:60px;}
}
</style>
</head>
<body>

<header>
    <h1>
    <img src="images/SLIATE_logo.png" alt="Logo">
    <h1>Advanced Technological Institute, Jaffna <br>Student Attendance System</h1>
    <img src="images/atijaffna_logo.jpg" alt="Logo">
</h1>
</header>

<div class="login-container">
    <div class="logo">
        <img src="images/atijaffna_logo.jpg" alt="Logo">
    </div>
    <h2>Login</h2>
    <?= $message ?>
    <form method="post">
        <div class="input-group">
            <i class="fa fa-user"></i>
            <input type="text" name="username" placeholder="Enter your username" required>
        </div>
        <div class="input-group">
            <i class="fa fa-lock"></i>
            <input type="password" name="password" placeholder="Enter your password" required>
        </div>
        <input type="submit" name="login" value="Login">

        <!-- Forgot password link -->
        <a href="https://wa.me/94764328827?text=Hello%2C%20I%20want%20to%20reset%20my%20Student%20Attendance%20System%20password." 
           target="_blank" class="forgot-password">Forgot Password?</a>
    </form>
</div>

<footer>
  <p>
    Developed with <span style="color:red;">❤️</span> by 
    <a href="https://harishpavan-dev.vercel.app" target="_blank">Bavananthan Harishpavan</a>
  </p>
</footer>

</body>
</html>
