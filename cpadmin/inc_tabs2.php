<?php
session_start();
if($_POST["type"]=="tabsList"){
	include_once("./Database.class.php");
	include_once("./imp_category_tabs.class.php");
	$imp_category_tabs = new imp_category_tabs();
	$array = array();
	$array["imprint_categ_id"] = $_POST["idc"];
	$tabsList = array();
	$tabsList = $imp_category_tabs->readArray($array);
	$i=0;
	$bgcolor ="";
	foreach( $tabsList as $key => $value ){ 
	  if($i%2==0)
	    $bgcolor="#E8E8E8";
	  else
		  $bgcolor="#FFFFFF";
	?>
    <tr>
  <td align="center" bgcolor="<?php echo $bgcolor; ?>"><?php echo $value->getid_tab();?></td>
   <td align="left" bgcolor="<?php echo $bgcolor; ?>"><?php echo $value->gettab_name();?></td>
  <td align="center" bgcolor="<?php echo $bgcolor; ?>"><a href="editTab.php?idtab=<?php echo $value->getid_tab();?>">Edit</a> | <a href="#" onClick="deleteTab(this)" id="<?php echo $value->getid_tab();?>">Delete</a>|
</td>
</tr>
    
    <?php
	$i++;
	}
}

if($_POST["type"]=="previewOption"){
	$idoption = $_POST["idoption"];
	include_once("./Database.class.php");
	include_once("./impoption_settings.class.php");
	$impoption_settings = new impoption_settings();
	$array = array();
	$array["IDOPTION"] = $idoption;
	$impoption_settings->readObject($array);
	echo "<img src='./cpadmin/imprint_files/".$impoption_settings->getFRONTEND_PREVIEW()."'/>";
}

if($_POST["type"]=="setOptionName"){
	$idBundle = $_POST["idBundle"];
	$rank = $_POST["rank"];
	$value = $_POST["name"];
	$_SESSION["bundleItems"]["items"][$sz][$idBundle]["opname"]=$value;
	echo $_SESSION["bundleItems"]["items"][$sz][$idBundle]["opname"];
}
								
?>