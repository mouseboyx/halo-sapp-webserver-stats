<html>
<head>
<title>Halo Stats Admin</title>
<?php include '../head.php'; ?>
</head>
<body>
<div class="container">
<?php
    include '../connect.php';
    if ($c) {
        $q='select * from users where admin=1';
        $res=mysqli_query($c,$q);
        //print_r($res);
        if ($res==null) {
            ?>
                <h1 class="admin">Install and Configuration</h1>
                <form action="install.php" method="post">
                Username for the admin user <input type="text" id="username" name="username" value="admin">
                <br>
                Password for the admin user <input type="password" id="password" name="password" value="">
                <br>
                Confirm password for the admin user <input type="password" id="confirm" name="confirm" value="">
                <br>
                This is being setup on an https:// server <input type="checkbox" id="https" name="https" checked>
                <br>
                (Must be checked for https:// --- Must be unchecked for plain http://)
                <br>
                (It is recommended to install this on a server that uses https:// or it leaves things open to attack)
                <div id="matchout"></div>
                <input type="submit" value="Install" id="install" disabled>
                </form>
            <?php
        } else {
            include '../isSecure.php';
            ini_set('session.use_strict_mode',"1");
            if ($https==true) {
            ini_set('session.cookie_secure',"on");
            }
            ini_set('session.cookie_lifetime',"0");
            ini_set('session.cookie_httponly',"1");
            if ($https==true) {
                if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "" ) {
                    session_start();
                }
            } else {
                    session_start();
            }
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']==true && $_SESSION['admin']==1) {
                    include 'console.php';
            } else {
                ?>
                
                <h1 class="admin">Login</h1>
                <section class="admin">
                <form action="login.php" method="post">
                    <div><div class="form">Username:</div><div class="form"><input type="text" id="username" name="username" value=""></div></div>
                    <div><div class="form">Password:</div><div class="form"><input type="password" id="password" name="password" value=""></div></div>
                    <input type="submit" value="Login">
                </form>
                </section>
                <?php
            }
        }
    } else {
        ?>
        <h1 class="admin">Install and Configuration</h1>
        <?php
        echo 'You need to update connect.php with credentials for the mysql server and database name before proceeding.';
    }
?>
</div>
<script>
username=document.getElementById('username');
password=document.getElementById('password');
confirm=document.getElementById('confirm');
matchout=document.getElementById('matchout');
install=document.getElementById('install');
password.addEventListener('keyup',function () {
    match();
});
confirm.addEventListener('keyup',function () {
    match();
});
username.addEventListener('keyup',function () {
    match();
});
function match() {
    if (username.value.length<5) {
        matchout.innerHTML='5 Characters minimum for username';
        matchout.style.color='red';
        install.disabled=true
    } else {
        install.disabled=false
        if (password.value.length<6) {
            matchout.innerHTML='6 Characters minimum for password';
            matchout.style.color='red';
            install.disabled=true
        } else {
            
            install.disabled=false
            if (password.value==confirm.value) {
                matchout.innerHTML='Passwords Match';
                matchout.style.color='darkgreen';
                install.disabled=false
            } else {
                matchout.innerHTML='Passwords Do Not Match!';
                matchout.style.color='red';
                install.disabled=true
            }
        }
    }
}
</script>
</body>
