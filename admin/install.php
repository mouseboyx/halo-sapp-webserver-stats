<html>
<head>
<?php include '../head.php'; ?>
</head>
<body>
<div class="container">
<?php
    //create table users (id bigint auto_increment,name varchar(255),salt varchar(255),password varchar(255), admin int, primary key(id))
    if (file_exists('../tablePrefix.php')) {
    include '../connect.php';
    include '../tablePrefix.php';
    $q='select * from '.$t_prefix.'users where admin=1';
    $res=mysqli_query($c,$q);
    //print_r($res);
    $row=mysqli_fetch_assoc($res);
    mysqli_close($c);
    } else {
    $row=null;
    }
    if ($row==null && !file_exists('../tablePrefix.php')) {
        include '../connect.php';
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirm'])) {
            if (strlen($_POST['username'])<5) {
                echo 'Username too short';
            } else {
                if (strlen($_POST['password'])<6) {
                    echo 'Password too short';
                } else {
                    if (isset($_POST['prefix']) && $_POST['prefix']!='') {
                        $alpha='abcdefghijklmnopqrstuvwxyz1234567890';
                        $outPrefix='';
                        for($i=0;$i<strlen($_POST['prefix']);$i++) {
                            $isAlpha=false;
                            for ($j=0;$j<strlen($alpha);$j++) {
                                if ($_POST['prefix'][$i]==$alpha[$j]) {
                                    $isAlpha=true;
                                    break;
                                }
                            }
                            if ($isAlpha==true) {
                                $outPrefix.=$_POST['prefix'][$i];
                            }
                        }
                        $outPrefix.='_';
                    } else {
                        $outPrefix='';
                    }
                    $outPrefix=mysqli_real_escape_string($c,$outPrefix);
                    file_put_contents('../tablePrefix.php','<?php $t_prefix="'.$outPrefix.'"; ?>');
                    $create_table_queries=[
                    'create table '.$outPrefix.'users (id bigint auto_increment,name varchar(255),password varchar(255), admin int, primary key(id))',
                    'create table '.$outPrefix.'servers (id bigint auto_increment,name varchar(70), request_key varchar(255), ip varchar(25),primary key(id))',
                    'create table '.$outPrefix.'server_settings (server_id bigint,description varchar(300),check_ip int,paused int,fallback_stock int)',
                    'create table '.$outPrefix.'players (id bigint auto_increment,name varchar(25),ip varchar(25),primary key(id))',                    
                    'create table '.$outPrefix.'killed_by (id bigint auto_increment,server_id bigint,game_id bigint,killer bigint,victim bigint,times bigint,primary key(id))',
                    'create table '.$outPrefix.'killed_by_weapon (killed_by_id bigint,tag_path varchar(100),times bigint)',
                    'create table '.$outPrefix.'killed_by_headshot (killed_by_id bigint,times bigint)',
                    
                    'create table '.$outPrefix.'games (id bigint auto_increment,server_id bigint,map varchar(100),mode varchar(100),type varchar(100),started_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,primary key(id))',
                    'create table '.$outPrefix.'total_server_deaths (server_id bigint,victim bigint,times bigint)',
                    'create table '.$outPrefix.'total_server_kills (server_id bigint,killer bigint,times bigint)',
                    'create table '.$outPrefix.'game_server_deaths (server_id bigint,game_id bigint,victim bigint,times bigint)',
                    'create table '.$outPrefix.'game_server_kills (server_id bigint,game_id bigint,killer bigint,times bigint)',
                    
                    'create table '.$outPrefix.'stock_damage_tags (id bigint auto_increment,tag_path varchar(100),shown_as varchar(100),primary key(id))',
                    'create table '.$outPrefix.'user_damage_tags (id bigint auto_increment,tag_path varchar(100),shown_as varchar(100),primary key(id))',
                    ];
                    foreach ($create_table_queries as $q) {
                        if (mysqli_query($c,$q)) {
                            echo $q.'<br>';
                        } else {
                            echo 'Could not execute '.$q;
                            break;
                        }
                    }
                    include '../damage.php';
                    foreach ($stock_halo_damage_tags as $tag => $name) {
                        $tag_escape=mysqli_real_escape_string($c,$tag);
                        $name_escape=mysqli_real_escape_string($c,$name);
                        $q="insert into stock_damage_tags (tag_path,shown_as) values ('".$tag_escape."','".$name_escape."')";
                        $res=mysqli_query($c,$q);
                        if ($res) {
                            echo $q.'<br>';
                        } else {
                            echo 'Error on '.$q;
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
                        
                        $q="insert into ".$outPrefix."users (name,password,admin) values ('".$username."','".$pass_hash."',1)";
                        
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
