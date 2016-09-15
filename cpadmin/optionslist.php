<?php
	include("includes/header.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Untitled Document</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<style type="text/css">
<!--
body,td,th {
	font-family: Arial;
	font-size: 12px;
	color: #333333;
}
.style1 {color: #FFFFFF}
.smallbox {
	width: 35px;
}
.melbox {
	width: 120px;
}
.lglbox {
	width: 230px;
}

.100{width:100%}
.pricebox {
	width: 50px;}
h1,h2,h3,h4,h5,h6 {
	font-family: Arial, Helvetica, sans-serif;
}
h1 {
	font-size: 100px;
	color: #FF0000;
}

.border { border:1px; border-color:#999999; padding:3px;}
.style2 {
	color: #000000;
	font-weight: bold;
}
.style4 {color: #FFFFFF; font-weight: bold; }
#menu_nav li{
	 color: #000000;
    line-height: 20px;
    margin-left: 10px;
    padding: 5px;
	list-style:none outside none;
}
-->
</style>

<script type="text/javascript" src="./js/jquery-1.4.4.min.js"></script>
<script>
function deleteOptions(el){
	var id = el.id;
	var del = confirm("Confirm delete option ?");
	if(del){
		$.ajax({
  			type: 'POST',
			  url: "./imprint/inc_options.php",
			  data: "type=delete&idop="+id,
			  success: delsuccess
  
		});	
	}
	
}

function delsuccess(data){
	if(data=="delete.success"){
		$.ajax({
  type: 'POST',
  url: "./imprint/inc_options.php",
  data: "type=list",
  success: success
  
});
	}
	
}
</script>

</head>

<!-- Master Div starts from here -->
<div class="Master_div"> 
      <!-- Header Div starts from here -->
    	<div class="PD_header">
    		<div class="upper_head"></div>
    		<div class="navi">
          		<?php include('includes/menu_main.php'); ?>
          	<div class="clear"></div>
        	</div>
  		</div>
        <div class="PD_main_form">
        <div style="width:150px;float:left">
         <ul id="menu_nav" style="background:none repeat scroll 0 0 #E9E9E9;border:1px solid #CFCFCF;height:400px;">
        			<li><a href="imprint.php">Existing Category</a></li>
			         <li><a href="addimprint.php">Add New Category</a></li>
			         <li><a href="optionslist.php">Existing Options</a></li>
			       
         
			        </ul>
        </div>
        <div style="float:left">
<table align="center" border="0" cellpadding="10" cellspacing="1" width="800">
  <tbody><tr>
    <td bgcolor="#66CCCC"><h2 class="style1">Options List</h2></td>
    <td align="right" bgcolor="#66CCCC" width="30%"></td>
  </tr>
</tbody></table>
<table align="center" border="0" cellpadding="10" cellspacing="1" width="800">
  <thead><tr>
    <td align="center" bgcolor="#000000" width="10%"><span class="style1"><strong>ID</strong></span></td>
    <td align="center" bgcolor="#000000" width="20%"><span class="style1"><strong>Option</strong></span></td>
    <td align="left" bgcolor="#000000" width="30%"><span class="style1"><strong>Admin Notes</strong></span></td>
    <td align="center" bgcolor="#000000" width="20%"><span class="style1"><strong>Category</strong></span></td>
    <td align="center" bgcolor="#000000" width="20%"><span class="style1"><strong>Action</strong></span></td>
  </tr>
</thead>
<tbody id="optionsList">
</tbody>
</table>
</div>
<script>
/*$("#categorylist").load("./imprint/inc_cimprint.php", function() {
  alert('Load was performed.');
});*/
$.ajax({
  type: 'POST',
  url: "./imprint/inc_options.php",
  data: "type=list",
  success: success
  
});

function success(data){
			$("#optionsList").html(data);
}
</script>
</div>
</div>
</body></html>