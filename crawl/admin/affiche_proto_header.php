<div id='submenu'>
    <ul>                               
        <li><a href="admin.php?f=prototype&type=selection" class=<?php print ($type=='selection'?"subselected":"subdefault"); ?>>Selection</a></li>
        <li><a href="admin.php?f=prototype&type=index" class=<?php print ($type=='index'?"subselected":"subdefault"); ?>>Indexation</a></li>
        <li><a href="admin.php?f=prototype&type=keywords" class=<?php print ($type=='keywords'?"subselected":"subdefault"); ?>>Keywords</a></li> 
        <li><a href="admin.php?f=prototype&type=expr" class=<?php print ($type=='expr'?"subselected":"subdefault"); ?>>Expressions</a></li>
        <li><a href="admin.php?f=prototype&type=errors" class=<?php print ($type=='errors'?"subselected":"subdefault"); ?>>Error detection</a></li>
        <li><a href="admin.php?f=prototype&type=correct" class=<?php print ($type=='correct'?"subselected":"subdefault"); ?>>Corrections</a></li>  
        <li><a href="admin.php?f=prototype&type=log" class=<?php print ($type=='log'?"subselected":"subdefault"); ?>>Log</a></li>         
    </ul>
</div>