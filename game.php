<?php
        if (isset($_GET['newgame']) && $_GET['newgame']==1 && isset($_GET['map']) && isset($_GET['mode']) && isset($_GET['type']) && isset($_GET['key'])) {
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
                            $map=mysqli_real_escape_string($c,$_GET['map']);
                            $mode=mysqli_real_escape_string($c,$_GET['mode']);
                            $type=mysqli_real_escape_string($c,$_GET['type']);
                            $q="select * from ".$t_prefix."maps where name='".$map."'";
                            $res=mysqli_query($c,$q);
                            if ($res) {
                                $row_map=mysqli_fetch_assoc($res);
                                if ($row_map!=null) {
                                    //echo 1;
                                } else {
                                    //echo 0;
                                $q="insert into ".$t_prefix."maps (name) values ('".$map."')";
                                $res=mysqli_query($c,$q);
                                }
                            } else {
                                //echo 0;
                            }
                            /* TO DO
                                Find the last game id before inserting a new game row
                                Find all the player_id from the last game
                                Update their k/d ratio in the new table kills_deaths_kdr by dividing their kills and deaths from kills_deaths_kdr
                            */
                            $q="insert into ".$t_prefix."games (server_id,map,mode,type) values (".$row['id'].",'".$map."','".$mode."','".$type."')";
                            $res=mysqli_query($c,$q);
                            /* the code for calculating the k/d ratios should come after a new row is inserted for a new game because it may take some time, and the new game id should be availabe as soon as possible so that stats are not placed in a previous game
                            */
                        }
                    }
                } else {
                    if ($request_key==$row['request_key']) {
                            $map=mysqli_real_escape_string($c,$_GET['map']);
                            $mode=mysqli_real_escape_string($c,$_GET['mode']);
                            $type=mysqli_real_escape_string($c,$_GET['type']);
                            $q="select * from ".$t_prefix."maps where name='".$map."'";
                            $res=mysqli_query($c,$q);
                            if ($res) {
                                $row_map=mysqli_fetch_assoc($res);
                                if ($row_map!=null) {
                                    //echo 1;
                                } else {
                                    //echo 0;
                                $q="insert into ".$t_prefix."maps (name) values ('".$map."')";
                                $res=mysqli_query($c,$q);
                                }
                            } else {
                                //echo 0;
                            }
                            $q="insert into ".$t_prefix."games (server_id,map,mode,type) values (".$row['id'].",'".$map."','".$mode."','".$type."')";
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
