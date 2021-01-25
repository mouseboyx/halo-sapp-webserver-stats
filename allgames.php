<?php
include 'connect.php';
include 'tablePrefix.php';
include 'functions.php';
$sid=mysqli_real_escape_string($c,intval($_GET['server_id']));
$q="select id from ".$t_prefix."servers where id=".$sid;
$res=mysqli_query($c,$q);
$server_row=mysqli_fetch_assoc($res);
$q="select max(id) as id from ".$t_prefix."games where server_id=".$server_row['id'];

$res=mysqli_query($c,$q);
$games_row=mysqli_fetch_assoc($res);
$game_id=mysqli_real_escape_string($c,intval($_GET['game_id']));

$q="select started_on from ".$t_prefix."games where server_id=".$server_row['id']." and id=".$game_id;

$res=mysqli_query($c,$q);
$games_started_on=mysqli_fetch_assoc($res);
$hide_next=false;
$hide_prev=false;
$q="select id from games where server_id=".$server_row['id']." and id>".$game_id." order by id asc limit 1";
$res=mysqli_query($c,$q);
$row=mysqli_fetch_assoc($res);
if ($row==null) {
    $hide_next=true;
} else {
$next_game_id=$row['id'];
}
$q="select id from games where server_id=".$server_row['id']." and id<".$game_id." order by id desc limit 1";
$res=mysqli_query($c,$q);
$row=mysqli_fetch_assoc($res);
if ($row==null) {
    $hide_prev=true;
} else {
$prev_game_id=$row['id'];
}
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
<h1>Game Stats &mdash; Game Started on <?php echo $games_started_on['started_on']; ?> UTC</h1>
<div class="fcontainer">
<?php if (!$hide_prev) { ?>
<a href="allgames.php?server_id=<?php echo $server_row['id']; ?>&game_id=<?php echo $prev_game_id; ?>"><div class="border fleft"><h1 class="server">&lt; Previous</h1></div></a>
<?php } ?>

<?php if (!$hide_next) { ?>
<a href="allgames.php?server_id=<?php echo $server_row['id']; ?>&game_id=<?php echo $next_game_id; ?>"><div class="border fright"><h1>Next &gt;</h1></div></a>
<?php } ?>
<br style="clear:both">
</div>
<?php
    $kills_deaths_array=[];
    $q="select * from ".$t_prefix."game_server_kills inner join ".$t_prefix."players on ".$t_prefix."players.id=".$t_prefix."game_server_kills.killer where game_id=".$game_id;
    $kills_res=mysqli_query($c,$q);
    
    while ($kills_row=mysqli_fetch_assoc($kills_res)) {
        $kills_deaths_array[$kills_row['killer']]['kills']=$kills_row['times'];
        $kills_deaths_array[$kills_row['killer']]['name']=$kills_row['name'];
        $kills_deaths_array[$kills_row['killer']]['ip']=$kills_row['ip'];
    }
    $q="select * from ".$t_prefix."game_server_deaths inner join ".$t_prefix."players on ".$t_prefix."players.id=".$t_prefix."game_server_deaths.victim where game_id=".$game_id;
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
?>


</div>

</body>
</html>
