<?php include('includes/header.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Imprint Options</title>
	<link rel="stylesheet" href="css/styles.css" type="text/css" />
	<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
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
.style9 {
	font-size: 14;
	font-weight: bold;
	color: #FFFFFF;
}

-->
</style>
</head>
<body>
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
        <table width="100%" border="0" cellpadding="5" cellspacing="1">
                <tr>
                  <td bgcolor="#000000">
				  	<a href="imprint.php"><span class="style9">Category</span></a>
				  </td>
                </tr>
                <tr>
                  <td bgcolor="#000000">
				  	<a href="imprint_list.php"><span class="style9">Imprint Option</span></a>
				  </td>
                </tr>
              </table>
        </div>
        <div style="float:left">
<table align="center" border="0" cellpadding="10" cellspacing="1" width="800">
  <tbody><tr>
    <td bgcolor="#66CCCC"><h2 class="style1">Imprint Option</h2></td>
    <td align="right" bgcolor="#66CCCC" width="30%"><h3><a href="imprint_admin.php" title="Add New Option">Add New Option</a></h3></td>
  </tr>
</tbody></table>

<?php
$sql_details = "SELECT ico.*,cc.CATEGORY FROM imprint_cusom_options as ico 
				INNER JOIN cimprint_category AS cc ON ico.category_id=cc.IDCATEGORY
				ORDER BY category_id ASC, id DESC";
$result_details = mysql_query($sql_details);
?>
	<table width="100%" cellpadding="4" cellpadding="4">
		<tr style="background:#000000;color:#fff;">
			<th width="40%"><span class="style1"><strong>Admin Note</strong></span></th>
			<th width="20%"><span class="style1"><strong>Type</strong></span></th>
			<th width="20%"><span class="style1"><strong>Category Name</strong></span></th>
			<th width="20%"><span class="style1"><strong>Action</strong></span></th>
		</tr>
	
	<?php
	$i = 0;
	while($option = mysql_fetch_array($result_details)) {
		
	?>
	<tr <?php if ($i++%2==0) echo 'style="background:#eee;"'; else  echo 'style="background:#fff;"'; ?> >
		<td width="40%"> 
			<?php echo $option['admin_note']; ?>
		</td>
		<td width="20%"> 
			<?php 
				$type = 'Logo';
				if ($option['type'] == 1) $type = 'Logo';
				elseif ($option['type'] == 2) $type = 'Number';
				else if ($option['type'] == 3) $type = 'Name';
				
				echo $type;
				
			 ?>
		</td>
		<td width="20%">
			<?php echo $option['CATEGORY'];?>
		</td>
	
		<td width="20%">
			<a href="imprint_admin.php?id=<?php echo $option['id'];?>" title="Edit">Edit</a> | 
                        <a href="imprint_delete.php?id=<?php echo $option['id'];?>" title="Delete">Delete</a>
		</td>
	</tr>
	<?php } ?>
	</table>
</div>
</div>
</div>
</div>
</body>
</html>