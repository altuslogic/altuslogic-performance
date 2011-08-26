<?php
$file = '<div class=\"post-56 post type-post status-publish format-standard hentry category-au-programme post\" id=\"post-56\">';
  $container = '<div id="post-56">';
    $pos0 = strpos($file,$container)+strlen($container);
    $cpt = 1;
    $pos = $pos0;
    while ($cpt>0){
        $pos1 = strpos($file,"<div>",$pos);
        $pos2 = strpos($file,"</div>",$pos);
        echo "<br>",$cpt," ",$pos1," ",$pos2;
        if ($pos1<$pos2 && $pos1!==FALSE){
            $cpt++;
            $pos = $pos1+5;
        }
        else if ($pos1>$pos2 && $pos2!==FALSE){  
            $cpt--;
            $pos = $pos2+6;
        } 
        else {
            echo "erreur";
            die();
        }
    }
    $file = substr($file,$pos0,$pos-$pos0);
    echo "<pre>$file</pre>"; die();
?>
