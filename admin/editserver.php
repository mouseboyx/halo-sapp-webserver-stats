<html>
<head>
<title>Edit Server</title>
<?php include '../head.php'; ?>
</head>
<body>
<div class="container">
<h1 class="admin">Edit Halo Server</h1>
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
                if (isset($_POST['id'])) {
                    include '../connect.php';
                    $server_id=mysqli_real_escape_string($c,strip_tags($_POST['id']));
                    //echo $_POST['servername'];
                    //echo $_POST['serverip'];
                    //echo $_POST['serverkey'];
                    //echo $_POST['description'];
                    if (isset($_POST['servername']) && isset($_POST['serverip']) && isset($_POST['description'])) {
                        if (preg_match('/(^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$)/',$_POST['serverip'])) {
                             if (strlen($_POST['servername'])>70) {
                                    echo 'Server name can not be more than 70 characters <form method="post" action="editserver.php"><input type="hidden" name="id" value="'.$server_id.'"><input style="font-size:1.5em;" type="submit" value="Return"></form>';
                                } else {
                                    if (strlen($_POST['description'])>300) {
                                        echo 'Server description can not be more than 300 characters <form method="post" action="editserver.php"><input type="hidden" name="id" value="'.$server_id.'"><input style="font-size:1.5em;" type="submit" value="Return"></form>';
                                    } else {
                                        if (isset($_POST['serverkey']) && strlen($_POST['serverkey']>255)) {
                                            echo 'Server key can not be more than 300 characters <form method="post" action="editserver.php"><input type="hidden" name="id" value="'.$server_id.'"><input style="font-size:1.5em;" type="submit" value="Return"></form>';
                                        } else {
                                            $servername=mysqli_real_escape_string($c,strip_tags($_POST['servername']));
                                            $serverip=mysqli_real_escape_string($c,$_POST['serverip']);
                                            if (isset($_POST['serverkey'])) {
                                            $serverkey=mysqli_real_escape_string($c,$_POST['serverkey']);
                                            }
                                            $description=mysqli_real_escape_string($c,strip_tags($_POST['description']));
                                            if (isset($_POST['serverkey'])) {
                                                $q="select * from servers where (request_key='".$serverkey."' or name='".$servername."' or ip='".$serverip."') and id<>".$server_id;
                                            } else {
                                                $q="select * from servers where (name='".$servername."' or ip='".$serverip."') and id<>".$server_id;
                                            }
                                            $res=mysqli_query($c,$q);
                                            $row = mysqli_fetch_assoc($res);
                                            if ($row!=null) {
                                                echo 'A server with this key, name, or ip address already exists. <form method="post" action="editserver.php"><input type="hidden" name="id" value="'.$server_id.'"><input style="font-size:1.5em;" type="submit" value="Return"></form>';
                                            } else {
                                                if (isset($_POST['requireip'])) {
                                                    $q="update server_settings set check_ip=1,description='".$description."' where server_id=".$server_id;
                                                } else {
                                                    $q="update server_settings set check_ip=0,description='".$description."' where server_id=".$server_id;
                                                }
                                                $res=mysqli_query($c,$q);
                                                if ($res) {
                                                    if (isset($_POST['serverkey'])) {
                                                        $q="update servers set name='".$servername."',request_key='".$serverkey."',ip='".$serverip."' where id='".$server_id."'";
                                                    } else {
                                                        $q="update servers set name='".$servername."',ip='".$serverip."' where id='".$server_id."'";
                                                    }
                                                    $res=mysqli_query($c,$q);
                                                    if ($res) {
                                                        echo 'Halo Server Successfuly edited <a href="index.php">Return to admin</a>';
                                                    } else {
                                                        echo 'Database error';
                                                    }
                                                } else {
                                                    echo 'Database error';
                                                }
                                            }
                                        }
                                    }
                                }
                        } else {
                            echo 'Server ip is not a valid ipv4 address <form method="post" action="editserver.php"><input type="hidden" name="id" value="'.$server_id.'"><input style="font-size:1.5em;" type="submit" value="Return"></form>';
                        }
                    } else {
                        //echo $server_id;
                        $q="select servers.name, server_settings.description,servers.ip,servers.request_key,server_settings.check_ip,servers.id from servers inner join server_settings on servers.id=server_settings.server_id where servers.id='".$server_id."' and server_settings.server_id='".$server_id."'";
                        $res=mysqli_query($c,$q);
                        $row=mysqli_fetch_assoc($res);
                        ?>
                        <h2 class="admin">Editing <?php echo $row['name']; ?></h2>
                        <section class="admin">
                            <form action="editserver.php" method="post">
                            <div class="admin element"><div class="form">Server name: </div><div class="form"><input type="text" id="servername" name="servername" value="<?php echo $row['name']; ?>"></div> <div class="form description"> This can be anything, at least one character, it will show up when searching for stats.  Best to make it the same as the server name in the lobby. Max 70 characters.</div></div>
                            
                            <div class="admin element"><div class="form">Server ip address: </div><div class="form"><input type="text" id="serverip" name="serverip" value="<?php echo $row['ip']; ?>"></div> <div class="form description"> It should be the ip address of the halo server.</div></div>
                            
                            <div class="admin element"><div class="form">Require this ip address: </div><div class="form"><input type="checkbox" id="requireip" name="requireip" <?php if ($row['check_ip']==1) {echo 'checked';} ?>></div> <div class="form description"> If you are having trouble making requests you can uncheck this.  Leaving unchecked makes it so anyone with the key can make requests in this servers name.</div></div>
                            
                            <div class="admin element"><div class="form">Server key: </div><div class="form"><input type="text" id="serverkey" name="serverkey" value="<?php echo $row['request_key']; ?>" size="66" disabled><input type="button" id="edit" value="edit key"></div> <div class="form description"> This key was auto-generated with JavaScript, but you can use your own if you wish. This is the key that needs to be placed in the lua script for logging to work.  Max 255 characters.  </div></div>
                            
                            <div class="admin element"><div class="form">Server description: </div><textarea rows="10" style="width:100%" name="description"><?php echo $row['description']; ?></textarea> <div class="form description"> Describe this server. When people view the server page this will show up.  Max 300 characters.</div></div>
                            <div>
                            <input type="submit" value="Edit Server" style="font-size:2em;margin:0 auto;display:block;margin-top:2em;">
                            <input type="hidden" name="id" value="<?php echo $server_id; ?>">
                            </div>
                            </form>
                        </section>
                        <script>
                            edit=document.getElementById('edit');
                            serverkey=document.getElementById('serverkey');
                            edit.addEventListener('click',function () {
                                if (serverkey.disabled==true) {
                                    serverkey.disabled=false;
                                }
                            });
                        </script>
                        <?php
                    }
                }
            }
?>
</body>
</html>
