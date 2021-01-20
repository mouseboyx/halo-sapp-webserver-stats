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
<title>Create Damage Tag List</title>
<?php include '../head.php'; ?>
</head>
<body>
<div class="container">
<h1 class="admin">Create Damage Tag List</h1>

        <?php include 'adminmenu.php' ?>
<?php
if (isset($_POST['listname']) && isset($_POST['damagetags'])) {
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
                    } else {
                        $allservers='0';
                    }
                    if (isset($_POST['allmaps']) && $_POST['allmaps']==1) {
                        $allmaps='1';
                    } else {
                        $allmaps='0';
                    }
                    include '../tablePrefix.php';
                    $list_name=mysqli_real_escape_string($c,strip_tags(htmlspecialchars($_POST['listname'])));
                    $q="insert into ".$t_prefix."user_damage_tag_lists (name,all_maps,all_servers,active) values ('".$list_name."',".$allmaps.",".$allservers.",".$active.")";
                    $res=mysqli_query($c,$q);
                    if ($res) {
                        $q='select last_insert_id()';
                        $res=mysqli_query($c,$q);
                        if ($res) {
                            $row=mysqli_fetch_assoc($res);
                            $last_tag_list_id=$row['last_insert_id()'];
                                if (isset($_POST['servers'])) {
                                    //user_damage_tag_servers
                                    foreach ($_POST['servers'] as $server) {
                                        //echo $last_tag_list_id;
                                        $server_id=mysqli_real_escape_string($c,$server);
                                        $q="select id from ".$t_prefix."servers where id=".$server_id;
                                        //echo "'".$q."'";
                                        $res=mysqli_query($c,$q);
                                        $row=mysqli_fetch_assoc($res);
                                        if ($row!=null) {
                                            $q="insert into ".$t_prefix."user_damage_tag_servers (damage_tag_list_id,server_id) values (".$last_tag_list_id.",".$server_id.")";
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
                                }
                                if (isset($_POST['maps'])) {
                                    //user_damage_tag_servers
                                    foreach ($_POST['maps'] as $map) {
                                        $map_id=mysqli_real_escape_string($c,$map);
                                        $q="select id from ".$t_prefix."maps where id=".$map_id;
                                        $res=mysqli_query($c,$q);
                                        $row=mysqli_fetch_assoc($res);
                                        if ($row!=null) {
                                            $q="insert into ".$t_prefix."user_damage_tag_maps (damage_tag_list_id,map_id) values (".$last_tag_list_id.",".$map_id.")";
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
                                $lines=explode("\n",$_POST['damagetags']);
                                foreach ($lines as $line) {
                                    if ($line!='') {
                                            $data=explode(',',$line);
                                            $tag_path=mysqli_real_escape_string($c,$data[0]);
                                            $shown_as=mysqli_real_escape_string($c,$data[1]);
                                            $q="insert into ".$t_prefix."user_damage_tags (damage_tag_list_id,tag_path,shown_as) values (".$last_tag_list_id.",'".$tag_path."','".$shown_as."')";
                                            $res=mysqli_query($c,$q);
                                            if ($res) {
                                                
                                            } else {
                                                echo 'Error inserting '.$tag_path;
                                            }
                                    }
                                }
                                echo 'Custom Tag Paths Added <a href="tagedit.php">Return</a>';
                        } else {
                            echo $q;
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
    <form action="taglistadd.php" method="post">
    <div class="admin element"><div class="form">Damage Tag List Name: </div><div class="form"><input type="text" id="listname" name="listname" value=""></div> <div class="form description"> An arbitrary name to identify this list.</div></div>
    
    <div class="admin element"><div class="form">Make this list active: </div><div class="form"><input type="checkbox" id="active" name="active" value="1" checked></div> <div class="form description"> If you want this list to be disabled leave unchecked.</div></div>
    
    <div class="admin element"><div class="form">List applies to all servers: </div><div class="form"><input type="checkbox" id="allservers" name="allservers" value="1"></div> <div class="form description"> Check here to make this list apply to all servers including servers added in the future.</div><br><br>
    
    <div class="form">Check servers this should apply to:</div><br>
    <?php
        $q="select * from ".$t_prefix."servers";
        $res=mysqli_query($c,$q);
        if ($res) {
            while ($row=mysqli_fetch_assoc($res)) {
                echo '<div class="form"><input type="checkbox" class="checkserver" name="servers[]" value="'.$row['id'].'"> </div> <div class="form">'.$row['name'].' --- '.$row['ip'].'</div><br>';
            }
        }
        
    ?>
    <div class="form description">If you don't see a server in the list add it <a href="serveradd.php">here</a></div>
    </div>
    
    
    <div class="admin element"><div class="form">List applies to all maps: </div><div class="form"><input type="checkbox" id="allmaps" name="allmaps" value="1"></div> <div class="form description"> Check here to make this list apply any map including maps not in the list below.</div><br><br>
    
    <div class="form">Check maps this should apply to:</div><br>
    <?php
        $q="select * from ".$t_prefix."maps";
        $res=mysqli_query($c,$q);
        if ($res) {
            while ($row=mysqli_fetch_assoc($res)) {
                echo '<div class="form"><input type="checkbox" class="checkmap" name="maps[]" value="'.$row['id'].'"> </div> <div class="form">'.$row['name'].'</div><br>';
            }
        }
        
    ?>
    <div class="form description">If you don't see a map in the list add it <a href="addmap.php">here</a></div>
    </div>
    
    <div class="admin element"><div class="form">Damage Tag List: </div><textarea name="damagetags" rows="40" style="width:100%" placeholder="For example this could be:&#10weapons\assault rifle\bullet,Assault Rifle&#10;vehicles\banshee\banshee bolt,Banshee Bolt"></textarea>
    <div class="form description"> Put each (damage tag/weapon name) combination on a new line separated by a comma (,). For example a few lines could read:<br>
    weapons\assault rifle\bullet,Assault Rifle<br>
    vehicles\banshee\banshee bolt,Banshee Bolt
    </div></div>
    <input type="submit"  style="font-size:2em;margin:0 auto;display:block;margin-top:2em;"  value="Add">
    </form>
</section>
<?php
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
