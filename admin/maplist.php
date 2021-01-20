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
?>
<html>
<head>
<title>Map List</title>
<?php include '../head.php'; ?>
</head>
<body>
<div class="container">
<h1 class="admin">Map List</h1>
        
        <?php include 'adminmenu.php' ?>
        <section class="admin">
        <form action="mapmanage.php" method="post">
        <div class="admin element"><div class="form">Add Map To List: </div><div class="form"><input type="text" id="newmap" name="newmap" value="" placeholder="Example: bloodgulch"><input type="submit" value="Add Map"></div> <div class="form description"> New maps are added automatically when a tracked server starts a new game with a map not in this list.  You can add them ahead of time with this.  This must be the same as the $map variable returned by sapp which is usually the filename.  Run /say * $map on the server running the map to find the name.</div>
        </form>
        <hr>
        <h3>Current Maps in the List</h3>
        <form action="mapmanage.php" method="post">
        <?php 
            $q="select * from ".$t_prefix."maps";
            $res=mysqli_query($c,$q);
            while ($row=mysqli_fetch_assoc($res)) {
                echo '<div class="admin server" style="padding:0;"><input type="checkbox" name="delmap[]" value="'.$row['id'].'">'.$row['name'].'</div>';
            }
        ?>
        <br>
        <input type="submit" value="Delete Selected Maps">
        <div class="form description">If a server starts a new game with a map deleted here it will be inserted into this list, but any settings previously applied will not be retained (Damage tag translation options will not apply to deleted maps)</div>
        </form>
        <hr>
        <h3>Restore Defaults</h3>
        <form action="mapmanage.php" method="post">
        <input type="hidden" value="1" name="default">
        <input type="button" value="Restore Default" id="restoredefault">
        <div id="confirm" style="display:none;"></div>
        <br>
        <div class="form description">This will return the map list to ONLY the maps that come with a stock Halo installation.</div>
        </form>
        </section>
        </form>
</div>
<script>
restoredefault=document.getElementById('restoredefault');
confirm=document.getElementById('confirm');
restoredefault.addEventListener('click',function () {
span=document.createElement('span')
span.innerHTML='Are you sure you want to restore default maps? ';
span.style.color='#f00';
input=document.createElement('input');
input.setAttribute('type','submit');
input.setAttribute('value','Confrim');
confirm.appendChild(span);
confirm.appendChild(input);
confirm.style.display="inline-block"
});
</script>
</body>
</html>
<?php
}
?>
