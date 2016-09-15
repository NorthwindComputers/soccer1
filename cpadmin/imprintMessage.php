<?php
	include('includes/header.php');
	session_start();
	//require 'includes/db.php';
	if(isset($_GET["type"]) && $_GET["type"] == "delete") {
		
		$id = mysql_real_escape_string($_POST["id"]);
		$sql_del = "DELETE FROM imprint_message WHERE id=".$id." LIMIT 1";
		if(!mysql_query($sql_del)) {
			echo "Error removing imprint message";
		} else {
			echo "Imprint message removed!";
		}
		header('Location:settings.php?p=Imprint');
	}
	if(isset($_POST["btnSaveUpdate"])) {
		$content =  addslashes(mysql_real_escape_string($_POST["content"]));
		$id =  intval($_POST["id"]);
		if (isset($_POST['is_update']) && $_POST['is_update']) {
			$sql_home = "UPDATE imprint_message SET content='".$content."' WHERE id=".$id;
			mysql_query($sql_home);
		} else {
			$sql_new  = "INSERT INTO imprint_message(id, content) VALUES(".$id.", '".$content."')";
			mysql_query($sql_new);
		}
		header('Location:settings.php?p=Imprint');
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Imprint Message</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" />
<link rel="stylesheet" href="js/jquery.wysiwyg.css" type="text/css" />
<link rel="stylesheet" href="css/jquery.ui.datepicker.css">
<link rel="stylesheet" href="css/jquery.ui.theme.css">
<link rel="stylesheet" href="css/jquery.treeview.css" type="text/css" />
<script type="text/javascript" src="js/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="js/jquery.wysiwyg.js"></script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="js/jquery.ui.core.js"></script>
<script type="text/javascript" src="js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="js/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="js/jquery.treeview.js"></script>

<script type="text/javascript">
	$(document).ready(function(){
		$('#leftmenu ul').hide();
		$('#leftmenu li a').click(function() {
			$(this).next().slideToggle('normal');
		});
		
	});

	function ChangeCat() {
		
	}
</script>
<!--[if lt IE 8]>
   <style type="text/css">
   li a {display:inline-block;}
   li a {display:block;}
   </style>
   <![endif]-->
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
  <!-- Header Div ends here --> 
  <!-- Product Detail Div starts from here -->
  <div class="options">
	<h1>Settings<br />
          <span>----------------------------------------------------------</span></h1>
    	<div class="clear"></div>
	
	<table width="100%" border="0" align="center" cellpadding="5" cellspacing="1">
  		<tr>
    			<td width="180" align="left" valign="top" class="setting">
        

	<ul id="leftmenu" class="leftmenu">
          <li><a class="menu" href="#">Sales Setup</a>
            <ul>
              <li><a class="menu" href="settings.php?p=SalesEmail">Email Setup</a></li>
              <li><a class="menu" href="settings.php?p=SalesMessage">Sales Message</a></li>
              <li><a class="menu" href="settings.php?p=VIPMessage">VIP Message</a></li>
            </ul>
          </li>
          <li><a class="menu" href="#">User Management</a>
            <ul>
              <li><a class="menu" href="settings.php?p=UsersRules">Rules</a></li>
              <li><a class="menu" href="settings.php?p=Users">View Users</a></li>
            </ul>
          </li>
          <li><a class="menu" href="settings.php?p=Gender">Ranges</a></li>
          <li><a class="menu" href="settings.php?p=CustomerGroup">Customer Group</a></li>
          <li><a class="menu" href="settings.php?p=Options">Product Options</a></li>
	  	  <li><a class="menu" href="settings.php?p=Category">Product Category</a></li>
          <li><a class="menu" href="settings.php?p=Style">Product Styles</a></li>
          <li><a class="menu" href="settings.php?p=Imprint">Imprint Message</a></li>
          <li><a class="menu" href="settings.php?p=Pricing">Product Pricing</a></li>
	  	  <li><a class="menu" href="settings.php?p=Manufacturer">Manufacturers</a></li>
	  	  <li><a class="menu" href="settings.php?p=Vendor">Vendors</a></li>
          <li><a class="menu" href="settings.php?p=Shipping">Shipping</a></li>
          <li><a class="menu" href="settings.php?p=Payment">Payments</a></li>
          <li><a class="menu" href="settings.php?p=">Google API</a></li>
          <li><a class="menu" href="settings.php?p=Tax">Tax Setup</a></li>
          <li><a class="menu" href="settings.php?p=Coupon">Coupon Manager</a></li>
          <li><a class="menu" href="settings.php?p=Banner">Banner</a></li>
           <li><a class="menu" href="settings.php?p=Ads">Ads</a></li>
          <li><a class="menu" href="#">VIP</a>
          	<ul>
            	<li><a class="menu" href="settings.php?p=VIP">Edit VIP</a></li>
                <li><a class="menu" href="settings.php?p=VIPManage">Manage VIPs</a></li>
                <li><a class="menu" href="settings.php?p=VIPLevel">Levels</a></li>
            </ul>
          </li>
        </ul>

    </td>

    <td align="left" valign="top">
<?php
	if(isset($_GET["type"]) && $_GET["type"] == "edit") {
		$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
		$productName = '';
		$content = '';
		$row_imprintMessage = array();
		if ($id) {
			$sql_imprintMessage = "SELECT p.*, im.content, im.id AS im_id FROM products AS p 
								   LEFT JOIN imprint_message AS im ON im.id=p.id 
								   WHERE p.id=".$id." LIMIT 1";
			$result_imprintMessage = mysql_query($sql_imprintMessage);
			$row_imprintMessage = mysql_fetch_assoc($result_imprintMessage);
			//print_r($row_imprintMessage); exit;
			$productName = $row_imprintMessage['BrowserName'].' '.$row_imprintMessage['BrowserName2']
						   .' '.$row_imprintMessage['BrowserName3'];
			$content = $row_imprintMessage['content'];
			
		}
		
		
		$isSecure = (!empty($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] != 'off');
	 	$url = ($isSecure ? 'https://' : 'http://') . $host;
	 	$basePath = str_replace('includes', '', dirname($_SERVER['SCRIPT_NAME']));
	 	$url  .= $_SERVER['SERVER_NAME'].('/' == $basePath ? '' : $basePath);
		//echo $url; exit;
		?>
        
	<form action="" method="post">
        <table border="0" width="700px">
        <tr>
        	<td colspan="2" class="cmsback" style="width: 100%;">Product Name<br/>
				<input type="text" disabled="true" id="ProductName" name="ProductName" value="<?php echo $productName;?>" />
	            <input type="hidden" id="id" name="id" value="<?php echo $id;?>" />
	        </td>
		</tr>
        <tr>
            <td colspan="2" class="cmsback" style="width: 100%;">Imprint Content<br/>
                <textarea id="content" name="content"><?php echo $content;?></textarea>
                <?php if (isset($row_imprintMessage['im_id'])&& $row_imprintMessage['im_id']): ?>
                	<input type="hidden" name="is_update" value="1">
                <?php endif; ?>
            </td>
        </tr>
            <tr>
            	<td colspan="2" style="background-color: #fff !important;">
					<input type="submit" class="savebutton" id="btnSaveUpdate" name="btnSaveUpdate" value="Save" />
			</td>
        </tr>
        
        </table>
        </form>
        <script>
			CKEDITOR.replace('content', 
			{ 
					fullPage : true,
			});
		</script>
       
		<?php
	}

	
?>
	</td>
  </tr>
</table>


  </div>

</body>
</html>
<?php
	mysql_close($conn);
?>