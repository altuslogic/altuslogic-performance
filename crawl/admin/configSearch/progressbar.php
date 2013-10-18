<?php    
    function initProgress($gauche,$haut,$largeur,$hauteur,$bord_col,$txt_col,$bg_col){
        $tailletxt=$hauteur-10;                      
        echo '<div id="progress" style="display:none;"><div id="pourcentage" style="top:0;';
        echo 'width:'.$largeur.'px;';
        echo 'height:'.$hauteur.'px;border:1px solid '.$bord_col.';font-family:Tahoma;font-weight:bold;';
        echo 'font-size:'.$tailletxt.'px;color:'.$txt_col.';z-index:1;text-align:left;"></div>';
        echo '<div id="progrbar" style="'; //+1
        //echo 'left:'.($gauche+1).";"; //+1
        echo 'width:0px;';  
        echo 'height:'.$hauteur.'px;';
        echo 'background-color:'.$bg_col.';z-index:0;"></div></div>';
    }
    
    function startProgress($texte){
        echo "\n<script>";
        echo "document.getElementById('pourcentage').innerHTML='".$texte." : 0%';";
        echo "document.getElementById('progress').style.display='block';\n";                          
        echo "</script>";
        flush();
    }
    
    function updateProgress($texte,$indice){
        if ($indice==100){
            echo "<script>document.getElementById('progress').style.display='none';</script>";
            return;
        }
        echo "\n<script>";
        echo "document.getElementById('pourcentage').innerHTML='".$texte." : ".$indice."%';";
        echo "document.getElementById('progrbar').style.width='".($indice*6)."px';\n";                         
        echo "</script>";
        flush();
    }  
?>
