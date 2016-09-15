<?php
/*******************************
 * Frontend Footer include 
 *                                   
 * Updated: 09 August 2016 
 * By: Richard Tuttle      
 ******************************/
 
$sql_footer = "SELECT Content FROM cms WHERE Type='Footer' LIMIT 1";
$result_footer = mysql_query($sql_footer) or die("Footer error:" . mysql_error());
$row_footer = mysql_fetch_assoc($result_footer);
echo stripslashes($row_footer["Content"]);
echo '<hr><p class="footerText" align="center">Copyright &copy;2012 - '.date("Y").' Youth Sports Publishing, Inc. | All Rights Reserved<br>Website design by <a href="http://webdesignboise.us" target="_blank">Web Design Idaho</a></p>';
?>
<script>ga('send', 'pageview');</script>