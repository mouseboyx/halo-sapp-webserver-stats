<?php
function translateTagPath($tag_path) {
    include 'connect.php';
    include 'tablePrefix.php';
    $tag_path_escape=mysqli_real_escape_string($c,$tag_path);
    $q="select shown_as from ".$t_prefix."user_damage_tags where tag_path='".$tag_path_escape."'";
    $res=mysqli_query($c,$q);
    $row=mysqli_fetch_assoc($res);
    if ($row==null) {
        $q2="select shown_as from ".$t_prefix."stock_damage_tags where tag_path='".$tag_path_escape."'";
        $res2=mysqli_query($c,$q2);
        $row2=mysqli_fetch_assoc($res2);
        if ($row2==null) {
            return $tag_path;
        } else {
            return $row2['shown_as'];
        }
    } else {
        return $row['shown_as'];
    }
}

function obscureIp($ip) {
    $parts=explode('.',$ip);
    $counter=1;
    $outstring='';
    foreach ($parts as $digit) {
        if ($counter<3) {
            $outstring.="xxx.";
        } else {
            if ($counter==3) {
                $outstring.="xx";
                $digit_string=strval($digit);
                $sub=substr($digit_string,-1);
                $outstring.=$sub.".";
            }
            if ($counter==4) {
                $outstring.=strval($digit);
            }
        }
    $counter++;
    }
    return $outstring;
}
?>
