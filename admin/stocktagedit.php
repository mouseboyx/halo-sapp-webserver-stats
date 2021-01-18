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
<div class="form description">When submitted this will replace the list of human readable names only for the maps that come with Halo.<br>In the format : tag\path\to\jpt\damage\item,Human Readable Name &nbsp; One per line.
<br>
You can add custom tags that override these settings using <a href="tagedit.php">Add/Edit <span class="pink">Custom</span> Damage Tags</a>.
</div>
<form action="stocktagedit.php" method="post">
<textarea style="width:100%;" rows="40" name="tags">
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
  }
?>
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
