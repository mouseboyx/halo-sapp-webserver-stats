<?php
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']==true && $_SESSION['admin']==1) {
    include '../connect.php';
    include '../tablePrefix.php';
    ?>
        <h1 class="admin">Manage Servers</h1>
        <?php include 'adminmenu.php' ?>
        
            <section class="admin">
            <span class="admin menu"><a href="serveradd.php" style="text-decoration:none;">+ Add New Server +</a></a>
            </section>
            <?php
                $q='select '.$t_prefix.'servers.name, '.$t_prefix.'server_settings.description,'.$t_prefix.'servers.ip,'.$t_prefix.'servers.request_key,'.$t_prefix.'server_settings.check_ip,'.$t_prefix.'servers.id from '.$t_prefix.'servers inner join '.$t_prefix.'server_settings on '.$t_prefix.'servers.id='.$t_prefix.'server_settings.server_id';
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
