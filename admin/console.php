<?php
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']==true && $_SESSION['admin']==1) {
    include '../connect.php';
    ?>
        <h1 class="admin">Administration Home for User: <?php echo $_SESSION['name']; ?></h1>
        <div class="admin">
        <h2 class="admin">Servers</h2>
            <section class="admin">
            <a href="serveradd.php" style="text-decoration:none;">+ Add New Server +</a>
            </section>
            <?php
                $q='select servers.name, server_settings.description,servers.ip,servers.request_key,server_settings.check_ip,servers.id from servers inner join server_settings on servers.id=server_settings.server_id';
                $res=mysqli_query($c,$q);
                while ($row=mysqli_fetch_assoc($res)) {
                    echo '<section class="admin server" style="margin-top:2em;">';
                        echo '<h2 class="admin"><span class="admin description">Name: </span>'.$row['name'].'</h2>';
                        
                        echo '<div class="admin server"><span class="admin description">Description: </span>'.$row['description'].'</div>';
                        
                        echo '<div class="admin server"><span class="admin description">Ip address: </span><span class="admin property">'.$row['ip'].'</span></div>';
                        
                        echo '<div class="admin server"><span class="admin description">Key: </span><span class="admin property"><input type="text" style="width:70%;" value="'.$row['request_key'].'"></span></div>';
                        
                        $check_ip='No';
                        if ($row['check_ip']==1) {
                            $check_ip='Yes';
                        }
                        echo '<div class="admin server"><span class="admin description">Always Check ip address: </span><span class="admin property">'.$check_ip.'</span></div>';
                        echo '<div class="admin server"><form method="post" action="editserver.php"><input type="hidden" name="id" value="'.$row['id'].'"><input style="font-size:1.5em;" type="submit" value="Edit"></form></div>';
                    echo '</section>';
                }
            ?>
        </div>
    <?php
}
?>
