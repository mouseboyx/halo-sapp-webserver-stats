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

<span class="admin menu"><a href="javascript:void(0)" id="showhelp">Show Help</a></span>
<div class="form description" id="help">
<h3>General</h3>
This can be left unconfigured.  It's main goal is to make reports of data less misleading and more understandable.  Below is how to do that and why you might want to.
<br><br>
When a player takes damage and dies in halo, a tag path is sent to the database to determine cause of death.  For example if a player kills another player with the Assualt Rifle by shooting them, then the tag path sent to the database is "weapons\assault rifle\bullet".  By default this is translated to "Assault Rifle" when someone queries the database.  Here you can override the stock damage tag paths based on the server, and the map loaded on the server.  You can add custom map names to apply to tag paths, and apply it to servers.  
<br><br>

The reason you would want this configured, is if a tag path created on death is in the database, but a name for the weapon cannot be found for it here, then the database falls back on the <a href="stocktagedit.php">stock</a> translations.  If the stock translations fail then the database falls back by using everything after the second to last slash.  For example "weapons\assault rifle\bullet" would show up as "assault rifle\bullet", which is not a big deal, but it makes it harder for humans to read and understand. In addition it may not be accurate to what weapon or method killed someone.  If a tag path has been kept the same on a custom map but it's a completely different weapon.  For example if "weapons\pistol\bullet" was actually a custom rocket launcher explosion that had nothing to do with the pistol it could be misleading in any case.
<br><br>
This handy script <a href="../save_damage_tags.lua">save_damage_tags.lua</a> will save all damage tags for a map to a file located in the sapp folder in the Halo Server root directory.  Usually C:\Program Files (x86)\Microsoft Games\Halo Server\sapp\damage_tags.txt. It does not save the weapon associated with the damage tag however. But this script is also interactive, it will save all chat messages and any damage and cause of death tag paths while in game.  The chat messages are logged in damage_tags.txt to give a way to take notes on weapon names.  It's best to use the interactive feature with a non public server with 2 players in the game to keep track of all of weapon names and associated damage.  Some weapons have different causes of damage, like the needler if you shoot one needle into a player the damage is different than the super detonation explosion caused by many needles.  For any weapon all of the possible damages must be taken into account. This script is in no way required for the main feature of logging kill death information on the web server database.  It's only for figuring out damage tag paths and associated weapon names for custom maps or stock map mods.

<br><br>
<h3>Map List</h3>
The map list is a pool of maps that a damage tag list can apply to, this is the map name that sapp returns, it is usually the file name like bloodgulch or putput without .map at the end.  The default maps are already included in the map list. If you are unsure of what a map name is login to the server running the map with admin privileges and run &nbsp; /say * $map &nbsp; This will be the correct map name.  You can select all or any map for a tag list to apply to.  If a server starts a new game with a map that is not in this list, the new map name will be added to this list automatically.
<h3>Damage Tag Lists</h3>
A new list can be created that will work as a resource that can be changed for the use case.  The damage tag lists can apply only to specific map(s) regardless of the server.  Or the damage tag lists can apply to specific map(s) on specific server(s).  Or the tag lists can apply to specific server(s) regardless of map.
<br><br>



</section>
<section class="admin">
            <span class="admin menu"><a href="taglistadd.php" style="text-decoration:none;">+ Add New Damage Tag List +</a></span>  <span class="admin menu"><a href="taglistadd.php" style="text-decoration:none;">+ Add New Map +</a></span>
            </section>
</div>
<script>
show=false
showhelp=document.getElementById('showhelp')
help=document.getElementById('help');
help.style.display='none';
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
