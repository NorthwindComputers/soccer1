<?php
include("./Database.class.php");
include("./cimprint_category.class.php");
include("./impcategory_option.class.php");
include("./impoption_settings.class.php");
include("./images.class.php");
include("./pricing.class.php");

if($_POST["type"]=="list"){
	$impcategory_option = new impcategory_option();
	$options_list = array();
	$options_list = $impcategory_option->readArray();
	$i=0;
	$bgcolor ="";
	foreach( $options_list as $key => $value ){ 
	  if($i%2==0)
	    $bgcolor="#E8E8E8";
	  else
		  $bgcolor="#FFFFFF";
			  ?>
          <tr>
	          <td align="center" bgcolor="<?php echo $bgcolor; ?>"><?php echo $value->getIDOPTION();?></td>
              <td align="left" bgcolor="<?php echo $bgcolor; ?>"><?php echo $value->getOPTION_NAME();?></td>
              <td align="left" bgcolor="<?php echo $bgcolor; ?>"><?php echo $value->getADMIN_NOTES();?></td>
              <?php 
			  	$categoryname = "";
				$cimprint_category = new cimprint_category();
				$array = array();
				$array["IDCATEGORY"] = $value->getIDCATEGORY();
				$cimprint_category->readObject($array);
			  	$categoryname = $cimprint_category->getCATEGORY();		  
			  ?>
              <td align="left" bgcolor="<?php echo $bgcolor; ?>"><?php echo $categoryname;?></td>
              <td align="center" bgcolor="<?php echo $bgcolor; ?>"><a href="edit_option.php?idop=<?php echo $value->getIDOPTION();?>">Edit</a> | <a href="#" onClick="deleteOptions(this)" id="<?php echo $value->getIDOPTION();?>">Delete</a></td>
           </tr>
           <?php
		$i++;
	}
}

if($_POST["type"]=="delete"){
	$idOption = mysql_real_escape_string($_POST["idop"]);
	$array = array();
	$impcategory_option = new impcategory_option();
	$array["IDOPTION"]=$idOption;
	// delete option
	$impcategory_option->delete($array);

	// delete option settings
	
	
	
	// delete option images
	
	
	//pricing
	
	echo "delete.success";
	
}

if($_POST["type"]=="updateOptionImgValue"){
	$idImg = mysql_real_escape_string($_POST["idimg"]);
	$value = mysql_real_escape_string($_POST["value"]);
	$images = new images();
	$array = array();
	$array["IDIMAGE"] = $idImg;
	$images->readObject($array);
	$images->setIMG_NUMBER($value);
	$images->insert();
	echo "update.succeed";
	
	
}
if($_POST["type"]=="deleteImg"){
	
	$idImg = mysql_real_escape_string($_POST["idimg"]);
	$images = new images();
	$array = array();
	$array["IDIMAGE"] = $idImg;
	$images->delete($array);
	echo "delete.succeed";
}

if($_POST["type"]=="updateImgSetupFeeValue"){
	
	$idop = mysql_real_escape_string($_POST["idop"]);
	$value = mysql_real_escape_string($_POST["value"]);
	$pricing = new pricing();
	$pricing->setIDOPTION($idop);
	$pricing->setSetup_fee($value);
	$pricing->updateSetupFee();
	echo "success";
}
?>