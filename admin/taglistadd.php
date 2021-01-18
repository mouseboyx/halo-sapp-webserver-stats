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

<section class="admin">
    <form action="serveradd.php" method="post">
    <div class="admin element"><div class="form">Damage Tag List Name: </div><div class="form"><input type="text" id="listname" name="listname" value=""></div> <div class="form description"> An arbitrary name to identify this list.</div></div>
    
    <div class="admin element"><div class="form">Make this list active: </div><div class="form"><input type="checkbox" id="active" name="active" value="1" checked></div> <div class="form description"> If you want this list to be disabled leave unchecked.</div></div>
    
    <div class="admin element"><div class="form">List applies to all servers: </div><div class="form"><input type="checkbox" id="allservers" name="allservers" value="1"></div> <div class="form description"> Check here to make this list apply to all servers including servers added in the future.</div><br><br>
    
    <div class="form">Check servers this should apply to:</div><br>
    <?php
        $q='select * from servers';
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
        $q='select * from maps';
        $res=mysqli_query($c,$q);
        if ($res) {
            while ($row=mysqli_fetch_assoc($res)) {
                echo '<div class="form"><input type="checkbox" class="checkmap" name="map[]" value="'.$row['id'].'"> </div> <div class="form">'.$row['name'].'</div><br>';
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
    </form>
</section>
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
