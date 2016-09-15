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
	$idtab = $_POST["idtab"];
	$_SESSION["currentImprint"][$idtab] = $idoption;
	include_once("./Database.class.php");
	include_once("./images.class.php");
	include_once("./impcategory_option.class.php");
	include_once("./optiontype.class.php");
	include_once("./colors_images.class.php");
	include_once("./options_color.class.php");
	// test if multiply or single select option
	$impcategory_option = new impcategory_option();
	$iparam = array();
	$iparam["IDOPTION"] = $idoption;
	$impcategory_option->readObject($iparam);
	$optiontype = new optiontype();
	$oparam = array();
	$oparam["IDTYPE"] = $impcategory_option->getIDTYPE();
	$optiontype->readObject($oparam);
	if($optiontype->getimptype()=="Single Select"){
	//
	 $db = new Database(); 
		$images = new images();
		$array = array();
		$array["IDOPTION"] = $idoption;
		$imgList = array();
		$imgList = $images->readArray($array);
		$row1 = 0;
		echo "<div class='separator2'></div>";
		
		  echo "<table style='width:100%'> 
		  			<tr>
		  				<td style='width: 427px;'>&nbsp;</td>
						<td align='right'>";
						
						$sqlc = "SELECT DISTINCT (idcolor) FROM colors_images where idoption =".$idoption;
						
						$listColors = $db->select($sqlc);
						foreach($listColors as $keys => $valuec){
							$idcolor = $valuec["idcolor"];
							$options_color = new options_color();
							$op = array();
							$op["idcolor"] = $idcolor;
							$options_color->readObject($op);
							echo "<img src='../images/".$options_color->getimages()."' alt='".$options_color->getname()."' style='float: left; clear: none; border: medium none; padding: 0px; margin-right: 2px;width:20px;height:20px;'/>";
						}
		   
		   echo "       </td>
		 		 	</tr>
				<tr><td colspan='2'>";
		
			
			$colors_images = new colors_images();
			$colorp = array();
			
			$colorp["idoption"] = $idoption;
			$listImgs = array();
			$listImgs = $colors_images->readArray($colorp);
			foreach($listImgs as $kcolor => $img){
				$images = new images();
				$pimg = array();
				$pimg["IDIMAGE"] = $img->getidimages();
				$images->readObject($pimg);
					echo"<div style='float: left;'>";
				echo "<img src='./cpadmin/imprint_files/".$images->getIMAGEURL()."' style='clear: none; padding: 0px; margin-right: 2px;width:84px;height:84px;'/>";
				echo "<input type='radio' name='radioOption' id=''/>".$images->getIMG_NUMBER();
					echo"</div>";
			}
			echo "</div></td></tr> </table>";
		
		/*foreach( $imgList as $key => $value ){
			$margintop = "44px";
			if($row1>=3)
				$margintop="261px";
			echo "<div style='float: left; min-width: 200px; height: 82px; margin-top: ".$margintop."'>";
			echo "<div>";
		
			?>
			<h5><a class="price" onclick="showPricing(this)" href="javascript:void(0)" id="p<?php echo $idoption;?>" style="background-color:#fe0000;color: #fff; width: 58px; margin-right: 4px; text-align: center;">pRICING</a></h5>
			<?php 
			echo "</div>";
			echo "<img src='./cpadmin/imprint_files/".$value->getIMAGEURL()."' style='float: left; min-width: 172px; height: 310px; width: 210px; margin-right: 4px; padding: 0px;'/><br/>
			<input type='radio' name='radioOption' id=''>".$value->getIMG_NUMBER()."
			</div>";
			$row1++;*/
		
	}else { // its multiply select
	   $sqlStyles = "select distinct styleName from colors_images where idoption =".$idoption;
	   $db = new Database(); 
	   $result = $db->select($sqlStyles);
	
	   foreach($result as $dt => $data){
		  
		  echo "<div class='separator2'></div>";
		  echo "<table style='width:100%'> 
		  			<tr>
		  				<td style='width: 427px;'>Please select ".$data["styleName"]."</td>
						<td align='right'>";
						
						$sqlc = "SELECT DISTINCT (idcolor) FROM colors_images WHERE styleName = '".$data["styleName"]."' AND idoption =".$idoption;
						
						$listColors = $db->select($sqlc);
						foreach($listColors as $keys => $valuec){
							$idcolor = $valuec["idcolor"];
							$options_color = new options_color();
							$op = array();
							$op["idcolor"] = $idcolor;
							$options_color->readObject($op);
							echo "<img src='../images/".$options_color->getimages()."' alt='".$options_color->getname()."' style='float: left; clear: none; border: medium none; padding: 0px; margin-right: 2px;width:20px;height:20px;'/>";
						}
		   
		   echo "       </td>
		 		 	</tr>
				<tr><td colspan='2'>";
		
			
			$colors_images = new colors_images();
			$colorp = array();
			$colorp["styleName"] = $data["styleName"];
			$colorp["idoption"] = $idoption;
			$listImgs = array();
			$listImgs = $colors_images->readArray($colorp);
			foreach($listImgs as $kcolor => $img){
				$images = new images();
				$pimg = array();
				$pimg["IDIMAGE"] = $img->getidimages();
				$images->readObject($pimg);
					echo"<div style='float: left;'>";
				echo "<img src='./cpadmin/imprint_files/".$images->getIMAGEURL()."' style='clear: none; padding: 0px; margin-right: 2px;width:84px;height:84px;'/>";
				echo "<div><input type='radio' name='radioOption".$data["styleName"]."' id=''/>".$images->getIMG_NUMBER()."</div>";
					echo"</div>";
			}
			echo "</div></td></tr> </table>";
	   }
	}
}

if($_POST["type"]=="previewshirt"){
	$idoption = $_POST["idoption"];
	include_once("./Database.class.php");
	include_once("./impoption_settings.class.php");
	include_once("./pricing.class.php");
	$impoption_settings = new impoption_settings();
	$array = array();
	$array["IDOPTION"] = $idoption;
	$pricing = new pricing();
	$pricing->readObject($array);
	$impoption_settings->readObject($array);
	echo "<img src='./cpadmin/imprint_files/".$impoption_settings->getFRONTEND_PREVIEW()."' style='max-width:174px;'/>|".$pricing->getPRICE4();
	
}

if($_POST["type"]=="overview"){
	$idtab = $_POST["idtab"];
	$idoption = $_SESSION["currentImprint"][$idtab];
	include_once("./Database.class.php");
	include_once("./impcategory_option.class.php");
	$array = array();
	$array["IDOPTION"] = $idoption;
	
	$option = new impcategory_option();
	$option->readObject($array);
	echo $option->getOPTION_NAME();
	
}

if($_POST["type"]=="previewPrice"){
	$idoption = $_POST["idoption"];
	include_once("./Database.class.php");
	include_once("./pricing.class.php");
	include_once("./impcategory_option.class.php");
	include_once("./optiontype.class.php");
	$array = array();
	$array["IDOPTION"] = $idoption;
	$option = new impcategory_option();
	$option->readObject($array);

	
	$pricing = new pricing();
	$pricing->readObject($array);
	?>
    <table width="384" cellpadding="0" cellspacing="0">
                                    <tr>
                                    <td width="148" height="41" rowspan="2" align="center" bgcolor="#C7C8CA" style="border-bottom: 1px solid; border-right: 1px solid;border-left: 1px solid;border-top: 1px solid;">Description</td>
                                    <td width="90" rowspan="2" align="center" bgcolor="#C7C8CA" style="border-bottom: 1px solid; border-right: 1px solid;border-top: 1px solid;">Non Member</td>
                                    <td height="21" colspan="4" align="center" bgcolor="#ED1B24" style="color:#fff;border-top: 1px solid #000;">VIP Member</td>
                                    </tr>
                                    <tr>
                                      <td width="37" height="23" align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getSTARTQT_1();?></td>
                                      <td width="37" align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getSTARTQT_2();?></td>
                                      <td width="35" align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getSTARTQT_3();?></td>
                                      <td width="35" align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getSTARTQT_4();?></td>
      </tr>
                                    <tr>
                                    	<td height="25" style="border-bottom: 1px solid; border-right: 1px solid;border-left: 1px solid;"><?php echo $option->getOPTION_NAME();  ?>
                                    	</td>
                                    	<td align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getNONMEMBER_PRICE();?>
                                    	</td>
                                    	<td align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getSTARTQT_1();?>
                                    	</td>
                                    	<td align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getSTARTQT_2();?></td>
                                    	<td align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getSTARTQT_3();?></td>
                                    	<td align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getSTARTQT_4();?></td>
                                    </tr>
                                    <tr>
                                      <td height="27" style="border-bottom: 1px solid; border-right: 1px solid;border-left: 1px solid;">Non Sequence</td>
                                      <td align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getNONSEQUENCE_PRICE();?></td>
                                      <td align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getNONSEQUENCE_PRICE1();?></td>
                                      <td align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getNONSEQUENCE_PRICE2();?></td>
                                      <td align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getNONSEQUENCE_PRICE3();?></td>
                                      <td align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getNONSEQUENCCE_PRICE4();?></td>
                                    </tr>
                                    <tr>
                                      <td height="24" style="border-bottom: 1px solid; border-right: 1px solid;border-left: 1px solid;"><?php
									  	$opid = $option->getIDTYPE();
										$ap = array();
										
										$ap["IDTYPE"] = $opid;
										$optiontype = new optiontype();
										$optiontype->readObject($ap);
										echo $optiontype->getOPTIONTYPE();
										
									  ?> Set-up Fee</td>
                                      <td align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getNONSEQUENCE_PRICE();?></td>
                                      <td align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getNONSEQUENCE_PRICE1();?></td>
                                      <td align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getNONSEQUENCE_PRICE2();?></td>
                                      <td align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getNONSEQUENCE_PRICE3();?></td>
                                      <td align="center" style="border-bottom: 1px solid; border-right: 1px solid;"><?php echo $pricing->getNONSEQUENCCE_PRICE4();?></td>
                                    </tr>
                                    </table>
    <?php		
}

if($_POST["type"]=="setOptionName"){
	$idBundle = $_POST["idBundle"];
	$rank = $_POST["rank"];
	$value = $_POST["name"];
	$_SESSION["bundleItems"]["items"][$rank][$idBundle]["opname"]=$value;
	echo $_SESSION["bundleItems"]["items"][$rank][$idBundle]["opname"];
}
if($_POST["type"]=="setOptionNumber"){
	$idBundle = $_POST["idBundle"];
	$rank = $_POST["rank"];
	$value = $_POST["number"];
	$_SESSION["bundleItems"]["items"][$rank][$idBundle]["opnumber"]=$value;
	echo $_SESSION["bundleItems"]["items"][$rank][$idBundle]["opnumber"];
}

?>