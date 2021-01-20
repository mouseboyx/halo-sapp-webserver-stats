<?php
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
            include '../connect.php';
            include '../tablePrefix.php';
?>
<html>
<head>
<title>Edit Damage Tag List</title>
<?php include '../head.php'; ?>
</head>
<body>
<div class="container">
<h1 class="admin">Edit Damage Tag List</h1>

        <?php include 'adminmenu.php' ?>
<?php
if (isset($_POST['listname']) && isset($_POST['damagetags']) && isset($_POST['id'])) {
$list_id=mysqli_real_escape_string($c,strip_tags(htmlspecialchars(intval($_POST['id']))));
    if (isset($_POST['servers']) && isset($_POST['allservers'])) {
        echo 'Error Cannot choose all servers and a selection of servers';
    } else {
        if (isset($_POST['allmaps']) && isset($_POST['maps'])) {
            echo 'Error Cannot choose all maps and a slection of maps';
        } else {
            if (isset($_POST['damagetags']) && $_POST['damagetags']!='') {
                if (isset($_POST['listname']) && $_POST['listname']!='') {
                    if (isset($_POST['active']) && $_POST['active']==1) {
                        $active='1';
                    } else {
                        $active='0';
                    }
                    if (isset($_POST['allservers']) && $_POST['allservers']==1) {
                        $allservers='1';
                        $q="delete from ".$t_prefix."user_damage_tag_servers where damage_tag_list_id=".$list_id;
                        $res=mysqli_query($c,$q);
                    } else {
                        $allservers='0';
                    }
                    if (isset($_POST['allmaps']) && $_POST['allmaps']==1) {
                        $allmaps='1';
                        $q="delete from ".$t_prefix."user_damage_tag_maps where damage_tag_list_id=".$list_id;
                        $res=mysqli_query($c,$q);
                    } else {
                        $allmaps='0';
                        
                    }
                    include '../tablePrefix.php';
                    $list_name=mysqli_real_escape_string($c,strip_tags(htmlspecialchars($_POST['listname'])));
                    $list_id=mysqli_real_escape_string($c,strip_tags(htmlspecialchars(intval($_POST['id']))));
                    $q="insert into ".$t_prefix."user_damage_tag_lists (name,all_maps,all_servers,active) values ('".$list_name."',".$allmaps.",".$allservers.",".$active.")";
                    $q="update ".$t_prefix."user_damage_tag_lists set name='".$list_name."',all_maps=".$allmaps.",all_servers=".$allservers.",active=".$active." where id=".$list_id;
                    $res=mysqli_query($c,$q);
                    if ($res) {
                            if (isset($_POST['servers'])) {
                                //user_damage_tag_servers
                                $q="delete from ".$t_prefix."user_damage_tag_servers where damage_tag_list_id=".$list_id;
                                $res=mysqli_query($c,$q);
                                if ($res) {
                                    foreach ($_POST['servers'] as $server) {
                                        //echo $last_tag_list_id;
                                        $server_id=mysqli_real_escape_string($c,$server);
                                        
                                        $q="select id from ".$t_prefix."servers where id=".$server_id;
                                        //echo "'".$q."'";
                                        $res=mysqli_query($c,$q);
                                        $row=mysqli_fetch_assoc($res);
                                        if ($row!=null) {
                                            $q="insert into ".$t_prefix."user_damage_tag_servers (damage_tag_list_id,server_id) values (".$list_id.",".$server_id.")";
                                            // echo "'".$q."'";
                                            $res=mysqli_query($c,$q);
                                            if ($res) {
                                                
                                            } else {
                                                echo 'Error Mysql error 1';
                                            }
                                        } else {
                                            echo 'Error server id '.$server_id.' does not exist';
                                        }
                                    }
                                } else {
                                    echo 'Error Mysql error 1';
                                }
                            }
                            if (isset($_POST['maps'])) {
                                $q="delete from ".$t_prefix."user_damage_tag_maps where damage_tag_list_id=".$list_id;
                                $res=mysqli_query($c,$q);
                                if ($res) {
                                    foreach ($_POST['maps'] as $map) {
                                        $map_id=mysqli_real_escape_string($c,$map);
                                        $q="select id from ".$t_prefix."maps where id=".$map_id;
                                        $res=mysqli_query($c,$q);
                                        $row=mysqli_fetch_assoc($res);
                                        if ($row!=null) {
                                            $q="insert into ".$t_prefix."user_damage_tag_maps (damage_tag_list_id,map_id) values (".$list_id.",".$map_id.")";
                                            $res=mysqli_query($c,$q);
                                            if ($res) {
                                                
                                            } else {
                                                echo 'Error Mysql error 2';
                                            }
                                        } else {
                                            echo 'Error map id '.$map_id.' does not exist';
                                        }
                                    }
                                }
                            }
                            $lines=explode("\n",$_POST['damagetags']);
                            $q="delete from ".$t_prefix."user_damage_tags where damage_tag_list_id=".$list_id;
                            $res=mysqli_query($c,$q);
                            if ($res) {
                            foreach ($lines as $line) {
                                if ($line!='') {
                                        $data=explode(',',$line);
                                        $tag_path=mysqli_real_escape_string($c,htmlspecialchars($data[0]));
                                        $shown_as=mysqli_real_escape_string($c,htmlspecialchars($data[1]));
                                        $q="insert into ".$t_prefix."user_damage_tags (damage_tag_list_id,tag_path,shown_as) values (".$list_id.",'".$tag_path."','".$shown_as."')";
                                        $res=mysqli_query($c,$q);
                                        if ($res) {
                                            
                                        } else {
                                            echo 'Error inserting '.$tag_path;
                                        }
                                }
                            }
                            echo 'Custom Tag Paths Saved <a href="tagedit.php">Return</a>';
                            } else {
                                echo 'Error Mysql error';
                            }
                    } else {
                        echo $q;
                        echo 'Error Mysql error';
                    }
                    
                    
                } else {
                    echo 'Error Blank list name';
                }
                
            } else {
                echo 'Error no damage tags sent';
            }
        }
    }
    //
    
} else {
?>
<section class="admin">
    <?php
        include '../tablePrefix.php';
        if (isset($_POST['id'])) {
        $list_id=mysqli_real_escape_string($c,$_POST['id']);
        $q="select * from ".$t_prefix."user_damage_tag_lists where id=".$list_id;
        $res=mysqli_query($c,$q);
        $list_row=mysqli_fetch_assoc($res);
        //echo $list_row['id'];
    ?>
    <form action="editdamagetaglist.php" method="post">
    <div class="admin element"><div class="form">Damage Tag List Name: </div><div class="form"><input type="text" id="listname" name="listname" value="<?php echo $list_row['name']; ?>"></div> <div class="form description"> An arbitrary name to identify this list.</div></div>
    
    <div class="admin element"><div class="form">Make this list active: </div><div class="form"><input type="checkbox" id="active" name="active" value="1" <?php if ($list_row['active']==1) { echo 'checked';}?>></div> <div class="form description"> If you want this list to be disabled leave unchecked.</div></div>
    
    <div class="admin element"><div class="form">List applies to all servers: </div><div class="form"><input type="checkbox" id="allservers" name="allservers" value="1" <?php if ($list_row['all_servers']==1) { echo 'checked';}?>></div> <div class="form description"> Check here to make this list apply to all servers including servers added in the future.</div><br><br>
    
    <div class="form">Check servers this should apply to:</div><br>
    <?php
        $q="select ".$t_prefix."servers.name,".$t_prefix."servers.ip,".$t_prefix."servers.id,".$t_prefix."user_damage_tag_servers.server_id from ".$t_prefix."servers inner join ".$t_prefix."user_damage_tag_servers on ".$t_prefix."servers.id=".$t_prefix."user_damage_tag_servers.server_id where damage_tag_list_id=".$list_row['id'];
        $res=mysqli_query($c,$q);
        $active_server_id=[];
        while ($row=mysqli_fetch_assoc($res)) {
            $active_server_id[intval($row['server_id'])] = 1;
        }
        //print_r($active_server_id);
        $q="select * from ".$t_prefix."servers";
        $res=mysqli_query($c,$q);
        if ($res) {
            while ($row=mysqli_fetch_assoc($res)) {
                $checked='';
                if (isset($active_server_id[intval($row['id'])])) {
                    if ($active_server_id[intval($row['id'])]==1) {
                        //echo 'checked'.$row['id'];
                        $checked='checked';
                    }
                }
                echo '<div class="form"><input type="checkbox" class="checkserver" name="servers[]" value="'.$row['id'].'" '.$checked.'> </div> <div class="form">'.$row['name'].' --- '.$row['ip'].'</div><br>';
            }
        }
        
    ?>
    <div class="form description">If you don't see a server in the list add it <a href="serveradd.php">here</a></div>
    </div>
    
    
    <div class="admin element"><div class="form">List applies to all maps: </div><div class="form"><input type="checkbox" id="allmaps" name="allmaps" value="1" <?php if ($list_row['all_maps']==1) { echo 'checked';}?>></div> <div class="form description"> Check here to make this list apply any map including maps not in the list below.</div><br><br>
    
    <div class="form">Check maps this should apply to:</div><br>
    <?php
        $q="select ".$t_prefix."maps.name,".$t_prefix."maps.id,".$t_prefix."user_damage_tag_maps.map_id from ".$t_prefix."maps inner join ".$t_prefix."user_damage_tag_maps on ".$t_prefix."maps.id=".$t_prefix."user_damage_tag_maps.map_id where damage_tag_list_id=".$list_row['id'];
        //echo $q;
        
        $res=mysqli_query($c,$q);
        
        $active_map_id=[];
        while ($row=mysqli_fetch_assoc($res)) {
            $active_map_id[intval($row['map_id'])] = 1;
        }
        
        $q="select * from ".$t_prefix."maps";
        $res=mysqli_query($c,$q);
        if ($res) {
            
            while ($row=mysqli_fetch_assoc($res)) {
                $checked='';
                if (isset($active_map_id[intval($row['id'])])) {
                    if ($active_map_id[intval($row['id'])]==1) {
                        //echo 'checked'.$row['id'];
                        $checked='checked';
                    }
                }
                echo '<div class="form"><input type="checkbox" class="checkmap" name="maps[]" value="'.$row['id'].'" '.$checked.'> </div> <div class="form">'.$row['name'].'</div><br>';
            }
        }
        
    ?>
    <div class="form description">If you don't see a map in the list add it <a href="addmap.php">here</a></div>
    </div>
    
    <div class="admin element"><div class="form">Damage Tag List: </div><textarea name="damagetags" rows="40" style="width:100%" placeholder="For example this could be:&#10weapons\assault rifle\bullet,Assault Rifle&#10;vehicles\banshee\banshee bolt,Banshee Bolt"><?php
        $q="select * from ".$t_prefix."user_damage_tags where damage_tag_list_id=".$list_id;
        $res=mysqli_query($c,$q);
        while ($row=mysqli_fetch_assoc($res)) {
            echo $row['tag_path'].','.$row['shown_as']."\n";
        }
    ?></textarea>
    <div class="form description"> Put each (damage tag/weapon name) combination on a new line separated by a comma (,). For example a few lines could read:<br>
    weapons\assault rifle\bullet,Assault Rifle<br>
    vehicles\banshee\banshee bolt,Banshee Bolt
    </div></div>
    <input type="submit"  style="font-size:2em;margin:0 auto;display:block;margin-top:2em;"  value="Save">
    <?php echo '<input type="hidden" name="id" value="'.$list_id.'">'; ?>
    </form>
</section>
<?php
}
}
?>
</div>
<script>
inputs=document.getElementsByTagName('input');
allmaps=document.getElementById('allmaps');
for (i=0;i<inputs.length;i++) {
    if (inputs[i].getAttribute('class')=='checkmap') {
        inputs[i].addEventListener('click',function () {
                allmaps.checked=false;
        });
    }
}
allmaps.addEventListener('click',function () {
    for (i=0;i<inputs.length;i++) {
        if (inputs[i].getAttribute('class')=='checkmap') {
            inputs[i].checked=false;
        }
    }
});

allservers=document.getElementById('allservers');
for (i=0;i<inputs.length;i++) {
    if (inputs[i].getAttribute('class')=='checkserver') {
        inputs[i].addEventListener('click',function () {
                allservers.checked=false;
        });
    }
}
allservers.addEventListener('click',function () {
    for (i=0;i<inputs.length;i++) {
        if (inputs[i].getAttribute('class')=='checkserver') {
            inputs[i].checked=false;
        }
    }
});
</script>
</body>
</html>
<?php
}
?>
