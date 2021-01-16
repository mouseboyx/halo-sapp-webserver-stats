<html>
<head>
<?php include '../head.php'; ?>
</head>
<body>
<div class="container">
<?php
    //create table users (id bigint auto_increment,name varchar(255),salt varchar(255),password varchar(255), admin int, primary key(id))
    include '../connect.php';
    $q='select * from users where admin=1';
    $res=mysqli_query($c,$q);
    //print_r($res);
    $row = mysqli_fetch_assoc($res);
    if ($row==null) {
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirm'])) {
            if (strlen($_POST['username'])<5) {
                echo 'Username too short';
            } else {
                if (strlen($_POST['password'])<6) {
                    echo 'Password too short';
                } else {
                    $create_table_queries=[
                    'create table users (id bigint auto_increment,name varchar(255),password varchar(255), admin int, primary key(id))',
                    'create table servers (id bigint auto_increment,name varchar(70), request_key varchar(255), ip varchar(25),primary key(id))',
                    'create table server_settings (server_id bigint,description varchar(300),check_ip int,paused int)',
                    'create table players (id bigint auto_increment,name varchar(25),ip varchar(25),primary key(id))',
                    'create table killed_by (id bigint auto_increment,server_id bigint,killer bigint,victim bigint,times bigint,primary key(id))'];
                    
                    foreach ($create_table_queries as $q) {
                        if (mysqli_query($c,$q)) {
                            echo $q.'<br>';
                        } else {
                            echo 'Could not execute '.$q;
                            break;
                        }
                    }
                    
                    
                    
                    $username=mysqli_real_escape_string($c,$_POST['username']);
                    $password=$_POST['password'];
                    $confirm=$_POST['confirm'];
                    
                    if ($password!=$confirm) {
                        echo 'Passwords do not match';
                    } else {
                        //echo 'match';
                        //$salt=str_shuffle(rand(1,1000000).time().rand(1,1000000).getUserIpAddr().rand(1,1000000).$_SERVER['HTTP_USER_AGENT'].rand(1,1000000).'halo rocks')
                        $pass_hash=password_hash($password,PASSWORD_DEFAULT);
                        
                        $q="insert into users (name,password,admin) values ('".$username."','".$pass_hash."',1)";
                        
                        $res=mysqli_query($c,$q);
                        if ($res) {
                            echo 'Created user '.$username.'<br>';
                            if (isset($_POST['https'])) {
                                    file_put_contents ( '../isSecure.php','<?php $https=true; ?>');
                            } else {
                                    file_put_contents ( '../isSecure.php','<?php $https=false; ?>');
                            }
                            ?>
                            <div>Installed Success, <a href="index.php">return to login</a></div>
                            <?php
                        }
                        
                    }
                }
            }
        }
    } else {
        echo 'The service is currently installed.';
    }
         
         
         
         
    function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
?>
</div>
</body>
</html>
