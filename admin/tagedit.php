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
<title>Custom Tag Edit</title>
<?php include '../head.php'; ?>
</head>
<body>
<div class="container">
<h1 class="admin"><span class="pink">Custom</span> Tag Edit</h1>

        <?php include 'adminmenu.php' ?>

<section class="admin">
<a href="javascript:void(0)" id="showhelp">Show Help</a>
<div class="form description" id="help" style="display:none;">When a player takes damage and dies in halo, a tag path is sent to the database to determine cause of death.  For example if a player kills another player with the Assualt Rifle by shooting them, then the tag path sent to the database is "weapons\assault rifle\bullet".  By default this is translated to "Assault Rifle" when someone queries the database.  Here you can override the stock damage tag paths based on the server, and the map loaded on the server.  You can add custom map names to apply to tag paths, and apply it to servers that have been added in the administration console.  
<br><br>
This handy script <a href="../save_damage_tags.lua">save_damage_tags.lua</a> will save all damage tags for a map to a file located in the sapp folder in the Halo Server root directory.  Usually C:\Program Files (x86)\Microsoft Games\Halo Server\sapp\damage_tags.txt.  This script is also interactive, it will save all chat messages and any damage and cause of death tag paths while in game.  The chat messages are logged in damage_tags.txt to give a way to take notes on weapon names.  It's best to use the interactive feature with a non public server with 2 players in the game to keep track of all of weapon names and associated damage.  Some weapons have different causes of damage, like the needler if you shoot one needle into a player the damage is different than the super detonation explosion caused by many needles.  For any weapon all of the possible damages must be taken into account. This script is in no way required for the main feature of logging kill death information on the web server database.  It's only for figuring out damage tag paths and associated weapon names for custom maps or stock map mods.
<br><br>
The reason you would want this configured, is if a tag path created on death is in the database, but a name for the weapon cannot be found for it here, then the database falls back on the stock translations.  If the stock translations fail then the database falls back by using everything after the second to last slash.  For example "weapons\assault rifle\bullet" would show up as "assault rifle\bullet", which is not a big deal, but it makes it harder for humans to read and understand. In addition it may not be accurate to what weapon or method killed someone.  If a tag path has been kept the same on a custom map but it's a completely different weapon.  For example if "weapons\pistol\bullet" was actually a custom rocket launcher explosion that had nothing to do with the pistol it could be misleading in any case.
</section>
</div>
<script>
show=false
showhelp=document.getElementById('showhelp')
help=document.getElementById('help');
showhelp.addEventListener('click',function () {
    if (show==false) {
        help.style.display='block';
        showhelp.innerHTML='Hide Help';
       
    }
    if (show==true) {
        help.style.display='none';
        showhelp.innerHTML='Show Help';
      
    }
    if (show==true) {
        show=false
    } else {
        show=true
    }
    
});
</script>
</body>
</html>
<?php
}
?>
