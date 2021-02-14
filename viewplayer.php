<?php
include 'connect.php';
include 'tablePrefix.php';
include 'functions.php';
$pid=mysqli_real_escape_string($c,intval($_GET['id']));
$q="select * from ".$t_prefix."players where id=".$pid;
$res=mysqli_query($c,$q);
$player_row=mysqli_fetch_assoc($res);
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

<h1>Player: <?php echo $player_row['name']; ?> IP: <?php echo obscureIp($player_row['ip']); ?></h1>
<div class="border">
<h2>First Seen After: <?php
    $q="select min(".$t_prefix."killed_by.game_id),".$t_prefix."games.started_on from ".$t_prefix."killed_by inner join ".$t_prefix."games on ".$t_prefix."killed_by.game_id=".$t_prefix."games.id where killer=".$player_row['id']." or victim=".$player_row['id']." group by ".$t_prefix."games.id limit 1";
    $started_on_res=mysqli_query($c,$q);
    $started_on_row=mysqli_fetch_assoc($started_on_res);
    echo $started_on_row['started_on'];
?> UTC &mdash; Last Seen After: <?php
    $q="select max(".$t_prefix."killed_by.game_id),".$t_prefix."games.started_on from ".$t_prefix."killed_by inner join ".$t_prefix."games on ".$t_prefix."killed_by.game_id=".$t_prefix."games.id where killer=".$player_row['id']." or victim=".$player_row['id']." group by ".$t_prefix."games.id order by killed_by.game_id desc limit 1";
    $started_on_res=mysqli_query($c,$q);
    $started_on_row=mysqli_fetch_assoc($started_on_res);
    echo $started_on_row['started_on'];
?> UTC</h2>

</div>
<div class="border">
<h2>Seen on these servers</h2>
<?php
     $q="select distinct ".$t_prefix."game_server_kills.server_id,".$t_prefix."servers.name,".$t_prefix."servers.id from ".$t_prefix."game_server_kills inner join ".$t_prefix."servers on ".$t_prefix."game_server_kills.server_id=".$t_prefix."servers.id where game_server_kills.killer=".$player_row['id'];
     $played_servers_res=mysqli_query($c,$q);
     while ($played_servers_row=mysqli_fetch_assoc($played_servers_res)) {
        echo '<div class="player"><a href="viewserver.php?id='.$played_servers_row['id'].'"><h3 class="server">'.$played_servers_row['name'].'</h3></a></div>';
     }
    
?>
</div>


<div class="border">
<h2>Information</h2>
<?php
    $q="select sum(times) from ".$t_prefix."game_server_kills where killer=".$player_row['id'];
    $res=mysqli_query($c,$q);
    $row_kills_total=mysqli_fetch_assoc($res);
    
    $q="select sum(times) from ".$t_prefix."game_server_deaths where victim=".$player_row['id'];
    $res=mysqli_query($c,$q);
    $row_deaths_total=mysqli_fetch_assoc($res);
    
    $q="select * from ".$t_prefix."servers";
    $res=mysqli_query($c,$q);
    $server_row=mysqli_fetch_assoc($res);
    
    $q="select sum(".$t_prefix."killed_by_player.times),".$t_prefix."weapons.tag_path from ".$t_prefix."killed_by_player inner join ".$t_prefix."weapons on ".$t_prefix."killed_by_player.weapon_id=".$t_prefix."weapons.id where ".$t_prefix."killed_by_player.killer=".$player_row['id']." group by weapon_id order by sum(".$t_prefix."killed_by_player.times) desc limit 5";
    //echo $q;
    
    $top_weapon_res=mysqli_query($c,$q);
    
    $q="select sum(".$t_prefix."killed_by_player.times),".$t_prefix."weapons.tag_path from ".$t_prefix."killed_by_player inner join ".$t_prefix."weapons on ".$t_prefix."killed_by_player.weapon_id=".$t_prefix."weapons.id where ".$t_prefix."killed_by_player.victim=".$player_row['id']." group by weapon_id order by sum(".$t_prefix."killed_by_player.times) desc limit 5";
    $top_weapon_death_res=mysqli_query($c,$q);
    
    $q="select ".$t_prefix."players.name,".$t_prefix."players.id,players.ip,sum(".$t_prefix."killed_by_player.times) from ".$t_prefix."killed_by_player inner join ".$t_prefix."players on ".$t_prefix."killed_by_player.victim=".$t_prefix."players.id where ".$t_prefix."killed_by_player.killer=".$player_row['id']." group by ".$t_prefix."killed_by_player.victim order by sum(".$t_prefix."killed_by_player.times) desc limit 5";
    $top_player_kills_res=mysqli_query($c,$q);
    
    $q="select ".$t_prefix."players.name,".$t_prefix."players.id,players.ip,sum(".$t_prefix."killed_by_player.times) from ".$t_prefix."killed_by_player inner join ".$t_prefix."players on ".$t_prefix."killed_by_player.killer=".$t_prefix."players.id where ".$t_prefix."killed_by_player.victim=".$player_row['id']." group by ".$t_prefix."killed_by_player.killer order by sum(".$t_prefix."killed_by_player.times) desc limit 5";
    $top_player_deaths_res=mysqli_query($c,$q);
?>
<div class="player"><a href="kills.php?id=<?php echo $player_row['id']; ?>"><h3 class="server">Total Kills: <?php if (!isset($row_kills_total['sum(times)'])) { echo 0; } else { echo $row_kills_total['sum(times)']; } ?></h3></a></div>
<div class="player"><a href="deaths.php?id=<?php echo $player_row['id']; ?>"><h3 class="server">Total Deaths: <?php if (!isset($row_deaths_total['sum(times)'])) { echo 0; } else { echo $row_deaths_total['sum(times)']; } ?></h3></a></div>

<div class="player"><a href="kdr.php?id=<?php echo $player_row['id']; ?>"><h3 class="server"><h3 class="server">K\D\R: <?php
    if (isset($row_kills_total['sum(times)']) && isset($row_deaths_total['sum(times)'])) {
        $kdr=intval($row_kills_total['sum(times)'])/intval($row_deaths_total['sum(times)']);
        echo number_format($kdr,2, '.','');
    }
?></h3></a></div>
<h2>Kills By:</h2>
<?php
$translate_hitstring=['head' => 'Headshots','body' => 'Body Shots', 'legs' => 'Leg Shots', 'shield generator' => 'Sheild Generator Shots'];
$q="select ".$t_prefix."hitstrings.hitstring,sum(".$t_prefix."killed_by_player.times) as times,".$t_prefix."killed_by_player.killer,hitstring_id from ".$t_prefix."killed_by_player inner join ".$t_prefix."hitstrings on ".$t_prefix."hitstrings.id=".$t_prefix."killed_by_player.hitstring_id where ".$t_prefix."killed_by_player.killer=".$player_row['id']." group by ".$t_prefix."hitstring_id order by hitstring_id asc;";
//echo $q;

$hitstring_res=mysqli_query($c,$q);
    while ($hitstring_row=mysqli_fetch_assoc($hitstring_res)) {
        if (isset($translate_hitstring[$hitstring_row['hitstring']])) {
        ?>
        <div class="player"><a href="hitstring.php?hitstring=<?php echo $hitstring_row['hitstring']; ?>&pid=<?php echo $hitstring_row['killer']; ?>"><h3 class="server"><?php echo $translate_hitstring[$hitstring_row['hitstring']]; ?>: <?php echo $hitstring_row["times"]; ?></h3></a></div>
        <?php
        }
    }
?>

<?php 
$q="select sum(killed_by_player.times) as times,killed_by_player.killer as id from killed_by_player where killed_by_player.killer=".$player_row['id']." and killed_by_player.backtap=1";
//echo $q;
$backtap_res=mysqli_query($c,$q);
$backtap_row=mysqli_fetch_assoc($backtap_res);
    if ($backtap_row!=null) {
    ?>
    <div class="player"><a href="backtap.php?pid=<?php echo $backtap_row['id']; ?>"><h3 class="server">Backtaps: <?php echo $backtap_row['times']; ?></h3></a></div>
    <?php } ?>
    
</div>

<div class="border">
    <h2>Top 5 Most Killed Players</h2>
    <?php
    while ($top_weapon_row=mysqli_fetch_assoc($top_player_kills_res)) {
        
        echo '<div class="player"><a href="viewplayer.php?id='.$top_weapon_row['id'].'"><h3 class="server">'.$top_weapon_row['name'].'  '.obscureIp($top_weapon_row['ip']).' &mdash; '.$top_weapon_row["sum(".$t_prefix."killed_by_player.times)"].' Kills</h3></a></div>';
    }
    ?>
    
</div>

<div class="border">
    <h2>Top 5 Most Deaths From Player</h2>
    <?php
    while ($top_weapon_row=mysqli_fetch_assoc($top_player_deaths_res)) {
        
        echo '<div class="player"><a href="viewplayer.php?id='.$top_weapon_row['id'].'"><h3 class="server">'.$top_weapon_row['name'].'  '.obscureIp($top_weapon_row['ip']).'  &mdash; '.$top_weapon_row["sum(".$t_prefix."killed_by_player.times)"].' Deaths</h3></a></div>';
    }
    ?>
    
</div>

<div class="border">
    <h2>Top 5 Weapons</h2>
    <?php
    while ($top_weapon_row=mysqli_fetch_assoc($top_weapon_res)) {
        
        echo '<div class="player"><h3 class="server">'.translateTagPath($top_weapon_row['tag_path']).' &mdash; '.$top_weapon_row["sum(".$t_prefix."killed_by_player.times)"].' Kills</h3></div>';
    }
    ?>
    
</div>

<div class="border">
    <h2>Top 5 Ways to Die</h2>
    <?php
    while ($top_weapon_row=mysqli_fetch_assoc($top_weapon_death_res)) {
        
        echo '<div class="player"><h3 class="server">'.translateTagPath($top_weapon_row['tag_path']).' &mdash; '.$top_weapon_row["sum(".$t_prefix."killed_by_player.times)"].' Deaths</h3></div>';
    }
    ?>
    
</div>

</body>
</html>
