<html>
<body>
<?php
$counter=0;
$alpha='abcdefghijklmnopqrstuvwxyz0123456789';
$killers=[];
$killer_names=[];
$killer_ips=[];

$victims=[];
$victim_names=[];
$victim_ips=[];
$weapons=[];
$body_parts=['head','body','legs','other'];
print_r($body_parts);
for ($i=0;$i<40;$i++) {
    $string='';
    for ($j=0;$j<20;$j++) {
        $string.=$alpha[rand(0,(strlen($alpha)-1))];
    }
    $weapons[$i]=$string;
}
$killer_ip=rand(0,255).".".rand(0,255).".".rand(0,255).".".rand(0,255);
$victim_ip=rand(0,255).".".rand(0,255).".".rand(0,255).".".rand(0,255);

    $killer_name='';
    for ($j=0;$j<(rand(3,10));$j++) {
        $killer_name.=$alpha[rand(0,(strlen($alpha)-1))];
    }
    
    
    $victim_name='';
    for ($j=0;$j<(rand(3,10));$j++) {
        $victim_name.=$alpha[rand(0,(strlen($alpha)-1))];
    }
  
    $u=count($killer_names);
        
        $killer_names[$u]=$killer_name;
        $killer_ips[$u]=$killer_ip;
        
    $u=count($victim_names);
        
        $victim_names[$u]=$victim_name;
        $victim_ips[$u]=$victim_ip;
for ($g=0;$g<50000000;$g++) {
    for ($z=0;$z<100;$z++) {
        for ($i=1;$i<3;$i++) {
            $killer_ip=rand(0,255).".".rand(0,255).".".rand(0,255).".".rand(0,255);
            $victim_ip=rand(0,255).".".rand(0,255).".".rand(0,255).".".rand(0,255);

                $killer_name='';
                for ($j=0;$j<(rand(3,10));$j++) {
                    $killer_name.=$alpha[rand(0,(strlen($alpha)-1))];
                }
                
                
                $victim_name='';
                for ($j=0;$j<(rand(3,10));$j++) {
                    $victim_name.=$alpha[rand(0,(strlen($alpha)-1))];
                }
            
                $u=count($killer_names);
                    
                    $killer_names[$u]=$killer_name;
                    $killer_ips[$u]=$killer_ip;
                    
                $u=count($victim_names);
        
            $victim_names[$u]=$victim_name;
            $victim_ips[$u]=$victim_ip;
            
            $killer_index=rand(0,count($killer_names)-1);
            $victim_index=rand(0,count($victim_names)-1);
            
            if (rand(0,100)<1) {
                $backtap=1;
            } else {
                $backtap=0;
            }
            $q="http://localhost/halo/halo.php?key=".($i)."&killer=".$killer_names[$killer_index]."&victim=".$victim_names[$victim_index]."&killer_ip=".$killer_ips[$killer_index]."&victim_ip=".$victim_ips[$victim_index]."&body_part=".$body_parts[rand(0,count($body_parts)-1)]."&killed_by_weapon=".$weapons[rand(0,count($weapons)-1)]."&backtap=".$backtap;
            file_get_contents($q);
        }

    }
    //isset($_GET['newgame']) && $_GET['newgame']==1 && isset($_GET['map']) && isset($_GET['mode']) && isset($_GET['type']) && isset($_GET['key'])
    file_get_contents("http://localhost/halo/game.php?newgame=1&map=bloodgulch&mode=asdf&type=ctf&key=1");
    file_get_contents("http://localhost/halo/game.php?newgame=1&map=bloodgulch&mode=asdf&type=ctf&key=2");
}

?>
</body>
</html>
