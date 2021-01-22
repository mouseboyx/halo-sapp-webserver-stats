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
    if (isset($_POST['delmap'])) {
        foreach ($_POST['delmap'] as $map) {
            $map_id=mysqli_real_escape_string($c,$map);
            $q="delete from ".$t_prefix."user_damage_tag_maps where map_id=".$map;
            $res=mysqli_query($c,$q);
            
            $q="delete from ".$t_prefix."maps where id=".$map;
            $res=mysqli_query($c,$q);
        }
    }
    if (isset($_POST['newmap']) && $_POST['newmap']!='') {
        
        $new_map=mysqli_real_escape_string($c,$_POST['newmap']);
        //echo $new_map;
        $q="insert into ".$t_prefix."maps (name) values ('".$new_map."')";
        $res=mysqli_query($c,$q);
    }
    if (isset($_POST['default']) && $_POST['default']==1) {
        $q="delete from ".$t_prefix."maps";
        $res=mysqli_query($c,$q);
        include '../maps.php';
        foreach ($stock_halo_maps as $map) {
            $map_escape=mysqli_real_escape_string($c,$map);
            $q="insert into ".$t_prefix."maps (name) values ('".$map_escape."')";
            $res=mysqli_query($c,$q);
            if ($res) {
                echo $q.'<br>';
            } else {
                echo 'Error on '.$q;
                break;
            }
        }
    }
                
}
?>
<html>
<body>
<script>
window.location.href="maplist.php";
</script>
</body>
</html>
