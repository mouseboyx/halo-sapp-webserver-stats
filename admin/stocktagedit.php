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
?>
<html>
<head>
<title>Stock Tag Edit</title>
<?php include '../head.php'; ?>
</head>
<body>
<div class="container">
<h1 class="admin"><span class="lightblue">Stock</span> Tag Edit</h1>

        <?php include 'adminmenu.php' ?>

<section class="admin">
<div class="form description">When a player takes damage in stock halo maps these are the possible causes of damage.  When a player dies the last known source of damage is sent to the database in the form of a tag path, and is the cause of death.  For example if a player kills another player with the Assualt Rifle by shooting them, then the tag path sent to the database is "weapons\assault rifle\bullet".  This page is a translation between tag path and a human readable description.  It's easier to read the cause of death as "Assault Rifle" rather than "weapons\assault rifle\bullet" to someone who queries the database.  Each line is a single translation: tag path to the left of the comma, and human readable name to the right.
</div>
<form action="stocktagedit.php" method="post">
<textarea style="width:100%;" rows="40" name="tags">
<?php
            
                   include '../connect.php';
                   if (isset($_GET['default']) && $_GET['default']==1) {
                        $q='delete from stock_damage_tags';
                        $res=mysqli_query($c,$q);
                        include '../damage.php';
                        foreach ($stock_halo_damage_tags as $tag => $name) {
                            $tag_escape=mysqli_real_escape_string($c,$tag);
                            $name_escape=mysqli_real_escape_string($c,$name);
                            $q="insert into stock_damage_tags (tag_path,shown_as) values ('".$tag_escape."','".$name_escape."')";
                            $res=mysqli_query($c,$q);
                            
                        }
                        
                   }
                   if (isset($_POST['tags']) && $_POST['tags']!='') {
                        $q='delete from stock_damage_tags';
                        $res=mysqli_query($c,$q);
                        $lines=explode("\n",$_POST['tags']);
                        foreach ($lines as $line) {
                            if ($line!='') {
                            $data=explode(',',$line);
                            
                            $tag_path=mysqli_real_escape_string($c,$data[0]);
                            $shown_as=mysqli_real_escape_string($c,$data[1]);
                            $q="insert into stock_damage_tags (tag_path,shown_as) values ('".$tag_path."','".$shown_as."')";
                            $res=mysqli_query($c,$q);
                            }
                        }
                   }
                   $q='select tag_path,shown_as from stock_damage_tags';
                   $res=mysqli_query($c,$q);
                   if ($res) {
                        while ($row=mysqli_fetch_assoc($res)) {
                            echo $row['tag_path'].','.$row['shown_as']."\n";
                        }
                   } else {
                        echo 'Error';
                   }
            
            
            
?>
</textarea>
        <?php
                        if (isset($_GET['default']) && $_GET['default']==1) { 
                        ?>
                        <script>
                            window.location.href='stocktagedit.php';
                        </script>
                        <?php
                        }
  
?>
<div class="form description">
These translations automatically apply to all maps, including custom maps, unless <a href="tagedit.php">overriden</a>.
<br><br>
Note: If projectile replacement is being done by a script or by a map mod, this may not be accurate to the weapon.  For example if the sniper is modified to shoot tank shells, this will say that they were killed by a tank shell, even if the sniper is the weapon that shot the projectile.  You can override this behavior based on the server and what map is being played here <a href="tagedit.php">Add/Edit <span class="pink">Custom</span> Damage Tags</a>.
</div>
<input type="submit" style="font-size:2em;margin:0 auto;display:block;margin-top:2em;" value="Update">
</form>
<input type="button" value="Load Defaults" id="default">
<div id="confirm"></div>
</section>
<?php
?>
</div>
<script>
d=document.getElementById('default');
added=false
confirm=document.getElementById('confirm');
d.addEventListener('click',function () {
    if (added==false) {
    div=document.createElement('div');
    mess=document.createElement('span');
    input=document.createElement('input');
    mess.innerHTML='Are you sure you want to load defaults? ';
    input.setAttribute('type','button');
    input.setAttribute('value','Confirm');
    input.addEventListener('click',function () {
        window.location.href='?default=1';
    });
    div.appendChild(mess);
    div.appendChild(input);
    confirm.appendChild(div);
    added=true;
    }
    
});
</script>
</body>
</html>
<?php
}
?>
