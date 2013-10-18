<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
<title><? echo $item_sel; ?></title>
<meta name="keywords" content="">
<meta name="description" content="">
<style>
html { font-weight: normal;font-size: 7pt;color : #555; font-family: Verdana; height: 100%;}
td { font-weight: normal;font-size: 7pt;color : #555; font-family: Verdana; }
td a {text-decoration: none;color:#2222aa;padding:2px; }
td.title {   font-weight: normal;font-size: 14pt;color : #555; font-family: Verdana; padding: 0 0 15px 0;}
td.title span {   font-weight: normal;font-size: 9pt;color : #aaa; font-family: Verdana; margin-left:5px;}
td.title a { text-decoration: none;color:#2222aa;padding:2px; }
a.sel{ background-color:#ddd;}
#add { font-size: 6pt; color : #888;}
div.inlne{  /* min-height: 100%;  */
      height:180px;
overflow:scroll;
}
#sticker{
    font-size: 7pt;color : #555; width:265px;padding:16px 0px 5px 20px;margin:0px;background-image:url('images/bg_t_s.png');float:left; height:65px;
}
#sticker:hover{
     background-image:url('images/bg_f_s.png');float:left;
}
#add{color: #ddd;}

</style>
<script language="JavaScript">
<!--
function resize_iframe()
{                                                    var height=window.innerWidth;//Firefox                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=H3H&amp;sll=45.462977,-73.571338&amp;sspn=0.20877,0.213203&amp;ie=UTF8&amp;hq=&amp;hnear=Canada&amp;z=14&amp;ll=45.510317,-73.587627&amp;output=embed"></iframe><br /><small><a href="http://maps.google.com/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=H3H&amp;sll=45.462977,-73.571338&amp;sspn=0.20877,0.213203&amp;ie=UTF8&amp;hq=&amp;hnear=Canada&amp;z=14&amp;ll=45.510317,-73.587627" style="color:#0000FF;text-align:left">View Larger Map</a></small>
    if (document.body.clientHeight)
    {
        height=document.body.clientHeight;//IE
    }
    //resize the iframe according to the size of the
    //window (all these should be on the same line)
    document.getElementById("map").style.height=parseInt(height-330)+"px";
   // document.getElementById("glu2").style.height=parseInt(height-220)+"px";
}

// this will resize the iframe every
// time you change the size of the window.
window.onresize=resize_iframe; 

</script>    
<script src="http://maps.google.com/maps?file=api&v=2&key=ABQIAAAAnNatFxH2LX42s_QZL51BihQcqy5cK-7cNBpUzh59xdkTYzbsHhShgAXTgLmpp66o1LxsNI_TMDlROQ" type="text/javascript"></script>
<script type="text/javascript" src="http://yulwatch.com/tabpane.js"></script>
<link type="text/css" rel="StyleSheet" href="http://yulwatch.com/tab.webfx.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<script language="javascript" src="http://yulwatch.com/hover.js" type="text/javascript"></script>
</head>        
   
<body onload="resize_iframe();load();" onunload="GUnload()">
          <table border="0" width="100%">
                        <tr><td  align="center">All</td>
                        <td align="center">Any</td>
                        <td align="center">www</td>
                        <td align="center">Profile</td>
                        <td align="center">Photo</td>
                        <td align="center">Video</td>
                        <td align="center">Display</td>
                        <td align="center">Logo</td></tr>
                        <tr><? echo "<td align=\"center\" class=\"title\">$pourcent[allx] %<br><span>$va[allx]</span></td>
                                     <td align=\"center\" class=\"title\">$pourcent[alln] %<br><span>$va[alln]</span></td>
                                     <td align=\"center\" class=\"title\">$pourcent[contxurl] %<br><span>$va[contxurl]</span></td>
                                     <td align=\"center\" class=\"title\">$pourcent[contxdspad] %<br><span>$va[contxdspad]</span></td>
                                     <td align=\"center\" class=\"title\">$pourcent[contxprofile] %<br><span>$va[contxprofile]</span></td>
                                     <td align=\"center\" class=\"title\">$pourcent[contxlogo] %<br><span>$va[contxlogo]</span></td>
                                     <td align=\"center\" class=\"title\">$pourcent[contxphoto] %<br><span>$va[contxphoto]</span></td>
                                     <td align=\"center\" class=\"title\">$pourcent[contxvideo] %<br><span>$va[contxvideo]</span></td>"; ?></tr>
                        </table>              
                        
         <div class="tab-pane" id="tab-pane-1"> 
       
    
         <? 
       /*  
          numb_count('final_detail2',$cols_sel,$item_sel,$publish,'final_detail2');   
          numb_count('final_email',$cols_sel,$item_sel,$publish,'final_email');  
        */    
          ?>
     
         
          <div class="tab-page"  style="overflow:auto;"><h2 class="tab">map</h2> 
          <div style="width:1900px;">
                    
          </div> 
          </div> 
          
      
        <? if(0){ ?>        
         <div id="map"></div>  
         
        
         <? } 
         
                                                  $sti.=showitems_url($cols_sel,$item_sel);
                              echo  $sti;
         ?>           
            
