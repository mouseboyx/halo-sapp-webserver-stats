<html>
<head>
<?php 
 include 'sitehead.php';
 include 'functions.php';
	    $rows_done=[];

?>
</head>
<?php include 'header.php'; ?>
<div class="border">
<h1  class="servers"><form method="get" action="players.php">Search For Player

<input class="search" type="text" name="q" id="search"><input type="submit" value="Search" class="searchbutton"></form></h1>

<?php
    if (isset($_GET['q']) && $_GET['q']!='') {
        include 'connect.php';
        include 'tablePrefix.php';
        $query=str_replace('%','',$_GET['q']);
        $query=str_replace('_','',$query);
        $query=mysqli_real_escape_string($c,$query);
        $q="select * from players where name like '%".$query."%'";
        $res=mysqli_query($c,$q);
        $name_ip_arr=[];
        while ($row=mysqli_fetch_assoc($res)) {
            $skip=false;
            foreach ($name_ip_arr as $values) {
                if ($values['ip']==$row['ip'] && $values['name']==$row['name']) {
                    $skip=true;
                }
            }
            if ($skip==false) {
            echo '<div class="player"><a href="viewplayer.php?id='.$row['id'].'"><h3 class="server">'.$row['name'].' '.obscureIp($row['ip']).'</h3></a></div>';
            $tmp=['name' => $row['name'],'ip' => $row['ip']];
            array_push($name_ip_arr,$tmp);
            }
        }
    }
?>
</div>
<body>
</body>
</html>
