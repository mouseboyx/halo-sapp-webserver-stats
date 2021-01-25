<html>
<head>
<?php 
 include 'sitehead.php';
 include 'functions.php';
	    $rows_done=[];

?>
<title>Halo Stats</title>
</head>
<body>
<div id="container">
    <?php include 'header.php'; ?>
    <div class="servers">
        <h1 class="servers">Servers</h1>
        <?php
            include 'connect.php';
            include 'tablePrefix.php';
            $q="select servers.id,servers.name,server_settings.description from ".$t_prefix."servers inner join ".$t_prefix."server_settings on ".$t_prefix."servers.id=".$t_prefix."server_settings.server_id";
            $res=mysqli_query($c,$q);
            while ($row=mysqli_fetch_assoc($res)) {
                ?>
                <div class="server"><a href="viewserver.php?id=<?php echo $row['id']; ?>"><h3 class="server"><?php echo $row['name']; ?> &mdash; <?php echo $row['description']; ?></h3></a></div>
                <?php
            }
            
        ?>
    </div>
    <br>
    <div class="border">
        <div class="player"><a href="players.php" class="servers"><h1 class="servers">Players (Click Here to Search)</h1></a></div>
        <div class="border recent">
        <h2> &mdash; Recent &mdash;</h2>
        <?php
            include 'connect.php';
            include 'tablePrefix.php';
            $q="select ".$t_prefix."players.ip as players_ip,".$t_prefix."players.id as players_id,".$t_prefix."players.name as players_name,".$t_prefix."killed_by.times as times,".$t_prefix."servers.name as server_name,".$t_prefix."killed_by.game_id as killed_by_game_id from ".$t_prefix."killed_by inner join ".$t_prefix."players on ".$t_prefix."players.id=".$t_prefix."killed_by.killer inner join ".$t_prefix."servers on ".$t_prefix."killed_by.server_id=".$t_prefix."servers.id order by ".$t_prefix."killed_by.killer desc limit 1024";
            
            $res=mysqli_query($c,$q);
            $counter=0;

            while ($row=mysqli_fetch_assoc($res)) {
                $q="select sum(times) from ".$t_prefix."killed_by where game_id=".$row['killed_by_game_id']." and killer=".$row['players_id'];
                $res2=mysqli_query($c,$q);
                $row2=mysqli_fetch_assoc($res2);
                $times=$row2['sum(times)'];
                $skip=false;
                foreach ($rows_done as $pid) {
                    if ($pid==$row['players_id']) {
                //		echo $row['players_id'].' '.$pid.'<br>';

                        $skip=true;
                        break;
                    } else {
                        $skip=false;
                    }
                }
                if ($counter>=5) {
                    break;
                }
                if ($skip==false) {
                        ?>
                        <div class="player"><a href="viewplayer.php?id=<?php echo $row['players_id']; ?>"><h3 class="server">Name: <?php echo $row['players_name'].' '.obscureIp($row['players_ip']); ?> &mdash; Kills: <?php echo $times; ?>
                        &mdash; Server: <?php echo $row['server_name']; ?></h3></a></div>
                        <?php
                array_push($rows_done,$row['players_id']);
                $counter++;
                }
	

            }
            
        ?>
        </div>
        
        <div class="border recent">
        <h2> &mdash; Most Kills Across All Servers &mdash;</h2>
        <?php
            include 'connect.php';
            include 'tablePrefix.php';
            $q="select ".$t_prefix."players.ip,sum(".$t_prefix."game_server_kills.times),".$t_prefix."players.name,".$t_prefix."players.id from ".$t_prefix."game_server_kills inner join ".$t_prefix."players on ".$t_prefix."players.id=".$t_prefix."game_server_kills.killer group by ".$t_prefix."game_server_kills.killer order by sum(".$t_prefix."game_server_kills.times) desc limit 5";
            
            $res=mysqli_query($c,$q);
            $counter=0;

            while ($row=mysqli_fetch_assoc($res)) {
                
                
                        ?>
                        <div class="player"><a href="viewplayer.php?id=<?php echo $row['id']; ?>"><h3 class="server">Name: <?php echo $row['name'].' '.obscureIp($row['ip']); ?> &mdash; Kills: <?php echo $row["sum(".$t_prefix."game_server_kills.times)"]; ?></h3></a></div>
                        <?php
                
                
	

            }
            
        ?>
        </div>
        
        <div class="border recent">
        <h2> &mdash; Most Deaths Across All Servers &mdash;</h2>
        <?php
            include 'connect.php';
            include 'tablePrefix.php';
            $q="select ".$t_prefix."players.ip,sum(".$t_prefix."game_server_deaths.times),".$t_prefix."players.name,".$t_prefix."players.id from ".$t_prefix."game_server_deaths inner join ".$t_prefix."players on ".$t_prefix."players.id=".$t_prefix."game_server_deaths.victim group by ".$t_prefix."game_server_deaths.victim order by sum(".$t_prefix."game_server_deaths.times) desc limit 5";
            
            $res=mysqli_query($c,$q);
            $counter=0;

            while ($row=mysqli_fetch_assoc($res)) {
                
                
                        ?>
                        <div class="player"><a href="viewplayer.php?id=<?php echo $row['id']; ?>"><h3 class="server">Name: <?php echo $row['name'].' '.obscureIp($row['ip']); ?> &mdash; Deaths: <?php echo $row["sum(".$t_prefix."game_server_deaths.times)"]; ?></h3></a></div>
                        <?php
                
                
	

            }
            
//
        ?>
        </div>
        
        <div class="border recent">
        <h2> &mdash; Best K/D/R Across All Servers &mdash;</h2>
        <?php
            include 'connect.php';
            include 'tablePrefix.php';
            $q="select ".$t_prefix."players.ip,sum(".$t_prefix."game_server_deaths.times),sum(".$t_prefix."game_server_kills.times),".$t_prefix."players.name,".$t_prefix."players.id from ".$t_prefix."game_server_kills inner join ".$t_prefix."game_server_deaths on ".$t_prefix."game_server_deaths.victim=".$t_prefix."game_server_kills.killer inner join ".$t_prefix."players on ".$t_prefix."game_server_kills.killer=".$t_prefix."players.id group by ".$t_prefix."game_server_kills.killer";
            
            $res=mysqli_query($c,$q);
            $kd_array=[];
            while ($row=mysqli_fetch_assoc($res)) {
                $tmp=['id' => $row['id'],'name' => $row['name'], 'kd' => intval($row["sum(".$t_prefix."game_server_kills.times)"])/intval($row["sum(".$t_prefix."game_server_deaths.times)"]), 'ip' => $row['ip']];
                array_push($kd_array,$tmp);
            }
            usort($kd_array, "cmp");
            $counter=0;
            //while ($row=mysqli_fetch_assoc($res)) {
              foreach ($kd_array as $arr) {  
                        $name=$arr['name'];
                        $id=$arr['id'];
                        $kd=$arr['kd'];
                        $ip=$arr['ip'];
                        ?>
                        <div class="player"><a href="viewplayer.php?id=<?php echo $id; ?>"><h3 class="server">Name: <?php echo $name.' '.obscureIp($ip); ?> &mdash; K/D/R: <?php echo number_format($kd,2, '.',''); ?></h3></a></div>
                        <?php
                
                
                $counter++;
                if ($counter>5) {
                    break;
                }

            }
            
        ?>
        </div>
        
    </div>
</div>
</body>
</html>
<?php
function cmp($a, $b)
{
    if ($a['kd'] == $b['kd']) {
        return 0;
    }
    return ($a['kd'] < $b['kd']) ? 1 : -1;
}
?>
