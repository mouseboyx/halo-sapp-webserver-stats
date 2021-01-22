<?php
$start_time=microtime(true);
//Information isn't sorted by a server basis so far, I was thinking I should create the web based admin interface next, to be able to add servers by key and ip
//after an admin interface is set up and you add a server to the database the sapp script will send a the key that you configure and this script will get the halo server ip to check if the request is not bogus
if ((isset($_GET['killer']) && isset($_GET['victim']) && isset($_GET['killer_ip']) && isset($_GET['victim_ip']) && isset($_GET['killed_by_weapon']) && isset($_GET['key']) && isset($_GET['body_part'])) || isset($_GET['scoring'])) {
    //make sure only valid ipv4 addresses can be used when sent to this script.
    
        //echo $_GET['killer'].' '.$_GET['victim'].' '.$_GET['killer_ip'].' '.$_GET['victim_ip'].' '.$_GET['killed_by_weapon'].' '.$_GET['key'].' '.$_GET['body_part'];
        //////////////////////////////
        //insert into players (server_id,name,ip) select 1,'asdf','1.1.1.1' from dual where not exists (select * from players where name='asdf' and ip='1.1.1.1' and server_id=1 limit 1);

        //////////////////////////////
        include 'connect.php';
        
        include 'tablePrefix.php';
        $request_key=mysqli_real_escape_string($c,$_GET['key']);
        $q="select ".$t_prefix."servers.ip,".$t_prefix."servers.id,".$t_prefix."servers.request_key,".$t_prefix."server_settings.check_ip from ".$t_prefix."servers inner join ".$t_prefix."server_settings on ".$t_prefix."server_settings.server_id=".$t_prefix."servers.id where request_key='".$request_key."'";
        $res=mysqli_query($c,$q);
        $row=mysqli_fetch_assoc($res);
        $server_id=$row['id'];
       // echo '<br>';
       // echo $q;
        //echo '<br>';
        //echo $row['ip'].','.$row['check_ip'];
        if (($row!=null && $row['ip']==getUserIpAddr()) || $row['check_ip']==0) {
            if ($row!=null && $row['request_key']==$_GET['key']) {
            //domain_name.."halo.php?scoring=1&name="..playerName.."&ip="..playerIp.."&score="..playerScore..server_key_request
               // echo 1;
                if (isset($_GET['scoring']) && $_GET['scoring']==1) {
               // echo 1;
                    if (preg_match('/(^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$)/',$_GET['ip'])) {
                        
                        $name=mysqli_real_escape_string($c,utf8_encode($_GET['name']));
                        $ip=mysqli_real_escape_string($c,$_GET['ip']);
                        $q="select id from ".$t_prefix."players where name='".$name."' and ip='".$ip."' and server_id=".$server_id;
                        $res=mysqli_query($c,$q);
                        $row = mysqli_fetch_assoc($res);
                        
                        if ($row==null) {
                            
                            //$q="insert into ".$t_prefix."players (server_id,name,ip) values (".$server_id.",'".$name."','".$ip."')";
                            $q="insert into players (server_id,name,ip) select ".$server_id.",'".$name."','".$ip."' from dual where not exists (select * from players where name='".$name."' and ip='".$ip."' and server_id=".$server_id." limit 1)";
                            $res=mysqli_query($c,$q);
                            if ($res) {
                                $q="select last_insert_id()";
                                $res=mysqli_query($c,$q);
                                $row=mysqli_fetch_assoc($res);
                                $player_id=$row['last_insert_id()'];
                            }
                           
                        } else {
                            $player_id=$row['id'];
                        }
                            $q="select max(id) from ".$t_prefix."games where server_id=".$server_id;
                            $res=mysqli_query($c,$q);
                            $row=mysqli_fetch_assoc($res);
                            $game_id=$row['max(id)'];
                            $score=mysqli_real_escape_string($c,$_GET['score']);
                            
                            $q="select score from ".$t_prefix."game_server_score where game_id=".$game_id." and server_id=".$server_id." and player=".$player_id;
                            $res=mysqli_query($c,$q);
                            $row=mysqli_fetch_assoc($res);
                            if ($row==null) {
                                
                                $q="insert into ".$t_prefix."game_server_score (server_id,game_id,player,score) values (".$server_id.",".$game_id.",".$player_id.",".$score.")";
                                $res=mysqli_query($c,$q);
                            } else {
                                $q="update ".$t_prefix."game_server_score set score=".$score." where server_id=".$server_id." and game_id=".$game_id." and player=".$player_id;
                                $res=mysqli_query($c,$q);
                            }
                        
                    }
                } else {
                    if (preg_match('/(^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$)/',$_GET['killer_ip']) && preg_match('/(^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$)/',$_GET['victim_ip'])) {
                    
                        $killer=mysqli_real_escape_string($c,utf8_encode($_GET['killer']));
                        $victim=mysqli_real_escape_string($c,utf8_encode($_GET['victim']));
                        //echo $killer;
                        $killer_ip=mysqli_real_escape_string($c,$_GET['killer_ip']);
                        $victim_ip=mysqli_real_escape_string($c,$_GET['victim_ip']);
                        
                        if ($res=mysqli_query($c, "select * from ".$t_prefix."players where name='".$killer."' and ip='".$killer_ip."' and server_id=".$server_id)) {
                            
                                
                            $row = mysqli_fetch_assoc($res);
                            if ($row==null) {
                                //echo '"'.gettype($row).'"';
                                //$q="insert into ".$t_prefix."players (server_id,name,ip) values (".$server_id.",'".$killer."','".$killer_ip."')";
                                $q="insert into players (server_id,name,ip) select ".$server_id.",'".$killer."','".$killer_ip."' from dual where not exists (select * from players where name='".$killer."' and ip='".$killer_ip."' and server_id=".$server_id." limit 1)";
                                $res=mysqli_query($c,$q);
                                if ($res) {
                                    //echo 'inserted';
                                }
                                // if ($res=mysqli_query($c,"insert into players
                            } else {
                                //echo '"'.gettype($row).'"';
                            }
                        }
                        if ($res=mysqli_query($c, "select * from ".$t_prefix."players where name='".$victim."' and ip='".$victim_ip."' and server_id=".$server_id)) {
                            $row = mysqli_fetch_assoc($res);
                            if ($row==null) {
                                //$q="insert into ".$t_prefix."players (server_id,name,ip) values (".$server_id.",'".$victim."','".$victim_ip."')";
                                $q="insert into players (server_id,name,ip) select ".$server_id.",'".$victim."','".$victim_ip."' from dual where not exists (select * from players where name='".$victim."' and ip='".$victim_ip."' and server_id=".$server_id." limit 1)";
                                $res=mysqli_query($c,$q);
                                if ($res) {
                                }
                            } else {
                            }
                        }
                      //  echo '<br>';
                        //killed_by
                        $q="select max(id) from ".$t_prefix."games where server_id=".$server_id;
                        $res=mysqli_query($c,$q);
                        $row=mysqli_fetch_assoc($res);
                        $game_id=$row['max(id)'];
                        
                        $q="select id from ".$t_prefix."players where name='".$killer."' and ip='".$killer_ip."' and server_id=".$server_id;
                        $res=mysqli_query($c, $q);
                        $row=mysqli_fetch_assoc($res);
                        $killer_id=$row['id'];
                        
                        $q="select id from ".$t_prefix."players where name='".$victim."' and ip='".$victim_ip."' and server_id=".$server_id;
                        $res=mysqli_query($c, $q);
                        $row=mysqli_fetch_assoc($res);
                        $victim_id=$row['id'];
                       // echo '<br><br>';
                       // echo $victim_id.' '.$killer_id;
                        //echo '<br><br>';
                        //echo $game_id.' '.$killer_id.' '.$victim_id.' '.$server_id;
                        $q="select times from ".$t_prefix."killed_by where server_id=".$server_id." and game_id=".$game_id." and killer=".$killer_id." and victim=".$victim_id;
                        //echo '"'.$q.'"';
                        $res=mysqli_query($c,$q);
                        $row=mysqli_fetch_assoc($res);
                        if ($row==null) {
                            $q="insert into ".$t_prefix."killed_by (server_id,game_id,killer,victim,times) values (".$server_id.",".$game_id.",".$killer_id.",".$victim_id.",1)";
                            $res=mysqli_query($c,$q);
                            $q="select last_insert_id()";
                            $res=mysqli_query($c,$q);
                            $row=mysqli_fetch_assoc($res);
                            $killed_by_id=$row['last_insert_id()'];
                        } else {
                            $q="select id from ".$t_prefix."killed_by where server_id=".$server_id." and game_id=".$game_id." and killer=".$killer_id." and victim=".$victim_id;
                            $res=mysqli_query($c,$q);
                            $row=mysqli_fetch_assoc($res);
                            $killed_by_id=$row['id'];
                            $q="update ".$t_prefix."killed_by set times=times+1 where id=".$killed_by_id;
                            $res=mysqli_query($c,$q);
                        }
                        //killed_by_weapon
                        $killed_by_weapon=mysqli_real_escape_string($c,$_GET['killed_by_weapon']);
                        $q="select id from ".$t_prefix."killed_by_weapon where killed_by_id=".$killed_by_id." and tag_path='".$killed_by_weapon."'";
                        $res=mysqli_query($c,$q);
                        $row=mysqli_fetch_assoc($res);
                        
                        if ($row==null) {
                            $q="insert into ".$t_prefix."killed_by_weapon (killed_by_id,tag_path,times) values (".$killed_by_id.",'".$killed_by_weapon."',1)";
                            $res=mysqli_query($c,$q);
                            $q="select last_insert_id()";
                            $res=mysqli_query($c,$q);
                            $row=mysqli_fetch_assoc($res);
                            $killed_by_weapon_id=$row['last_insert_id()'];
                        } else {
                            $killed_by_weapon_id=$row['id'];
                            $q="update ".$t_prefix."killed_by_weapon set times=times+1 where id=".$killed_by_weapon_id;
                            $res=mysqli_query($c,$q);
                        }
                    
                        
                        
                        //body part
                        $body_part=mysqli_real_escape_string($c,$_GET['body_part']);
                        $q="select times from ".$t_prefix."killed_by_body_part where killed_by_weapon_id=".$killed_by_weapon_id." and hitstring='".$body_part."'";
                        $res=mysqli_query($c,$q);
                        $row=mysqli_fetch_assoc($res);
                        
                        if ($row==null) {
                            $q="insert into ".$t_prefix."killed_by_body_part (killed_by_weapon_id,hitstring,times) values (".$killed_by_weapon_id.",'".$body_part."',1)";
                            $res=mysqli_query($c,$q);
                            
                        } else {
                            $q="update ".$t_prefix."killed_by_body_part set times=times+1 where killed_by_weapon_id=".$killed_by_weapon_id;
                            $res=mysqli_query($c,$q);
                        }
                        //backtap
                        if (isset($_GET['backtap']) && $_GET['backtap']==1) {
                            
                            $q="select times from ".$t_prefix."killed_by_backtap where killed_by_weapon_id=".$killed_by_weapon_id;
                            $res=mysqli_query($c,$q);
                            $row=mysqli_fetch_assoc($res);
                            
                            if ($row==null) {
                                $q="insert into ".$t_prefix."killed_by_backtap (killed_by_weapon_id,times) values (".$killed_by_weapon_id.",1)";
                                $res=mysqli_query($c,$q);
                                
                            } else {
                                $q="update ".$t_prefix."killed_by_backtap times=times+1 where killed_by_weapon_id=".$killed_by_weapon_id;
                                $res=mysqli_query($c,$q);
                            }
                        }
                        //game_server_kills
                        $q="select times from ".$t_prefix."game_server_kills where server_id=".$server_id." and game_id=".$game_id." and killer=".$killer_id;
                        $res=mysqli_query($c,$q);
                        $row=mysqli_fetch_assoc($res);
                        if ($row==null) {
                            $q="insert into ".$t_prefix."game_server_kills (server_id,game_id,killer,times) values (".$server_id.",".$game_id.",".$killer_id.",1)";
                            $res=mysqli_query($c,$q);
                            
                        } else {
                            $q="update ".$t_prefix."game_server_kills set times=times+1 where server_id=".$server_id." and game_id=".$game_id." and killer=".$killer_id;
                            $res=mysqli_query($c,$q);
                        }
                        //game_server_deaths
                        $q="select times from ".$t_prefix."game_server_deaths where server_id=".$server_id." and game_id=".$game_id." and victim=".$victim_id;
                        $res=mysqli_query($c,$q);
                        $row=mysqli_fetch_assoc($res);
                        if ($row==null) {
                            $q="insert into ".$t_prefix."game_server_deaths (server_id,game_id,victim,times) values (".$server_id.",".$game_id.",".$victim_id.",1)";
                            $res=mysqli_query($c,$q);
                            
                        } else {
                            $q="update ".$t_prefix."game_server_deaths set times=times+1 where server_id=".$server_id." and game_id=".$game_id." and victim=".$victim_id;
                            $res=mysqli_query($c,$q);
                        }
                    }
                }
            //end check key    
            
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

//echo '<br>';
//echo 'Time :'.((microtime(true))-$start_time);
?>
