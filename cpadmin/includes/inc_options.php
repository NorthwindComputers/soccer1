<?php
	
	require 'db.php';
		
	if($_POST["type"] == "list") {
		
		?>

		<table cellpadding="5" cellspacing="1" width="980px">
			<tr>
            	<td class="row1" style="text-align: center; width: 300px;"></td>
				<td class="row1" style="text-align: center;"><input class="search" type="text" id="soptionid" name="soptionid" value="Search ID" /></td>
				<td class="row1" style="text-align: center;"><input class="search" type="text" id="sname" name="sname" value="Search Option Name" /></td>
				<!-- <td class="row1" style="text-align: center;"><input class="search" type="text" id="sbname" name="sbname" value="Search Browser Name" /></td> -->
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td class="row1">
					<input type="button" style="float: right; border: 1px solid #bebebe; background-color: #ff6600; width: 120px; height: 25px; color: #fff;" id="btnSearch" name="btnSearch" value="Search" />
				</td>

			</tr>
		</table>

		<script>
			$("#soptionid").focus(function() {
				if($(this).val() == 'Search ID') {
					$(this).val('');
				}
			});
			$("#sname").focus(function() {
				if($(this).val() == 'Search Option Name') {
					$(this).val('');
				}
			});

			$("#soptionid").focusout(function() {
				if($(this).val() == '') {
					$(this).val('Search ID');
				}
			});
			$("#sname").focusout(function() {
				if($(this).val() == '') {
					$(this).val('Search Option Name');
				}
			});

			$("#btnSearch").click(function() {
				$("#optionlist").html('<img src="images/loader.gif" />');
				var oid;
				var oname;

				if($("#soptionid").val() == 'Search ID') {
					oid = '';
				} else {
					oid = $("#soptionid").val();
				}

				if($("#sname").val() == 'Search Option Name') {
					oname = '';
				} else {
					oname = $("#sname").val();
				}

				$("#optionlist").load("includes/inc_options.php", {"type":"options", 
										 "id":oid, 
										 "name":oname}
				);

			});

			$("#optionlist").load("includes/inc_options.php", {"type":"options"});
		</script>

        <form action="" method="post">
		<div id="optionlist">
        	<img src="images/loader.gif" />
		</div>
        </form>
        <?php
			
		mysql_close($conn);
		exit();
	}

	if($_POST["type"] == "options") {
		$id = mysql_real_escape_string($_POST["id"]);
		$name = mysql_real_escape_string($_POST["name"]);

		?>
		<table cellpadding="5" cellspacing="1" width="980px" style="margin-top: 20px;">
                <tr>
                    <td colspan="7" class="headersmain">Search Options
                    	<input type="button" style="float: right; border: 1px solid #bebebe; background-color: #ff6600; width: 120px; height: 25px; color: #fff;" onClick="window.location='options.php?id=new'" id="btnNew" name="btnNew" value="Add New" />
                    </td>
                </tr>
                <tr>
                    <td class="headers" style="width: 100px;">Option ID</td>
                    <td class="headers" style="width: 300px;">Backend Name</td>
                    <td class="headers" style="width: 200px;">Display In Browser</td>
                    <td class="headers" style="width: 150px;">Display In Detailed</td>
                    <td class="headers" style="width: 130px;">Categories</td>
                    <td class="headers" style="width: 40px;"></td>
                </tr>
                
                <?php
					$sql_options  = "SELECT id, BackendTitle, DisplaySearchNav, DisplayProductDetail FROM options WHERE ";
					
					if($id != '') {
						$sql_options .= " id=$id AND ";
					}
					if($name != '') {
						$sql_options .= " BackendTitle LIKE '%$name%' AND ";
					}
					
					if(substr($sql_options, -6) == "WHERE ") {
						$sql_options = substr($sql_options, 0, -6);
					} else {
						$sql_options = substr($sql_options, 0, -4);
					}
					$sql_options .= " ORDER BY id";
					
					$result_options = mysql_query($sql_options);

					$r_num = 1;
					while($row_options=mysql_fetch_array($result_options)) {
						if($r_num == 1) {
							$color = "row1";
							$r_num++;
						} else {
							$color = "row2";
							$r_num = 1;
						}
						
						$sql_cats = "SELECT c.Category FROM category c, options_category o WHERE c.id=o.CategoryID AND o.OptionID=$row_options[id]";
						$result_cats = mysql_query($sql_cats);
						
						$category = '';
						while($row_cats = mysql_fetch_array($result_cats)) {
							$category .= $row_cats["Category"].", ";
						}
						$category = substr($category, 0, -2);
						?>
                        	<tr>
								<td class="<?=$color;?>" style="text-align: center; font-weight: bold;">
                                	<a href="options.php?id=<?=$row_options["id"];?>"><?=$row_options["id"];?></a>
                                </td>  
                                <td class="<?=$color;?>"><?=$row_options["BackendTitle"];?></td>
                                <td class="<?=$color;?>"><?=$row_options["DisplaySearchNav"];?></td>
                                <td class="<?=$color;?>"><?=$row_options["DisplayProductDetail"];?></td>
                                <td class="<?=$color;?>"><?=$category;?></td>
                                <td class="<?=$color;?>" style="text-align: center;"><img class="deloption" style="cursor: pointer; width: 17px;" src="images/delete.png" id="<?=$row_options["id"];?>" /></td>
                            </tr>
                        <?php
					}
				?>
            </table>
			<script>
				$(".deloption").click(function() {
					var del = confirm('Delete Option?');
					
					if(del) {
						$.post('includes/inc_options.php', {"type":"delete", "id":$(this).attr("id")}, 
							function(data) {
								alert(data);
								$("#optionlist").html('<img src="images/loader.gif" />');
								var oid;
								var oname;
				
								if($("#soptionid").val() == 'Search ID') {
									oid = '';
								} else {
									oid = $("#soptionid").val();
								}
				
								if($("#sname").val() == 'Search Option Name') {
									oname = '';
								} else {
									oname = $("#sname").val();
								}
								
								$("#optionlist").load("includes/inc_options.php", {"type":"options", 
														 "id":oid, 
														 "name":oname});
							});
					}
				});
			</script>
            
		<?php
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "delete") {
	
		$id = mysql_real_escape_string($_POST["id"]);
		$sql_del = "DELETE FROM options WHERE id=$id LIMIT 1";
		if(!mysql_query($sql_del)) {
			echo "error deleting option: ".mysql_error();
		} else {
			$sql_delcats = "DELETE FROM options_category WHERE OptionID=$id";
			if(!mysql_query($sql_delcats)) {
				echo "error deleting option category: ".mysql_error();
			} else {
				echo "Option Deleted!";
			}
		}
	
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "details") {
		
		$id = mysql_real_escape_string($_POST["id"]);
		$sql_opt = "SELECT * FROM options WHERE id=$id LIMIT 1";
		$result_opt = mysql_query($sql_opt);
		$row_opt = mysql_fetch_assoc($result_opt);
		
		foreach($row_opt as $key=>$value) {
			$$key = stripslashes($value);
		}
		
		?>
        <form action="" method="post">
        <table cellpadding="5" cellspacing="1" width="980px">
        	<tr>
            	<td colspan="2" class="subheader" style="font-size: 14px;">Option Information</td>
            </tr>
            <tr>
            	<td class="row1" style="width: 50%; font-weight: bold;">Backend Title</td>
                <td class="row1" style="width: 50%; font-weight: bold;">Frontend Title</td>
            </tr>
            <tr>
            	<td class="row2" style="width: 50%;">
                	<input type="hidden" id="id" name="id" value="<?=$id;?>" />
                	<input type="text" class="option" id="BackendTitle" name="BackendTitle" value="<?=$BackendTitle;?>" />
                </td>
                <td class="row2" style="width: 50%;">
                	<input type="text" class="option" id="FrontendTitle" name="FrontendTitle" value="<?=$FrontendTitle;?>" />
                </td>
            </tr>
            <tr>
            	<td class="row1" style="width: 50%; font-weight: bold;">Display In Search Navigation?</td>
                <td class="row1" style="width: 50%; font-weight: bold;">Filter By</td>
            </tr>
            <tr>
            	<td class="row2" style="width: 50%; vertical-align: top;">
                	<select class="option" id="DisplaySearchNav" name="DisplaySearchNav">
                    	<option value="Yes, All Pages" <?php if($DisplaySearchNav=='Yes, All Pages') { echo 'selected="selected"'; }?> >Yes, All Pages</option>
                        <option value="Yes, Specific Category" <?php if($DisplaySearchNav=='Yes, Specific Category') { echo 'selected="selected"'; }?> >Yes, Specific Category</option>
                        <option value="No" <?php if($DisplaySearchNav=='No') { echo 'selected="selected"'; }?> >No</option>
                    </select>
                    
                    <?php
						if($DisplaySearchNav == 'Yes, Specific Category') {
							$display = "block";
						} else {
							$display = "none";
						}
					?>
                    
                    <div id="divcategories" style="display: <?=$display;?>;">
						<?php
                        
                            function subCategories($parent, $optid) {
                                $sql_sub = "SELECT id, Category FROM category WHERE ParentID=$parent";
                                $result_sub = mysql_query($sql_sub);
                                $num_sub = mysql_num_rows($result_sub);
                            
                                if($num_sub>0) {
                                
                                    echo '<ul>';
                                    while($row_sub=mysql_fetch_array($result_sub)) {
                                        $sql_checked = "SELECT CategoryID FROM options_category WHERE CategoryID=$row_sub[id] AND OptionID=$optid LIMIT 1";

                                        $result_checked = mysql_query($sql_checked);
                                        $num_rows = mysql_num_rows($result_checked);
                    
                                        if($num_rows>0) {
                                            $checked = ' checked="checked" ';
                                        } else {
                                            $checked = '';
                                        }
                                        
                                        echo '<li><span class="folder" style="font-size: 15px;"><input type="checkbox" style="width: 12px; height: 12px; background-color: #fff; margin: 0px 5px 0px 5px;" '.$checked.' id="category[]" name="category[]" value="'.$row_sub["id"].'"/>'.$row_sub["Category"].'</span>';
                                        subCategories($row_sub["id"], $optid);
                                        echo "</li>";
                                    }
                                    echo '</ul>';
                                } 
                            }
                    
                            $sql_cat = "SELECT id, Category FROM category WHERE ParentID=0 AND id!=13 AND id!=14";
                            $result_cat = mysql_query($sql_cat);
                    
                            echo '<ul id="categories" class="filetree">';
                            while($row_cat=mysql_fetch_array($result_cat)) {
                                $sql_checked = "SELECT CategoryID FROM options_category WHERE CategoryID=$row_cat[id] AND OptionID=$id LIMIT 1";
								 
                                $result_checked = mysql_query($sql_checked);
                                $num_rows = mysql_num_rows($result_checked);
                    
                                if($num_rows>0) {
                                    $checked = ' checked="checked" ';
                                } else {
                                    $checked = '';
                                }
                    
                                echo '<li><span class="folder" style="font-size: 15px;"><input type="checkbox" style="width: 12px; height: 12px; background-color: #fff; margin: 0px 5px 0px 5px;" '.$checked.' id="category[]" name="category[]" value="'.$row_cat["id"].'"/>'.$row_cat["Category"].'</span>';
                                subCategories($row_cat["id"], $id);
                                echo "</li>";
                            }
                            echo '</ul>';
                        
                        ?>
                    </div>
                    
                </td>
                <td class="row2" style="width: 50%; vertical-align: top;">
                	<select class="option" id="FilterBy" name="FilterBy" style="margin-bottom: 20px;">
                    	<option value="">Select Filter...</option>
                    	<option value="Brand" <?php if($FilterBy=='Brand') { echo 'selected="selected"'; }?> >Brand</option>
                        <option value="Size" <?php if($FilterBy=='Size') { echo 'selected="selected"'; }?> >Size</option>
                        <option value="Color" <?php if($FilterBy=='Color') { echo 'selected="selected"'; }?> >Color</option>
                        <option value="Trim" <?php if($FilterBy=='Trim') { echo 'selected="selected"'; } ?> > Trim</option>
                        <option value="Price" <?php if($FilterBy=='Price') { echo 'selected="selected"'; }?> >Price</option>
                        <option value="Style" <?php if($FilterBy=='Style') { echo 'selected="selected"'; }?> >Style</option>
                    </select>
                    
                    <div id="filterdisplay">
                    	<?php
							if($FilterBy == "Size" || $FilterBy == "Color" || $FilterBy == "Trim") {
								?>
                                	<span style="font-weight: bold;">Display Type:</span><br/>
                                    <select id="DisplayType" name="DisplayType">
                                        <option <?php if($DisplayType=="Dropdown") { echo ' selected="selected" '; } ?> value="Dropdown">Drop down</option>
                                        <option <?php if($DisplayType=="Imageblock") { echo ' selected="selected" '; } ?> value="Imageblock">Image Block</option>
                                    </select>
                                    <br/>
                                    <span style="font-weight: bold; padding-top: 15px;">Category:</span><br/>
                                    <select id="OptionCategory" name="OptionCategory" onchange="getOptions();">
                                        <option value="">Select Category...</option>
                                                <?php
                                    
									if($FilterBy == "Trim") {
										$FilterByType = "Color";
									} else {
										$FilterByType = $FilterBy;
									}
                                    $sql_cats = "SELECT Name FROM attribute_category WHERE Type='".strtolower($FilterByType)."s'";
                                    $result_cats = mysql_query($sql_cats);
                                    while($row_cats = mysql_fetch_array($result_cats)) {
                                        if($OptionCategory == $row_cats["Name"]) {
                                            $selected = ' selected="selected"';
                                        } else {
                                            $selected = '';
                                        }
                                        echo "<option value=\"$row_cats[Name]\" $selected>$row_cats[Name]</option>";
                                    }
                                    ?>
                                    </select>
                
                                    <?php
                                }
                                        
                            ?>
                    </div>
                    
                </td>
            </tr>
            <tr>
            	<td colspan="2" class="row1" style="font-weight: bold;">
                	Filter Options:
                </td>
            </tr>
            <tr>
            	<td colspan="2" class="row2">
                	<div id="filteroptions">
                    	<?php
							
							switch($FilterBy) {
								case "Brand":
									$field = "Manufacturer";
									$table = "manufacturers";
									break;
								case "Size":
									$field = "Size";
									$table = "sizes";
									$cat = $OptionCategory;
									break;
								case "Color":
								case "Trim":
									$field = "Color";
									$table = "colors";
									$cat = $OptionCategory;
									break;
								case "Price":
									$field = "";
									$table = "";
									break;
								case "Style":
									$field = "Style";
									$table = "styles";
									break;
							}
							
							if($FilterBy != 'Price') {
								
								$sql_options = "SELECT id, $field FROM $table";

								if($cat != '') {
									$sql_options .= " WHERE Category='$cat'";	
								}

								$result_options = mysql_query($sql_options);
								
								echo '<table cellpadding="5" cellspacing="1">';
								$n_td = 1;
								while($row_options = mysql_fetch_array($result_options)) {
									if($n_td==1) {
										echo "<tr>";
									}
									
									$sql_checked = "SELECT id FROM options_filter WHERE FilterID=$row_options[id] AND Name='$FilterBy' AND OptionID=$id";
									$result_checked = mysql_query($sql_checked);
									$num_checked = mysql_num_rows($result_checked);
									
									if($num_checked>0) {
										$checked = ' checked="checked"';
									} else {
										$checked = '';
									}
									
									echo '<td style="height: 35px; width: 200px;"><input type="checkbox" style="margin-right: 5px;" id="filter[]" name="filter[]" value="'.$row_options["id"].'" '.$checked.'>'.$row_options[$field].'</td>';
									if($n_td==4){
										echo "</tr>";
										$n_td = 1;
									} else {
										$n_td++;
									}
								}
								
								if($n_td>1) {
									for($i=$n_td;$i<5;$i++) {
										echo "<td></td>";
									}
									echo "</tr>";
								}
								echo '</table>';
							} else {
							?>
                                <script>
									$("#filteroptions").load("includes/inc_options.php", {"type":"Price", "id":"<?=$id;?>"});								
								</script>
                            <?php
							}
						?>
                    </div>
                </td>
            </tr>
            
        </table>
       
        <table cellpadding="5" cellspacing="1" width="980px">
        	<tr>
            	<td><input type="submit" class="customers" style="float: right;" id="btnUpdate" name="btnUpdate" value="Save Changes" /></td>
            </tr>
        </table>
        </form>
        <script>
				$("#categories").treeview();
				$("#DisplaySearchNav").change(function(){
					if($(this).val() == 'Yes, Specific Category') {
						$("#divcategories").fadeIn('slow');
					} else {
						$("#divcategories").fadeOut('slow');
					}
				});
				
				$("#FilterBy").change(function() {
					if($(this).val() == "Size") {
						$("#filterdisplay").html('<img src="images/loader.gif" />');
						$("#filterdisplay").load("includes/inc_options.php", {"type":"displaytype", "optcat":$(this).val()});
						$("#filteroptions").html('');
					}
					if($(this).val() == "Color") {
						$("#filterdisplay").html('<img src="images/loader.gif" />');
						$("#filterdisplay").load("includes/inc_options.php", {"type":"displaytype", "optcat":$(this).val()} );
						$("#filteroptions").html('');
					}
					if($(this).val() == "Price" || $(this).val() == "Brand" || $(this).val() == "Style") {
						$("#filterdisplay").html('');
						$("#filteroptions").html('<img src="images/loader.gif" />');
						$("#filteroptions").load("includes/inc_options.php", {"type":$(this).val(), "id":"<?=$id;?>"});
					}
				});

				function getOptions() {
					$("#filteroptions").html('<img src="images/loader.gif" >');
					$("#filteroptions").load("includes/inc_options.php", {"type":$("#FilterBy").val(), "optcat":$("#OptionCategory").val()});

				}
			</script>
<!-- //////////////////////////////////////////////////////////////////////////////////////// -->        
       
        <?php
	
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "new") {
		?>
		
        	<form action="" method="post">
                <table cellpadding="5" cellspacing="1" width="980px">
                    <tr>
                        <td colspan="2" class="subheader" style="font-size: 14px;">Option Information</td>
                    </tr>
                    <tr>
                        <td class="row1" style="width: 50%; font-weight: bold;">Backend Title</td>
                        <td class="row1" style="width: 50%; font-weight: bold;">Frontend Title</td>
                    </tr>
                    <tr>
                        <td class="row2" style="width: 50%;">
                            <input type="text" class="option" id="BackendTitle" name="BackendTitle" value="" />
                        </td>
                        <td class="row2" style="width: 50%;">
                            <input type="text" class="option" id="FrontendTitle" name="FrontendTitle" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td class="row1" style="width: 50%; font-weight: bold;">Display In Search Navigation?</td>
                        <td class="row1" style="width: 50%; font-weight: bold;">Filter By</td>
                    </tr>
                    <tr>
                        <td class="row2" style="width: 50%; vertical-align: top;">
                            <select class="option" id="DisplaySearchNav" name="DisplaySearchNav">
                            	<option value="Yes, All Pages">Yes, All Pages</option>
                                <option value="Yes, Specific Category">Yes, Specific Category</option>
                                <option value="No">No</option>
                            </select>
                            
                            <div id="divcategories"  style="display: none;">
                            
                            	<?php
								
									function subCategories($parent) {
										$sql_sub = "SELECT id, Category FROM category WHERE ParentID=$parent";
										$result_sub = mysql_query($sql_sub);
										$num_sub = mysql_num_rows($result_sub);
									
										if($num_sub>0) {
										
											echo '<ul>';
											while($row_sub=mysql_fetch_array($result_sub)) {
												echo '<li><span class="folder" style="font-size: 15px;"><input type="checkbox" class="cats" style="width: 12px; height: 12px; background-color: #fff; margin: 0px 5px 0px 5px;" id="category[]" name="category[]" value="'.$row_sub["id"].'"/>'.$row_sub["Category"].'</span>';
												subCategories($row_sub["id"]);
												echo "</li>";
											}
											echo '</ul>';
										} 
									}
							
									$sql_cat = "SELECT id, Category FROM category WHERE ParentID=0 AND id!=13 AND id!=14";
									$result_cat = mysql_query($sql_cat);
							
									echo '<ul id="categories" class="filetree">';
									while($row_cat=mysql_fetch_array($result_cat)) {
										echo '<li><span class="folder" style="font-size: 15px;"><input type="checkbox" class="cats" style="width: 12px; height: 12px; background-color: #fff; margin: 0px 5px 0px 5px;" id="category[]" name="category[]" value="'.$row_cat["id"].'"/>'.$row_cat["Category"].'</span>';
										subCategories($row_cat["id"]);
										echo "</li>";
									}
									echo '</ul>';
								
								?>
                            </div>
                            
                        </td>
                        <td class="row2" style="width: 50%; vertical-align: top;">
                            <select class="option" id="FilterBy" name="FilterBy">
                                <option value="">Select Filter...</option>
                                <option value="Brand">Brand</option>
                                <option value="Size">Size</option>
                                <option value="Color">Color</option>
                                <option value="Trim">Trim</option>
                                <option value="Price">Price</option>
                                <option value="Style">Style</option>
                            </select>

				<div id="filterdisplay"></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="row1" style="font-weight: bold;">
                            Filter Options:
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="row2">
                            <div id="filteroptions">
                            </div>
                        </td>
                    </tr>
                </table>
               
                <table cellpadding="5" cellspacing="1" width="980px">
                    <tr>
                        <td><input type="submit" class="customers" style="float: right;" id="btnSave" name="btnSave" value="Save" /></td>
                    </tr>
                </table>
       		</form>
            <script>
				$("#categories").treeview();
				$("#DisplaySearchNav").change(function(){
					if($(this).val() == 'Yes, Specific Category') {
						$("#divcategories").fadeIn('slow');
					} else {
						$("#divcategories").fadeOut('slow');
					}
				});
				
				$("#FilterBy").change(function() {
					if($(this).val() == "Size") {
						$("#filterdisplay").html('<img src="images/loader.gif" />');
						$("#filterdisplay").load("includes/inc_options.php", {"type":"displaytype", "optcat":$(this).val()});
						$("#filteroptions").html('');
					}
					if($(this).val() == "Color" || $(this).val() == "Trim") {
						$("#filterdisplay").html('<img src="images/loader.gif" />');
						$("#filterdisplay").load("includes/inc_options.php", {"type":"displaytype", "optcat":$(this).val()} );
						$("#filteroptions").html('');
					}
					if($(this).val() == "Price" || $(this).val() == "Brand" || $(this).val() == "Style") {
						$("#filterdisplay").html('');
						$("#filteroptions").html('<img src="images/loader.gif" />');
						$("#filteroptions").load("includes/inc_options.php", {"type":$(this).val()});
					}
				});

				function getOptions() {
					$("#filteroptions").html('<img src="images/loader.gif" >');
					$("#filteroptions").load("includes/inc_options.php", {"type":$("#FilterBy").val(), "optcat":$("#OptionCategory").val()});

				}
			</script>
        
		<?php
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"]=="Brand") {
	
		$sql_filter = "SELECT id, Manufacturer FROM manufacturers ORDER BY Manufacturer";
		$result_filter = mysql_query($sql_filter);
		
		$filter = '<table cellpadding="5" cellspacing="1">';
		$n_td = 1;
		while($row_filter = mysql_fetch_array($result_filter)) {
			if($n_td==1) {
				$filter .= "<tr>";	
			}
			$filter .= '<td style="height: 35px; width: 200px;"><input type="checkbox" style="margin-right: 5px;" id="filter[]" name="filter[]" value="'.$row_filter["id"].'">'.$row_filter["Manufacturer"].'</td>';
			
			if($n_td==4){
				$filter .= "</tr>";
				$n_td = 1;
			} else {
				$n_td++;
			}
		}
		
		if($n_td>1) {
			for($i=$n_td;$i<5;$i++) {
				$filter .= "<td></td>";
			}
			$filter .= "</tr>";
		}
		
		$filter .= '</table>';
		echo $filter;
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "Price") {
			$id = mysql_real_escape_string($_POST["id"]);
		?>
        
        	<table cellpadding="5" cellpadding="1" style="width: 40%; float: left;">
            	<tr>
                	<td colspan="2" class="subheader">Enter Price Range:</td>
                </tr>
                <tr>
                	<td style="font-weight: bold;">From</td>
                    <td><input type="text" class="option" id="pricefrom" name="pricefrom" /></td>
                </tr>
                <tr>
                	<td style="font-weight: bold;">To</td>
                    <td><input type="text" class="option" id="priceto" name="priceto" /></td>
                </tr>
                <tr>
                	<td colspan="2">
                    	<input type="button" class="customers" style="float: right;" id="addprice" name="addprice" value="Add Price" />
                    </td>
                </tr>
            </table>
            <table cellpadding="5" cellpadding="1" style="width: 50%; float: right;">
            	<tr>
                	<td class="subheader">Current Options</td>
                </tr>
                <tr>
                	<td>
                		<div id="divPriceOptions"></div>
                    </td>
                </tr>
            </table>
            <script>
				$("#divPriceOptions").load("includes/inc_options.php", {"type":"viewprice", "id":"<?=$id;?>"});
				$("#addprice").click(function() {
					$("#divPriceOptions").html('<img src="images/loader.gif" />');
					$.post("includes/inc_options.php", {"type":"addprice", "id":"<?=$id;?>", "fromprice": $("#pricefrom").val(), "toprice": $("#priceto").val()}, 
						function(data) {
							$("#divPriceOptions").load("includes/inc_options.php", {"type":"viewprice", "id":"<?=$id;?>"});
						}
					);
				});
				function removeprice(id) {
				
					var del = confirm('remove price option?');
					
					if(del) {
						$("#divPriceOptions").html('<img src="images/loader.gif" />');
						$("#divPriceOptions").load("includes/inc_options.php", {"type":"deleteprice", "id":id}, function() {
							$("#divPriceOptions").load("includes/inc_options.php", {"type":"viewprice", "id": "<?=$id;?>"});
						});
					}
				}
			</script>
        <?php
	
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "Size") {
		
		$sizecat = mysql_real_escape_string($_POST["optcat"]);
		$sql_filter = "SELECT id, Size FROM sizes wHERE Category='$sizecat'";
		$result_filter = mysql_query($sql_filter);
		
		$filter = '<table cellpadding="5" cellspacing="1">';
		$n_td = 1;
		while($row_filter = mysql_fetch_array($result_filter)) {
			if($n_td==1) {
				$filter .= "<tr>";	
			}
			$filter .= '<td style="height: 35px; width: 200px;"><input type="checkbox" style="margin-right: 5px;" id="filter[]" name="filter[]" value="'.$row_filter["id"].'">'.$row_filter["Size"].'</td>';
			
			if($n_td==4){
				$filter .= "</tr>";
				$n_td = 1;
			} else {
				$n_td++;
			}
		}
		
		if($n_td>1) {
			for($i=$n_td;$i<5;$i++) {
				$filter .= "<td></td>";
			}
			$filter .= "</tr>";
		}
		
		$filter .= '</table>';
		echo $filter;
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "Color" || $_POST["type"] == "Trim") {
		
		$colorcat = mysql_real_escape_string($_POST["optcat"]);
		$sql_filter = "SELECT id, Color FROM colors WHERE Category='$colorcat'";
		$result_filter = mysql_query($sql_filter);
		
		$filter = '<table cellpadding="5" cellspacing="1">';
		$n_td = 1;
		while($row_filter = mysql_fetch_array($result_filter)) {
			if($n_td==1) {
				$filter .= "<tr>";	
			}
			$filter .= '<td style="height: 35px; width: 200px;"><input type="checkbox" style="margin-right: 5px;" id="filter[]" name="filter[]" value="'.$row_filter["id"].'">'.$row_filter["Color"].'</td>';
			
			if($n_td==4){
				$filter .= "</tr>";
				$n_td = 1;
			} else {
				$n_td++;
			}
		}
		
		if($n_td>1) {
			for($i=$n_td;$i<5;$i++) {
				$filter .= "<td></td>";
			}
			$filter .= "</tr>";
		}
		
		$filter .= '</table>';
		echo $filter;
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"]=="Style") {
	
		$sql_filter = "SELECT id, Style FROM styles ORDER BY Style";
		$result_filter = mysql_query($sql_filter);
		
		$filter = '<table cellpadding="5" cellspacing="1">';
		$n_td = 1;
		while($row_filter = mysql_fetch_array($result_filter)) {
			if($n_td==1) {
				$filter .= "<tr>";	
			}
			$filter .= '<td style="height: 35px; width: 200px;"><input type="checkbox" style="margin-right: 5px;" id="filter[]" name="filter[]" value="'.$row_filter["id"].'">'.$row_filter["Style"].'</td>';
			
			if($n_td==4){
				$filter .= "</tr>";
				$n_td = 1;
			} else {
				$n_td++;
			}
		}
		
		if($n_td>1) {
			for($i=$n_td;$i<5;$i++) {
				$filter .= "<td></td>";
			}
			$filter .= "</tr>";
		}
		
		$filter .= '</table>';
		echo $filter;
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "addprice") {
	
		$sql_price = "INSERT INTO options_filter(OptionID, Name, PriceRange) VALUES($_POST[id], 'Price', '".$_POST["fromprice"]."-".$_POST["toprice"]."')";
		mysql_query($sql_price);
		mysql_close($conn);

		exit();
	}
	
	if($_POST["type"] == "viewprice") {
		
		if($_POST["id"] != '') {
			$id = mysql_real_escape_string($_POST['id']);
		$sql_price = "SELECT id, PriceRange FROM options_filter WHERE Name='Price' AND OptionID=$id]";
		$result_price = mysql_query($sql_price);
		?>
        	
            <table cellpadding="5" cellspacing="1" width="100%">
				<?php
                    while($row_price=mysql_fetch_array($result_price)) {
                        echo "<tr><td class=\"row1\">$row_price[PriceRange] [<a href=\"#\" onclick=\"removeprice($row_price[id]);\">remove</a>]</td></tr>";
                    }
                ?>
            </table>
        
        <?php
		}	
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "deleteprice") {
	
		$id = mysql_real_escape_string($_POST["id"]);
		$sql_del="DELETE FROM options_filter WHERE id=$id";
		mysql_query($sql_del);
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "displaytype") {
		if($_POST["optcat"] == "Trim") {
			$cat = "colors";
		} else {
			$cat = strtolower(mysql_real_escape_string($_POST["optcat"]))."s";
		}
	
		?>
			<span style="font-weight: bold;">Display Type:</span><br/>
            		<select id="DisplayType" name="DisplayType">
            			<option value="Dropdown">Drop down</option>
                		<option value="Imageblock">Image Block</option>
            		</select>
			<br/>
			<span style="font-weight: bold; padding-top: 15px;">Category:</span><br/>
			<select id="OptionCategory" name="OptionCategory" onChange="getOptions();">
				<option value="">Select Category...</option>
				<?php
					$sql_cats = "SELECT Name FROM attribute_category WHERE Type='$cat'";
					echo $sql_cats;
					$result_cats = mysql_query($sql_cats);
					while($row_cats = mysql_fetch_array($result_cats)) {
						echo "<option value=\"$row_cats[Name]\">$row_cats[Name]</option>";
					}
				?>
			</select>
        <?php
		mysql_close($conn);
		exit();
	}
?>