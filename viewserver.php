<?php
include 'connect.php';
include 'tablePrefix.php';
include 'functions.php';
$sid=mysqli_real_escape_string($c,intval($_GET['id']));
$q="select id from ".$t_prefix."servers where id=".$sid;
$res=mysqli_query($c,$q);
$server_row=mysqli_fetch_assoc($res);
$q="select max(id) as id from ".$t_prefix."games where server_id=".$server_row['id'];

$res=mysqli_query($c,$q);
$games_row=mysqli_fetch_assoc($res);

$q="select started_on from ".$t_prefix."games where server_id=".$server_row['id']." and id=".$games_row['id'];

$res=mysqli_query($c,$q);
$games_started_on=mysqli_fetch_assoc($res);
?>
<html>
<head>
<?php 
 include 'sitehead.php';


?>
</head>
<body>
<?php include 'header.php'; ?>

<div class="border">
<h2>Latest Game Stats &mdash; Game Started on <?php echo $games_started_on['started_on']; ?> UTC</h2>
<?php
    //$q="select ".$t_prefix."game_server_kills.times as kills,".$t_prefix."players.name,".$t_prefix."players.id,".$t_prefix."players.ip,".$t_prefix."game_server_deaths.times as deaths from ".$t_prefix."game_server_kills inner join ".$t_prefix."players on ".$t_prefix."game_server_kills.".$t_prefix."killer=players.id inner join ".$t_prefix."game_server_deaths on ".$t_prefix."game_server_deaths.victim=".$t_prefix."players.id where ".$t_prefix."game_server_kills.game_id=".$games_row['id']." and ".$t_prefix."game_server_kills.server_id=".$server_row['id']." and ".$t_prefix."game_server_deaths.server_id=".$server_row['id']." and ".$t_prefix."game_server_deaths.game_id=".$games_row['id'];
    $kills_deaths_array=[];
    $q="select * from ".$t_prefix."game_server_kills inner join ".$t_prefix."players on ".$t_prefix."players.id=".$t_prefix."game_server_kills.killer where game_id=".$games_row['id'];
    $kills_res=mysqli_query($c,$q);
    
    while ($kills_row=mysqli_fetch_assoc($kills_res)) {
        $kills_deaths_array[$kills_row['killer']]['kills']=$kills_row['times'];
        $kills_deaths_array[$kills_row['killer']]['name']=$kills_row['name'];
        $kills_deaths_array[$kills_row['killer']]['ip']=$kills_row['ip'];
    }
    $q="select * from ".$t_prefix."game_server_deaths inner join ".$t_prefix."players on ".$t_prefix."players.id=".$t_prefix."game_server_deaths.victim where game_id=".$games_row['id'];
    $deaths_res=mysqli_query($c,$q);
    
    while ($deaths_row=mysqli_fetch_assoc($deaths_res)) {
        $kills_deaths_array[$deaths_row['victim']]['deaths']=$deaths_row['times'];
        $kills_deaths_array[$deaths_row['victim']]['name']=$deaths_row['name'];
        $kills_deaths_array[$deaths_row['victim']]['ip']=$deaths_row['ip'];
    }
    
    foreach ($kills_deaths_array as $key => $value) {
 
        if (!isset($value['deaths'])) {
            $value['deaths']=0;
        }
        if (!isset($value['kills'])) {
            $value['kills']=0;
        }
        echo '<div class="player"><a href="viewplayer.php?id='.$key.'"><h3 class="server">'.$value['name'].' '.obscureIp($value['ip']).'&mdash; Kills: '.$value['kills'].' &mdash; Deaths: '.$value['deaths'].'</h3></a></div>';
    }
    /*
    $kills_deaths_res=mysqli_query($c,$q);
    echo $q;
    while ($kills_deaths_row=mysqli_fetch_assoc($kills_deaths_res)) {
        echo '<div class="player"><a href="viewplayer.php?id='.$kills_deaths_row['id'].'"><h3 class="server">'.$kills_deaths_row['name'].' '.obscureIp($kills_deaths_row['ip']).'&mdash; Kills: '.$kills_deaths_row['kills'].' &mdash; Deaths: '.$kills_deaths_row['deaths'].'</h3></a></div>';
    }*/
?>
</div>
<div class="border">
        <div class="player"><a href="allgames.php?server_id=<?php echo $server_row['id']; ?>&game_id=<?php echo $games_row['id']; ?>" class="servers"><h1 class="servers">Click Here to View All Games</h1></a></div>
</body>
</html>
