<?php
//Information isn't sorted by a server basis so far, I was thinking I should create the web based admin interface next, to be able to add servers by key and ip
//after an admin interface is set up and you add a server to the database the sapp script will send a the key that you configure and this script will get the halo server ip to check if the request is not bogus
if (isset($_GET['killer']) && isset($_GET['victim']) && isset($_GET['killer_ip']) && isset($_GET['victim_ip'])) {
    //make sure only valid ipv4 addresses can be used when sent to this script.
    if (preg_match('/(^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$)/',$_GET['killer_ip']) && preg_match('/(^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$)/',$_GET['victim_ip'])) {
       //echo "1<br>";
        //create table players (id bigint auto_increment,name varchar(25),ip varchar(25),primary key(id));
        
        //echo $_GET['killer'].' , '.$_GET['victim'].' , '.$_GET['killer_ip'].' , '.$_GET['victim_ip'];
        //connection to the mysql server the server info is in connect.php, adding config for this to the install process at some  point.
        include 'connect.php';
        $killer=mysqli_real_escape_string($c,$_GET['killer']);
        $victim=mysqli_real_escape_string($c,$_GET['victim']);
        $killer_ip=mysqli_real_escape_string($c,$_GET['killer_ip']);
        $victim_ip=mysqli_real_escape_string($c,$_GET['victim_ip']);
        if ($res=mysqli_query($c, "select * from players where name='".$killer."' and ip='".$killer_ip."'")) {
               
                
            $row = mysqli_fetch_assoc($res);
            if ($row==null) {
                //echo '"'.gettype($row).'"';
                $q="insert into players (name,ip) values ('".$killer."','".$killer_ip."')";
                $res=mysqli_query($c,$q);
                if ($res) {
                    //echo 'inserted';
                }
                // if ($res=mysqli_query($c,"insert into players
            } else {
                //echo '"'.gettype($row).'"';
            }
        }
        if ($res=mysqli_query($c, "select * from players where name='".$victim."' and ip='".$victim_ip."'")) {
            
            
            $row = mysqli_fetch_assoc($res);
            if ($row==null) {
                //echo '"'.gettype($row).'"';
                $q="insert into players (name,ip) values ('".$victim."','".$victim_ip."')";
                $res=mysqli_query($c,$q);
                if ($res) {
                    //echo 'inserted';
                }
                // if ($res=mysqli_query($c,"insert into players
            } else {
                //echo '"'.gettype($row).'"';
            }
        }
        //create table killed_by (id bigint auto_increment,killer bigint,victim bigint,times bigint,primary key(id));

        $q="select id from players where name='".$killer."' and ip='".$killer_ip."'";
        $res=mysqli_query($c,$q);
        $row=mysqli_fetch_assoc($res);
        $killer_id=$row['id'];
        echo $killer_id;
        $q="select id from players where name='".$victim."' and ip='".$victim_ip."'";
        $res=mysqli_query($c,$q);
        $row=mysqli_fetch_assoc($res);
        $victim_id=$row['id'];
        echo $victim_id;
        
        if ($res=mysqli_query($c, "select * from killed_by where killer=".$killer_id." and victim=".$victim_id."")) {
             $row = mysqli_fetch_assoc($res);
             if ($row==null) {
                $q="insert into killed_by (killer,victim,times) values (".$killer_id.",".$victim_id.",1)";
                $res=mysqli_query($c,$q);
             } else {
                $q="update killed_by set times=times+1 where killer=".$killer_id." and victim=".$victim_id;
                $res=mysqli_query($c,$q);
             }
        }
        
        
	}
}
if (isset($_GET['test']) && $_GET['test']=='true') {
    //echo 'User Real IP - '.getUserIpAddr();
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
