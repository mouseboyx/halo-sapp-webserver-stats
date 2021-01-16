<?php
        if (isset($_GET['newgame']) && $_GET['newgame']==1) {
            include 'tablePrefix.php';
            include 'connect.php';
            $request_key=mysqli_real_escape_string($c,$_GET['key']);
            $q="select ".$t_prefix."servers.ip,".$t_prefix."servers.request_key,".$t_prefix."server_settings.check_ip,".$t_prefix."servers.id from ".$t_prefix."servers inner join ".$t_prefix."server_settings on ".$t_prefix."servers.id=".$t_prefix."server_settings.server_id where ".$t_prefix."servers.request_key='".$request_key."'";
            $res=mysqli_query($c,$q);
            $row=mysqli_fetch_assoc($res);
            if ($row!=null) {
                if ($row['check_ip']==1) {
                    $thisIp=getUserIpAddr();
                    if ($thisIP==$row['ip']) {
                        if ($request_key==$row['request_key']) {
                            $q="insert into ".$t_prefix."games (server_id) values (".$row['id'].")";
                            $res=mysqli_query($c,$q);
                        }
                    }
                } else {
                    if ($request_key==$row['request_key']) {
                            $q="insert into ".$t_prefix."games (server_id) values (".$row['id'].")";
                            $res=mysqli_query($c,$q);
                        }
                }
            }
        }
        
        function getUserIpAddr() {
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
