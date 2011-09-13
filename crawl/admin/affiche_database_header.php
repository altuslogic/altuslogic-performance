<div id='submenu'>
    <ul>                               
        <li><a href="admin.php?f=database&type=selection" class=<?php print ($type=='selection'?"subselected":"subdefault"); ?>>selection</a></li>  
        <li><a href="admin.php?f=database&type=tabledetails" class=<?php print ($type=='tabledetails'?"subselected":"subdefault"); ?>>table details</a></li>       <li><a href="admin.php?f=database&type=databasedetails" class=<?php print ($type=='databasedetails'?"subselected":"subdefault"); ?>>database stats</a></li>  
    </ul>
</div>