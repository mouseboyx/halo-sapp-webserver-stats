<html>
<?php include '../head.php'; ?>
<body>
<?php
    //create table users (id bigint auto_increment,name varchar(255),salt varchar(255),password varchar(255), admin int, primary key(id))
    include '../connect.php';
    $q='select * from users where admin=1';
    $res=mysqli_query($c,$q);
    //print_r($res);
    
    if ($res==null) {
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['https']) && isset($_POST['confirm'])) {
            if (strlen($_POST['username'])<5) {
                echo 'Username too short';
            } else {
                if (strlen($_POST['password'])<6) {
                    echo 'Password too short';
                } else {
                    //echo '1';
                    $q='create table users (id bigint auto_increment,name varchar(255),password varchar(255), admin int, primary key(id))';
                    
                    $res=mysqli_query($c,$q);
                    if ($res) {
                        
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
                                $q='create table servers (id bigint auto_increment,name varchar(70), request_key varchar(255), ip varchar(25),primary key(id))';
                                $res=mysqli_query($c,$q);
                                if ($res) {
                                    $q='create table server_settings (server_id bigint,description varchar(300),check_ip int)';
                                    $res=mysqli_query($c,$q);
                                    if ($res) {
                                        $q='create table players (id bigint auto_increment,name varchar(25),ip varchar(25),primary key(id))';
                                        $res=mysqli_query($c,$q);
                                        if ($res) {
                                            $q='create table killed_by (id bigint auto_increment,server_id bigint,killer bigint,victim bigint,times bigint,primary key(id))';
                                            $res=mysqli_query($c,$q);
                                            if ($res) {
                                                if (isset($_POST['https'])) {
                                                        file_put_contents ( '../isSecure.php','<?php $https=true; ?>');
                                                } else {
                                                        file_put_contents ( '../isSecure.php','<?php $https=false; ?>');
                                                }
                                                ?>
                                                <div>Installed Success, <a href="index.php">return to login</a>
                                                <?php
                                            } else {
                                                echo 'Error creating killed by table';
                                            }
                                        } else {
                                            echo 'Error creating players table';
                                        }
                                    } else {
                                        echo 'Error creating server settings table';
                                    }
                                } else {
                                    echo 'Error creating servers table';
                                }
                            } else {
                                echo 'Error adding admin user';
                            }
                        }
                    } else {
                        echo 'Error creating tables';
                    }
                }
            }
        } else {
            echo 'Post data error';
        }
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
</body>
</html>
