<?php
include '../isSecure.php';
ini_set('session.use_strict_mode',"1");
if ($https==true) {
ini_set('session.cookie_secure',"on");
}
ini_set('session.cookie_lifetime',"0");
ini_set('session.cookie_httponly',"1");
?>
<html>
<head>
<?php include '../head.php'; ?>
</head>
<body>
<?php
if ($https==true) {
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "" ) {
    session_start();
    include '../connect.php';
    include '../tablePrefix.php';
    if ($c) {
        $username=mysqli_real_escape_string($c,$_POST['username']);
        $password=$_POST['password'];
        $q="select password from ".$t_prefix."users where admin=1 and name='".$username."'";
        $res=mysqli_query($c,$q);
        $row = mysqli_fetch_assoc($res);
        if ($row!=null) {
            $verify=password_verify($password,$row['password']);
            if ($verify==true) {
                $q="select name,id,admin from ".$t_prefix."users where admin=1 and name='".$username."'";
                $res=mysqli_query($c,$q);
                if ($res) {
                    $row = mysqli_fetch_assoc($res);
                    if ($row!=null) {
                        $_SESSION['loggedin']=true;
                        $_SESSION['id']=$row['id'];
                        $_SESSION['admin']=$row['admin'];
                        $_SESSION['name']=$row['name'];
                        ?>
                        <?php echo $row['name']; ?> logged in, <a href="index.php">redirecting</a>...
                        <script>
                        window.location.href='index.php';
                        </script>
                        <?php
                    }
                }
            } else {
                echo 'Username or password incorrect <a href="index.php">Return</a>';
            }
        } else {
            echo 'Username or password incorrect <a href="index.php">Return</a>';
        }       
    }
    
    }
} else {
    session_start();
    include '../connect.php';
    include '../tablePrefix.php';
    if ($c) {
        $username=mysqli_real_escape_string($c,$_POST['username']);
        $password=$_POST['password'];
        $q="select password from ".$t_prefix."users where admin=1 and name='".$username."'";
        $res=mysqli_query($c,$q);
        $row = mysqli_fetch_assoc($res);
        if ($row!=null) {
            $verify=password_verify($password,$row['password']);
            if ($verify==true) {
                $q="select name,id,admin from ".$t_prefix."users where admin=1 and name='".$username."'";
                $res=mysqli_query($c,$q);
                if ($res) {
                    $row = mysqli_fetch_assoc($res);
                    if ($row!=null) {
                        $_SESSION['loggedin']=true;
                        $_SESSION['id']=$row['id'];
                        $_SESSION['admin']=$row['admin'];
                        $_SESSION['name']=$row['name'];
                        ?>
                        <?php echo $row['name']; ?> logged in, <a href="index.php">redirecting</a>...
                        <script>
                        window.location.href='index.php';
                        </script>
                        <?php
                    }
                }
            } else {
                echo 'Username or password incorrect <a href="index.php">Return</a>';
            }
        } else {
            echo 'Username or password incorrect <a href="index.php">Return</a>';
        }       
    }
}
//echo $_SESSION["favcolo"];
?>
</body>
</html>
