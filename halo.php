<?php
//Information isn't sorted by a server basis so far, I was thinking I should create the web based admin interface next, to be able to add servers by key and ip
//after an admin interface is set up and you add a server to the database the sapp script will send a the key that you configure and this script will get the halo server ip to check if the request is not bogus
if (isset($_GET['killer']) && isset($_GET['victim']) && isset($_GET['killer_ip']) && isset($_GET['victim_ip']) && isset($_GET['killed_by_weapon']) && isset($_GET['key'])) {
    //make sure only valid ipv4 addresses can be used when sent to this script.
    if (preg_match('/(^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$)/',$_GET['killer_ip']) && preg_match('/(^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$)/',$_GET['victim_ip'])) {
        echo $_GET['killer'].' '.$_GET['victim'].' '.$_GET['killer_ip'].' '.$_GET['victim_ip'].' '.$_GET['killed_by_weapon'].' '.$_GET['key'].' ';
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
