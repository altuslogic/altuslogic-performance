<?php
    function start_timer(){
        $time = microtime(true);
        return $time; 
    }
    function end_timer($start){
        $end = microtime(true);
        return $end-$start;
    }
?>
