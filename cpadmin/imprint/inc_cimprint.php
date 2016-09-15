<?php
include("./Database.class.php");
include("./cimprint_category.class.php");
if($_POST["type"]=="list"){
	$cimprint_category = new cimprint_category();
	$category_list = array();
	$category_list = $cimprint_category->readArray();
	$i=0;
	$bgcolor ="";
	foreach( $category_list as $key => $value ){ 
	  if($i%2==0)
	    $bgcolor="#E8E8E8";
	  else
		  $bgcolor="#FFFFFF";
			  ?>

<tr>
  <td align="center" bgcolor="<?php echo $bgcolor; ?>"><?php echo $value->getIDCATEGORY();?></td>
  <td align="left" bgcolor="<?php echo $bgcolor; ?>"><?php echo $value->getCATEGORY();?></td>
  <td align="left" bgcolor="<?php echo $bgcolor; ?>"><?php echo $value->getADMIN_NOTES();?></td>
  <td align="center" bgcolor="<?php echo $bgcolor; ?>"><a href="editimprint2.php?idp=<?php echo $value->getIDCATEGORY();?>">Edit</a> | <a href="#" onClick="deleteImprintCateg(this)" id="<?php echo $value->getIDCATEGORY();?>">Delete</a>|
</td>
</tr>
<?php
		$i++;
	}
}

if($_POST["type"]=="delete"){
	$cimprint_category = new cimprint_category();
	$array = array();
	$array["IDCATEGORY"] = mysql_real_escape_string($_POST["idc"]);
	$abcd = $cimprint_category->delete($array);
}

if($_POST["type"]=="optionsList"){
	include_once("./impcategory_option.class.php");
	$impcategory_option = new impcategory_option();
	$array = array();
	$array["IDCATEGORY"] = mysql_real_escape_string($_POST["idp"]);
	$optionsList = array();
	$optionsList = $impcategory_option->readArray($array);
	$optionsHTML = '<table width="800" cellspacing="1" cellpadding="10" border="0" align="center">
		  <tbody><tr>
		    <td bgcolor="#66CCCC"><h2 class="style1" style="color:#fff !important;">Options</h2></td>
		    <td width="30%" bgcolor="#66CCCC" align="right"><h3>Add New Options</h3></td>
		  </tr>
		</tbody></table>';
	$optionsHTML.='<table width="800" cellspacing="1" cellpadding="10" border="0" align="center">
  					<tbody>
						  <tr>
						    <td width="10%" bgcolor="#000000" align="center"><span class="style1"><strong>ID</strong></span></td>
						    <td width="20%" bgcolor="#000000" align="center"><span class="style1"><strong>Options</strong></span></td>
						    <td width="40%" bgcolor="#000000" align="left"><span class="style1"><strong>Admin Notes</strong></span></td>
						    <td width="30%" bgcolor="#000000" align="center"><span class="style1"><strong>Action</strong></span></td>
						  </tr>';
	$i=0;
	
	foreach( $optionsList as $key => $value ){
		 if($i%2==0)
	    $bgcolor="#E8E8E8";
	  else
		  $bgcolor="#FFFFFF";
			
		$optionsHTML.='
 		 <tr>
		    <td bgcolor="'.$bgcolor.'" align="center">'.$value->getIDOPTION().'</td>
		    <td bgcolor="'.$bgcolor.'" align="left">'.$value->getOPTION_NAME().'</td>
		    <td bgcolor="'.$bgcolor.'" align="left">'.$value->getADMIN_NOTES().'</td>
		    <td bgcolor="'.$bgcolor.'" align="center"><a href="edit_option.php?idop='.$value->getIDOPTION().'" id="option'.$value->getIDOPTION().'">Edit</a> |<a href="#" onclick="confirmDeleteOption(this)" id="op'.$value->getIDOPTION().'"> Delete</a></td>
		  </tr>';
	}
	$optionsHTML.='	</tbody>
					</table>';
					
	echo $optionsHTML;

}

if($_POST["type"]=="newOption"){
	include_once("./impcategory_option.class.php");
	include_once("./optiontype.class.php");
	$impcategory_option = new impcategory_option();
	$newOptionId = 1;
	$sql_new_optionID = "select MAX(IDOPTION) as id from impcategory_option";
	$record = Database::select($sql_new_optionID);	

	if(!empty($record[0]["id"]))
		$newOptionId= $record[0]["id"]+1;
		
	$optiontype = new optiontype();
	$options = $optiontype->readArray();
	
	?>
<form name="newoption" action="" method="post">
<table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
  <tbody>
    <tr>
      <td bgcolor="#000000"><span class="style1"><strong>Option </strong></span></td>
    </tr>
  </tbody>
</table>
<table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
  <tbody>
    <tr>
      <td width="50%" valign="top" bgcolor="#F2F2F2" align="left"><table width="100%" cellspacing="0" cellpadding="3" border="0">
          <tbody>
            <tr>
              <td width="33%"><strong>Option Type</strong></td>
              <td width="33%"><select id="optionType" class="melbox" name="optionType" onchange="getTypeOptionsFields(this)">
                  <option selected="selected" name="optionType">Select One</option>
                  <?php
                  foreach( $options as $key => $value ){
					  ?>
                  	<option value="<?php echo $value->getIDTYPE();?>"><?php echo $value->getOPTIONTYPE();?></option>
				  <?php }?>
                </select></td>
              <td width="33%"><input type="text" value="" id="textfield27" class="melbox" name="textfield34"></td>
            </tr>
          </tbody>
        </table></td>
      <td width="50%" valign="top" bgcolor="#F2F2F2" align="left">&nbsp;</td>
    </tr>
  </tbody>
</table>
<table width="800" cellspacing="3" cellpadding="3" border="0" align="center">
  <tbody>
    <tr>
      <td width="50%" valign="top" bgcolor="#F2F2F2" align="left"><table width="100%" cellspacing="0" cellpadding="3" border="0">
          <tbody>
            <tr>
              <td width="50%"><strong>Option ID</strong></td>
              <td width="50%"><input type="text" value="<?php echo $newOptionId;?>" id="textfield" class="lglbox" name="optionId" style="width:230px;" readonly="readonly"/></td>
            </tr>
            <tr>
              <td><strong>Option Name</strong></td>
              <td><input type="text" value="" id="optionName" class="lglbox" name="optionName" /></td>
            </tr>
            <tr>
              <td><strong>Admin Notes</strong></td>
              <td><input type="text" value="" id="adminNotes" class="lglbox" name="adminNotes" /></td>
            </tr>
          </tbody>
        </table></td>
      <td width="50%" valign="top" bgcolor="#F2F2F2" align="left"><div id="optionDetails"></div></td>
    </tr>
    <tr>
      <td colspan="2" align="center" valign="top" bgcolor="#F2F2F2"><input type="submit" value="Save"/></td>
      </tr>
  </tbody>
</table>
</form>
<?php
}
?>
<?php 
if($_POST["type"]=="optionNumber"){
	?>
    
<table width="100%">
    <tr>
   	  <td><strong>
      <input type="checkbox" id="squence" name="sequence" style="width:35px;">
      Non Sequence</strong></td>
        <td></td>
	</tr>   
     <tr>
    	<td><strong>Number of Availble Colors</strong></td>
        <td><input type="text" name="nbrColors"/></td>
	</tr>   
    </table>
    <?php
}
?>
<?php 
if($_POST["type"] == "getCustomerGroupLogo") {
	$logoType = mysql_real_escape_string($_POST["logoType"]);
	
	?>
    
    <?php	
	if($logoType==1){
		?>
<select id="CustomerGroup" name="CustomerGroup">
                    	<option value="">Select Customer Group...</option>
<?php                        
        require '../includes/db.php';
			$sql_cg = "SELECT GroupName FROM customer_group ORDER BY GroupName";
							$result_cg = mysql_query($sql_cg);
							
							while($row_cg = mysql_fetch_array($result_cg)) {
								if($row_cust["CustomerGroup"] == $row_cg["GroupName"]) {
									$selected = ' selected="selected" ';
								} else {
									$selected = '';
								}
								echo "<option value=\"$row_cg[GroupName]\" $selected>$row_cg[GroupName]</option>";
							}
							?>
                            </select>
                            <?php
							
	}
}
?>
<?php	
if($_POST["type"]=="optionLogo"){
?>

 <table width="100%">
    <tr>
   	   <td><strong>Logo</strong></td>
       <td>
       <select name="logoType"  id="logoType" onchange="getCustomerGroupLogo(this)">
       	<option value="0">Select</option>
       	<option value="1">Soccer One</option>
        <option value="2">For Any Group</option>
       </select>
       <div id="customerGroupLogo">
       
       </div>
       </td>
	</tr>   
   
    </table>

<?php	
}

if($_POST["type"]=="deleteOption"){
	include_once("./impcategory_option.class.php");
	$idop = mysql_real_escape_string($_POST["opid"]);
	$array = array();
	$array["IDOPTION"] = $idop;
	$impcategory_option = new impcategory_option();
	$impcategory_option->delete($array);
	include_once("./impoption_settings.class.php");
	include_once("./images.class.php");
	$images = new images();
	$impoption_settings = new impoption_settings();
	$impoption_settings->delete($array);
	$images->delete($array);
	echo "delete.success";
}
if($_POST["type"]=="setoptionNumber"){
	include_once("./impcategory_option.class.php");
	$optionId = mysql_real_escape_string($_POST["optionId"]);
	$type = mysql_real_escape_string($_POST["typeOp"]);
	$impcategory_option = new impcategory_option();
	$params = array();
	$params["IDOPTION"] = $optionId;
	$impcategory_option->readObject($params);
	$impcategory_option->setIDTYPE($type);
	$impcategory_option->insert();
	echo "update.success";
}
?>