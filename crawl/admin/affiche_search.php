<link rel="stylesheet" type="text/css" href="configSearch/my.css">
<script type="text/javascript"
    src="http://maps.googleapis.com/maps/api/js?sensor=true">
</script>
<script type="text/javascript"
    src="http://www.google.com/jsapi?key=ABQIAAAAuIowY22xd6M7t2fgVRJUZhTS00s8BpmtWZHzIF6WCUpWiuK3aRR3Kcl7kSKmyZnX6ao-QJp2Ptyj_w">
</script>                               
<link rel="stylesheet" href="../../jquery.ui/all.css">      
<script src="../../jquery-1.5.1.js"></script>
<script src="geolocate.js"></script>

<div id='submenu'>
    <ul>                               
        <li><a href="admin.php?f=search&type=search" class=<?php print ($type=='search'?"subselected":"subdefault"); ?>>Search</a></li>   
        <li><a href="admin.php?f=search&type=geo" class=<?php print ($type=='geo'?"subselected":"subdefault"); ?>>Geo</a></li>        
    </ul>
</div>

<?php                              
    include "configSearch/index.php";
?>