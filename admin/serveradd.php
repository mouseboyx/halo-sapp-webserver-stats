<html>
<head>
<title>Add New Server</title>
<?php include '../head.php'; ?>
</head>
<body>
<div class="container">
<h1 class="admin">Add New Halo Server</h1>
<div class="admin">
        <?php include 'adminmenu.php' ?>
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
                    if (isset($_POST['servername']) && isset($_POST['serverip']) && isset($_POST['serverkey']) && isset($_POST['description'])) {
                            include '../tablePrefix.php';
                            
                            if (preg_match('/(^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$)/',$_POST['serverip'])) {
                                if (strlen($_POST['servername'])>70) {
                                    echo 'Server name can not be more than 70 characters <a href="serveradd.php">Return</a>';
                                } else {
                                    if (strlen($_POST['description'])>300) {
                                        echo 'Server description can not be more than 300 characters <a href="serveradd.php">Return</a>';
                                    } else {
                                        if (strlen($_POST['serverkey']>255)) {
                                            echo 'Server key can not be more than 300 characters <a href="serveradd.php">Return</a>';
                                        } else {
                                            include '../connect.php';
                                            $servername=mysqli_real_escape_string($c,strip_tags($_POST['servername']));
                                            $serverip=mysqli_real_escape_string($c,$_POST['serverip']);
                                            $serverkey=mysqli_real_escape_string($c,$_POST['serverkey']);
                                            $description=mysqli_real_escape_string($c,strip_tags($_POST['description']));
                                            $q="select * from ".$t_prefix."servers where request_key='".$serverkey."' or name='".$servername."' or ip='".$serverip."'";
                                            $res=mysqli_query($c,$q);
                                            $row = mysqli_fetch_assoc($res);
                                            if ($row!=null) {
                                                echo 'A server with this key, name, or ip address already exists.';
                                            } else {
                                                $q="insert into ".$t_prefix."servers (name,request_key,ip) values ('".$servername."','".$serverkey."','".$serverip."')";
                                                $res=mysqli_query($c,$q);
                                                if ($res) {
                                                    $q="select id from ".$t_prefix."servers where request_key='".$serverkey."' and name='".$servername."' and ip='".$serverip."'";
                                                   // echo '" '.$q.' "';
                                                    $res=mysqli_query($c,$q);
                                                    $row = mysqli_fetch_assoc($res);
                                                    if ($row!=null) {
                                                        $new_id=$row['id'];
                                                        if (isset($_POST['requireip'])) {
                                                            $q="insert into ".$t_prefix."server_settings (server_id,description,check_ip) values (".$new_id.",'".$description."',1)";
                                                        } else {
                                                            $q="insert into ".$t_prefix."server_settings (server_id,description,check_ip) values (".$new_id.",'".$description."',0)";
                                                        }
                                                        $res=mysqli_query($c,$q);
                                                        if ($res) {
                                                            echo 'Halo Server Successfuly added <a href="index.php">Return to admin</a> --- <a href="serveradd.php">Add another server</a>';
                                                        }
                                                    } else {
                                                        echo 'A database error occured.';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                echo 'Server ip is not a valid ipv4 address <a href="serveradd.php">Return</a>';
                            }
                    } else {
                    ?>
                    
                    <section class="admin">
                        <form action="serveradd.php" method="post">
                        <div class="admin element"><div class="form">Server name: </div><div class="form"><input type="text" id="servername" name="servername" value=""></div> <div class="form description"> This can be anything, at least one character, it will show up when searching for stats.  Best to make it the same as the server name in the lobby. Max 70 characters.</div></div>
                        
                        <div class="admin element"><div class="form">Server ip address: </div><div class="form"><input type="text" id="serverip" name="serverip" value=""></div> <div class="form description"> It should be the ip address of the halo server.</div></div>
                        
                        <div class="admin element"><div class="form">Require this ip address: </div><div class="form"><input type="checkbox" id="requireip" name="requireip" checked></div> <div class="form description"> If you are having trouble making requests you can uncheck this.  Leaving unchecked makes it so anyone with the key can make requests in this servers name.</div></div>
                        
                        <div class="admin element"><div class="form">Server key: </div><div class="form"><input type="text" id="serverkey" name="serverkey" value="" size="66"></div> <div class="form description"> This key was auto-generated with JavaScript at random, so it could possibly contain offensive words.  You can use your own key if you wish. This is the key that needs to be placed in the lua script for logging to work.  Max 255 characters.  </div></div>
                        
                        <div class="admin element"><div class="form">Server description: </div><textarea rows="10" style="width:100%" name="description"></textarea> <div class="form description"> Describe this server. When people view the server page this will show up.  Max 300 characters.</div></div>
                        <div>
                        <input type="submit" value="Add Server" style="font-size:2em;margin:0 auto;display:block;margin-top:2em;">
                        </div>
                        </form>
                    </section>
                    <?php
                    }
            }
?>
        <script>
            serverkey=document.getElementById('serverkey');
            var array = new Uint32Array(64);
            window.crypto.getRandomValues(array);
            instring='abcdefghijklmnopqrstuvwxyz1234567890'
            result=''
            for (i=0;i<array.length;i++) {
                
                result+=instring[Math.floor((array[i]/4294967295)*instring.length)]
                
            }
            serverkey.value=result;
        </script>
</div>
</body>
</html>
