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
        //$q='select * from users where admin=1';
        //$res=mysqli_query($c,$q);
        //print_r($res);
        $table_prefix_set=file_exists('../tablePrefix.php');
        if ($table_prefix_set==false) {
            ?>
                <h1 class="admin">Install and Configuration</h1>
                <form action="install.php" method="post">
                Username for the admin user <input type="text" id="username" name="username" value="admin">
                <br>
                Password for the admin user <input type="password" id="password" name="password" value="">
                <br>
                Confirm password for the admin user <input type="password" id="confirm" name="confirm" value="">
                <div id="matchout"></div>
                <br>
                This is being setup on an https:// server <input type="checkbox" id="https" name="https" checked>
                <br>
                <div class="gray pad">
                (Must be checked for https:// --- Must be unchecked for plain http://)
                <br>
                (It is recommended to install this on a server that uses https:// or it leaves things open to attack)
                </div>
                <br>
                Table prefix <input type="text" name="prefix" id="prefix" disabled><input type="button" id="enable-prefix" value="Edit Prefix">
                <div class="gray pad">This is not required. If you are on shared hosting with a single database, adding a table prefix will prevent different applications from creating and using the same table. <br>Only a-z,0-9 allowed. If left blank, no table prefix will be added.</div>
                <div id="prefix-info"></div>
                <br>
                
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
if (password && matchout) {
password.addEventListener('keyup',function () {
    match();
});
}
if (confirm && matchout) {
confirm.addEventListener('keyup',function () {
    match();
});
}
if (username && matchout) {
username.addEventListener('keyup',function () {
    match();
});
}
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
alpha='abcdefghijklmnopqrstuvwxyz1234567890'
prefix=document.getElementById('prefix')
enablePrefix=document.getElementById('enable-prefix');
prefixInfo=document.getElementById('prefix-info')
if (enablePrefix) {
enablePrefix.addEventListener('click',function () {
prefix.disabled=false;
});
}
if (prefix) {
prefix.addEventListener('keyup',function () {
outstring=''
for (i=0;i<prefix.value.length;i++) {
    isAlpha=false
    for (j=0;j<alpha.length;j++) {
        if (prefix.value[i]==alpha[j]) {
            isAlpha=true
            break;
        }
    }
    if (isAlpha==true) {
        outstring+=prefix.value[i];
    }
}
prefix.value=outstring;
prefixInfo.innerHTML='Your prefix will appear as '+outstring+'_table_name_here'

});
}
</script>
</body>
