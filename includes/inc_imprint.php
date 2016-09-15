<?php
/**********************************
 * Impriting details file 
 *
 * Version: 1.2
 * By: Richard Tuttle
 * Last Updated: 06 June 2013
 **********************************/
 
	session_start();
	require_once '../cpadmin/includes/db.php';

	if($_POST["type"] == "Options") {
		$prodID   = $_POST["prodID"];
		$optType  = $_POST["optType"];
		$optType2 = "";
		$optLoc   = $_POST["optLoc"];
		$imgLoc = $optLoc."_main";
		$impQty = intval($_SESSION["imprintConfig"]["qty"]);
		if($impQty == '') {
			$impQty = intval($_SESSION["singleitems"]["qty"]);
		}
		
		// SET SIZE INFORMATION FOR MULTIPLE QTY'S //////////////////////////////////////////////
		$opt1Text = "";
		$opt2Text = "";
		$sql_prodtype = "SELECT ProductType, option_seting_1 FROM products WHERE id=$prodID LIMIT 1";
		$result_prodtype = mysql_query($sql_prodtype);
		$row_prodtype = mysql_fetch_assoc($result_prodtype);
		$prodType = $row_prodtype["ProductType"];
		$prodSingle = $row_prodtype["option_seting_1"];
		
		if($prodType == "Bundle") {
			foreach($_SESSION["bundleItems"]["items"] as $key => $value) {
				$sql_impType = "SELECT ImprintType FROM products WHERE id=$key LIMIT 1";
				$result_impType = mysql_query($sql_impType);
				$row_impType = mysql_fetch_assoc($result_impType);
				$colorSKU = $_SESSION["bundleItems"][$key]["color"];
				
				if(($optLoc == "Front" || $optLoc == "Back") && $row_impType["ImprintType"] == "Shirt") {
					$n = 0;
					foreach($value["size"] as $sizeval) {
						$sql_size = "SELECT Size FROM product_options WHERE ProductID=$key AND ColorSKU='$colorSKU' AND SizeSKU='$sizeval' LIMIT 1";
						$result_size = mysql_query($sql_size);
						$row_size = mysql_fetch_assoc($result_size);
						
						$opt1Text .= '<input type="hidden" name="'.$optLoc.'1_sizeName[]" value="'.$row_size["Size"].'" /><p class="sizeText">'.$row_size["Size"].'</p>';
						$opt1Text .= '<input size="3" type="text" class="textOpt" id="'.$optLoc.'_sn'.$n.'" name="'.$optLoc.'1_sizeValue[]" value="" />';
						$opt2Text .= '<input type="hidden" name="'.$optLoc.'2_sizeName[]" value="'.$row_size["Size"].'" /><p class="sizeText">'.$row_size["Size"].'</p>';
						$opt2Text .= '<input type="text" class="textOpt" id="'.$optLoc.'_sn'.$n.'" name="'.$optLoc.'2_sizeValue[]" value="" />';
						$n++;
					}
				} elseif($optLoc == "Short" && $row_impType["ImprintType"] == "Short") {
					$n = 0;
					foreach($value["size"] as $sizeval) {
						$sql_size = "SELECT Size FROM product_options WHERE ProductID=$key AND ColorSKU='$colorSKU' AND SizeSKU='$sizeval' LIMIT 1";
						$result_size = mysql_query($sql_size);
						$row_size = mysql_fetch_assoc($result_size);
						
						$opt1Text .= '<input type="hidden" name="'.$optLoc.'1_sizeName[]" value="'.$row_size["Size"].'" /><p class="sizeText">'.$row_size["Size"].'</p>';
						$opt1Text .= '<input type="text" class="textOpt" id="'.$optLoc.'_sn'.$n.'" name="'.$optLoc.'1_sizeValue[]" value="" />';
						$opt2Text .= '<input type="hidden" name="'.$optLoc.'2_sizeName[]" value="'.$row_size["Size"].'" /><p class="sizeText">'.$row_size["Size"].'</p>';
						$opt2Text .= '<input type="text" class="textOpt" id="'.$optLoc.'_sn'.$n.'" name="'.$optLoc.'2_sizeValue[]" value="" />';
						$n++;
					}
				} elseif($optLoc == "Socks" && $row_impType["ImprintType"] == "Socks") {
					$n = 0;
					foreach($value["size"] as $sizeval) {
						$sql_size = "SELECT Size FROM product_options WHERE ProductID=$key AND ColorSKU='$colorSKU' AND SizeSKU='$sizeval' LIMIT 1";
						$result_size = mysql_query($sql_size);
						$row_size = mysql_fetch_assoc($result_size);
						
						$opt1Text .= '<input type="hidden" name="'.$optLoc.'1_sizeName[]" value="'.$row_size["Size"].'" /><p class="sizeText">'.$row_size["Size"].'</p>';
						$opt1Text .= '<input type="text" class="textOpt" id="'.$optLoc.'_sn'.$n.'"  name="'.$optLoc.'1_sizeValue[]" value="" />';
						$opt2Text .= '<input type="hidden" name="'.$optLoc.'2_sizeName[]" value="'.$row_size["Size"].'" /><p class="sizeText">'.$row_size["Size"].'</p>';
						$opt2Text .= '<input type="text" class="textOpt" id="'.$optLoc.'_sn'.$n.'"  name="'.$optLoc.'2_sizeValue[]" value="" />';
						$n++;
					}
				}
			}			
		} elseif($prodType == "Product" && $prodSingle == "2") {
			$sql_impType = "SELECT ImprintType FROM products WHERE id=$prodID LIMIT 1";
			$result_impType = mysql_query($sql_impType);
			$row_impType = mysql_fetch_assoc($result_impType);
			$impType = $row_impType["ImprintType"];
			//:::::::::::::::::::::::::::::::
			if(($optLoc == "Front" || $optLoc == "Back") && $impType == "Shirt") {
				$s_count = sizeof($_SESSION["singleitems"]["items"][$prodID]["size"]);
				if($s_count>0) {
					for($s=0;$s<$s_count;$s++) {
						$s_size  = $_SESSION["singleitems"]["items"][$prodID]["size"][$s];
						$s_color = $_SESSION["singleitems"]["items"][$prodID]["color"][$s];
						$sql_colsiz = "SELECT Size, Color FROM product_options WHERE ProductID=$prodID AND ColorSKU='$s_color' AND SizeSKU='$s_size' LIMIT 1";
						$result_colsiz = mysql_query($sql_colsiz);
						$row_colsiz = mysql_fetch_assoc($result_colsiz);
						
						$opt1Text .= '<input type="hidden" name="'.$optLoc.'1_sizeName[]" value="'.$row_colsiz["Size"].":".$row_colsiz["Color"].'" /><p class="sizeText">'.$row_colsiz["Size"].": ".$row_colsiz["Color"].'</p>';
						$opt1Text .= '<input size="3" type="text" class="textOpt" id="'.$optLoc.'_sn'.$s.'" name="'.$optLoc.'1_sizeValue[]" value="" />';
						$opt2Text .= '<input type="hidden" name="'.$optLoc.'2_sizeName[]" value="'.$row_colsiz["Size"].":".$row_colsiz["Color"].'" /><p class="sizeText">'.$row_colsiz["Size"].": ".$row_colsiz["Color"].'</p>';
						$opt2Text .= '<input type="text" class="textOpt" id="'.$optLoc.'_sn'.$s.'" name="'.$optLoc.'2_sizeValue[]" value="" />';
					}
					$n = $s;
				}
			} elseif($optLoc == "Short" && $impType == "Short") {
				$s_count = sizeof($_SESSION["singleitems"]["items"][$prodID]["size"]);
				if($s_count>0) {
					for($s=0; $s<$s_count; $s++) {
						$s_size  = $_SESSION["singleitems"]["items"][$prodID]["size"][$s];
						$s_color = $_SESSION["singleitems"]["items"][$prodID]["color"][$s];
						$sql_colsiz = "SELECT Size, Color FROM product_options WHERE ProductID=$prodID AND ColorSKU='$s_color' AND SizeSKU='$s_size' LIMIT 1";
						$result_colsiz = mysql_query($sql_colsiz);
						$row_colsiz = mysql_fetch_assoc($result_colsiz);
						
						$opt1Text .= '<input type="hidden" name="'.$optLoc.'1_sizeName[]" value="'.$row_colsiz["Size"].":".$row_colsiz["Color"].'" /><p class="sizeText">'.$row_colsiz["Size"].": ".$row_colsiz["Color"].'</p>';
						$opt1Text .= '<input type="text" class="textOpt" id="'.$optLoc.'_sn'.$s.'" name="'.$optLoc.'1_sizeValue[]" value="" />';
						$opt2Text .= '<input type="hidden" name="'.$optLoc.'2_sizeName[]" value="'.$row_colsiz["Size"].":".$row_colsiz["Color"].'" /><p class="sizeText">'.$row_colsiz["Size"].": ".$row_colsiz["Color"].'</p>';
						$opt2Text .= '<input type="text" class="textOpt" id="'.$optLoc.'_sn'.$s.'" name="'.$optLoc.'2_sizeValue[]" value="" />';
					}
					$n = $s;
				}
			} elseif($optLoc == "Socks" && $impType == "Socks") {
				$s_count = sizeof($_SESSION["singleitems"]["items"][$prodID]["size"]);
				if($s_count>0) {
					for($s=0; $s<$s_count; $s++) {
						$s_size  = $_SESSION["singleitems"]["items"][$prodID]["size"][$s];
						$s_color = $_SESSION["singleitems"]["items"][$prodID]["color"][$s];
						$sql_colsiz = "SELECT Size, Color FROM product_options WHERE ProductID=$prodID AND ColorSKU='$s_color' AND SizeSKU='$s_size' LIMIT 1";
						$result_colsiz = mysql_query($sql_colsiz);
						$row_colsiz = mysql_fetch_assoc($result_colsiz);
						
						$opt1Text .= '<input type="hidden" name="'.$optLoc.'1_sizeName[]" value="'.$row_colsiz["Size"].":".$row_colsiz["Color"].'" /><p class="sizeText">'.$row_colsiz["Size"].": ".$row_colsiz["Color"].'</p>';
						$opt1Text .= '<input type="text" class="textOpt" id="'.$optLoc.'_sn'.$s.'" name="'.$optLoc.'1_sizeValue[]" value="" />';
						$opt2Text .= '<input type="hidden" name="'.$optLoc.'2_sizeName[]" value="'.$row_colsiz["Size"].":".$row_colsiz["Color"].'" /><p class="sizeText">'.$row_colsiz["Size"].": ".$row_colsiz["Color"].'</p>';
						$opt2Text .= '<input type="text" class="textOpt" id="'.$optLoc.'_sn'.$s.'" name="'.$optLoc.'2_sizeValue[]" value="" />';
					}
					$n = $s;
				}
			}
			//::::::::::::::::::::::::::::::::::
		} else {
			echo "Qty: ".$impQty."<br/><br/>";
			$colorSKU = $_SESSION["imprintConfig"]["color"];
			$sizeSKU  = $_SESSION["imprintConfig"]["size"];
			$sql_size = "SELECT Size FROM product_options WHERE ProductID=$prodID AND ColorSKU='$colorSKU' AND SizeSKU='$sizeSKU' LIMIT 1";
			$result_size = mysql_query($sql_size);
			$row_size = mysql_fetch_assoc($result_size);
			
			$opt1Text = '';
			$opt2Text = '';
			
			for($q=1; $q<=$impQty; $q++) {
				$opt1Text .= '<input type="hidden" name="'.$optLoc.'1_sizeName[]" value="'.$sizeSKU.'" /><p class="sizeText">'.$row_size["Size"].'</p>';
				$opt1Text .= '<input size="3" type="text" class="textOpt" id="'.$optLoc.'_sn'.$q.'" name="'.$optLoc.'1_sizeValue[]" value="" />';
				$opt2Text .= '<input type="hidden" name="'.$optLoc.'2_sizeName[]" value="'.$sizeSKU.'" /><p class="sizeText">'.$row_size["Size"].'</p>';
				$opt2Text .= '<input type="text" class="textOpt" id="'.$optLoc.'_sn'.$q.'" name="'.$optLoc.'2_sizeValue[]" value="" />';
			}
			$n = $q;
		}
		// END SIZE INFORMATION FOR MULTIPLE QTY'S //////////////////////////////////////////////
		
		if($optLoc == "Front") {
			switch($optType) {
				case "logo_name":
					$optType  = "name";
					$optType2 = "logo";
					$imgLoc2 = $optLoc."_pocket";
					break;
				case "name_number":
					$optType  = "name";
					$optType2 = "number";
					$imgLoc2 = $optLoc."_pocket";
					break;
				case "logo_number":
					$optType  = "logo";
					$optType2 = "number";
					$imgLoc2 = $optLoc."_pocket";
					break;
				case "pocketlogo":
					$imgLoc = $optLoc."_pocket";
					break;
				case "chestlogo":
					break;
				case "pocketlogo_number":
					$optType = "pocketlogo";
					$optType2 = "number";
					$imgLoc = $optLoc."_pocket";
					$imgLoc2 = $optLoc."_main";
					break;
				case "chestlogo_number":
					$optType = "chestlogo";
					$optType2 = "number";
					$imgLoc2 = $optLoc."_pocket";
					break;
			}
		} elseif($optLoc == "Back") {
			switch($optType) {
				case "name":
					$imgLoc = $optLoc."_name";
					break;
				case "logo_name":
					$optType  = "logo";
					$optType2 = "name";
					$imgLoc2 = $optLoc."_name";
					break;
				case "name_number":
					$optType  = "number";
					$optType2 = "name";
					$imgLoc2 = $optLoc."_name";
					break;
				case "logo_number":
					$optType  = "logo";
					break;
			}
		}
		
		$optTypeQry = $optType;
		switch($optTypeQry) {
			case "chestlogo":
				$optTypeQry = "logo";
				break;
			case "pocketlogo":
				$optTypeQry = "pocket";
				break;
		}

		$sql_opts = "SELECT o.id FROM imprint_options o, imprint_images i, products p WHERE p.ImprintCatID = o.ImprintCategory AND o.id = i.OptionID AND o.Type = '$optTypeQry' AND p.id=$prodID AND i.Type ='$optLoc' LIMIT 1";
		$result_opts = mysql_query($sql_opts);
		$row_opts = mysql_fetch_assoc($result_opts);
		$sql_imgs = "SELECT * FROM imprint_images WHERE OptionID=$row_opts[id] AND Type='$optLoc' AND Parent=0";
		$result_imgs = mysql_query($sql_imgs);
		?>
        <input type="hidden" id="<?=$optLoc;?>opt1Loc" name="<?=$optLoc;?>opt1Loc" value="<?=$optLoc;?>" />
        <input type="hidden" id="<?=$optLoc;?>opt1Image" name="<?=$optLoc;?>opt1Image" value="" />
        <input type="hidden" id="<?=$optLoc;?>opt1Type" name="<?=$optLoc;?>opt1Type" value="<?=$optType;?>" />
        <input type="hidden" id="<?=$optLoc;?>opt1Color" name="<?=$optLoc;?>opt1Color" value="" />
        <div class="selImage">
        	<div class="selImageHdr">Step 1: Select Logo</div>
			<img class="selImgHover" id="<?=$optLoc;?>_lrgImage" src="" />
			<?php
            while($row_imgs = mysql_fetch_array($result_imgs)) {
                echo '<img class="'.$optLoc.'_optImages" id="'.$row_imgs["id"].'" src="cpadmin/logo/'.$row_imgs["Image"].'" />';	
            }
            ?>
        </div>
        <div class="selColor">
        	<div class="selImageHdr">Step 2: Select Color</div>
            <?php
            	if ($optLoc == "Back") {
            		echo '<div id="'.$optLoc.'_colors">Select Number first</div>';
            	} else {
            		echo '<div id="'.$optLoc.'_colors">Select Logo</div>';
            	}
            ?>
        </div>
        <?php
			$step = 3;
			if($optType == "name" || $optType == "number") {
				?>
                <div class="selName">
                	<div class="selNameHdr">Step <?=$step;?>: Enter <?=ucfirst($optType);?></div>
                    	<?=$opt1Text;?>
						<!-- input type="button" class="continue repeat" id="<?=$optLoc;?>_rs" name="<?=$optLoc;?>_rs" value="Repeat these Entries" / -->
                    </div>
                </div>
				<?php
				$step++;
			}
			
			if($optType == "chestlogo" || $optType == "pocketlogo") {
				?>
					<div class="selTeam">
						<div class="selTeamHdr">Step <?=$step;?>: Enter Team Name</div>
						<input type="text" class="textOpt optFull" id="<?=$optLoc;?>_opt1Team" name="<?=$optLoc;?>_opt1Team" value="" />
					</div>
				<?php
			}
		?>
        <script>
			$(".<?=$optLoc;?>_optImages").click(function() {
				$("#<?=$optLoc;?>_colors").html('<img src="images/loader.gif" />');
				$("#<?=$optLoc;?>_colors").load("includes/inc_imprint.php",{
					"type":"colors", 
					"optID":$(this).attr("id"), 
					"imgLoc":"<?=$imgLoc;?>", 
					"optValBox":"opt1Image", 
					"optValColor":"opt1Color"
				});
				$("#<?=$imgLoc;?>").attr("src", $(this).attr("src"));
				$("#<?=$optLoc;?>opt1Image").val($(this).attr("src"));
			});
			$(".<?=$optLoc;?>_optImages").hover(
				function(){
					$("#<?=$optLoc;?>_lrgImage").attr('src', $(this).attr("src"));
					$("#<?=$optLoc;?>_lrgImage").css('display','block');
					
					$("#<?=$optLoc;?>_lrgImage").css('left', $(this).position().left+40);
					$("#<?=$optLoc;?>_lrgImage").css('top', $(this).position().top+40);
				}, function(){
					$("#<?=$optLoc;?>_lrgImage").attr('src', '');
					$("#<?=$optLoc;?>_lrgImage").css('display','none');
				}
			);
		</script>
        <?php
		if($optType2 != "") {
			?>
            <div class="clear"></div>
            <hr />
            <input type="hidden" id="<?=$optLoc;?>opt2Loc" name="<?=$optLoc;?>opt2Loc" value="<?=$optLoc;?>" />
            <input type="hidden" id="<?=$optLoc;?>opt2Image" name="<?=$optLoc?>opt2Image" value="" />
            <input type="hidden" id="<?=$optLoc;?>opt2Type" name="<?=$optLoc?>opt2Type" value="<?=$optType2;?>" />
            <input type="hidden" id="<?=$optLoc;?>opt2Color" name="<?=$optLoc;?>opt2Color" value="" />
            <?php
			$sql_opts2 = "SELECT o.id FROM imprint_options o, products p WHERE p.ImprintCatID = o.ImprintCategory AND o.Type = '$optType2' LIMIT 1";
			$result_opts2 = mysql_query($sql_opts2);
			$row_opts2 = mysql_fetch_assoc($result_opts2);
			$sql_imgs2 = "SELECT * FROM imprint_images WHERE OptionID=$row_opts2[id] AND Type='$optLoc' AND Parent=0";
			$result_imgs2 = mysql_query($sql_imgs2);
			?>
            <div class="selImage">
            	<div class="selImageHdr">Select <?=$optType2;?></div>
                <?php
					while($row_imgs2 = mysql_fetch_array($result_imgs2)) {
						echo '<img class="optImages2" id="'.$row_imgs2["id"].'" src="cpadmin/logo/'.$row_imgs2["Image"].'" />';	
					}
				?>
            </div>
            <div class="selColor">
            	<div class="selImageHdr">Select Color</div>
                <div id="<?=$optLoc;?>_colors2">Select <?=$optType2;?> first</div>
            </div>
            <?php
				if($optType2 == "name" || $optType2 == "number") {
					?>
					<div class="selName">
						<div class="selNameHdr">Enter <?=ucfirst($optType2);?></div>
							<?=$opt2Text;?>
							<input type="button" class="continue repeat" id="<?=$optLoc;?>_rs" name="<?=$optLoc;?>_rs" value="Repeat these Entries" />
						</div>
					</div>
					<?php
				}
				
				if($optType2 == "chestlogo" || $optType2 == "pocketlogo") {
					?>
						<div class="selTeam">
							<div class="selTeamHdr">Enter Team Name</div>
							<input type="text" class="textOpt optFull" id="<?=$optLoc;?>_opt2Team" name="<?=$optLoc;?>_opt2Team" value="" />
						</div>
					<?php
				}
			?>
            <script>
				$(".optImages2").click(function() {
					$("#<?=$optLoc;?>_colors2").html('<img src="images/loader.gif" />');
					$("#<?=$optLoc;?>_colors2").load("includes/inc_imprint.php",{
						"type":"colors", 
						"optID":$(this).attr("id"), 
						"imgLoc":"<?=$imgLoc2;?>",
						"optValBox":"opt2Image", 
						"optValColor":"opt2Color"
					});
					$("#<?=$imgLoc2;?>").attr("src", $(this).attr("src"));
					$("#<?=$optLoc;?>opt2Image").val($(this).attr("src"));
				});
			</script>
        <?php
		} 
		?>
		<script>
			$(".repeat").click(function() {
				var n = <?=$n;?>;
				var id = $(this).attr("id");
				id = id.replace("_rs","");
				
				for(var i=0;i<n;i++) {
					$("input[id$='_sn"+i+"']").val($("#"+id+"_sn"+i).val());
				}
			});
		</script>
		<?php
		mysql_close($conn);
		exit();
	}
	// EDIT ::::::::::::::::::::::
	if($_POST["type"] == "EditOptions") {
		$prodID   = $_POST["prodID"];
		$optType  = $_POST["optType"];
		$optType2 = "";
		$optLoc   = $_POST["optLoc"];
		$scid     = $_POST["scid"];
		$imgLoc = $optLoc."_main";
		
		// SET SIZE INFORMATION FOR MULTIPLE QTY'S //////////////////////////////////////////////
		$opt1Text = "";
		$opt2Text = "";
		
		$sql_scimp = "SELECT * FROM imprint_shopping_cart WHERE CartID=$scid AND Opt1Loc='$optLoc' LIMIT 1";
		$result_scimp = mysql_query($sql_scimp);
		$row_scimp = mysql_fetch_assoc($result_scimp);
		
		$arTextVals1 = explode("|", $row_scimp["Opt1Text"]);
		$arTextVals2 = explode("|", $row_scimp["Opt2Text"]);
		
		unset($arTextVals1[count($arTextVals1)-1]);
		unset($arTextVals2[count($arTextVals2)-1]);
		
		$sql_prodtype = "SELECT ProductType, option_seting_1 FROM products WHERE id=$prodID LIMIT 1";
		$result_prodtype = mysql_query($sql_prodtype);
		$row_prodtype = mysql_fetch_assoc($result_prodtype);
		$prodType = $row_prodtype["ProductType"];
		$prodSingle = $row_prodtype["option_setting_1"];
		
		if($prodType == "Bundle") {
			$sql_bitems = "SELECT DISTINCT ProductID, RootSKU FROM shopping_cart WHERE BundleID=$scid";
			$result_bitems = mysql_query($sql_bitems);
			
			while($row_bitems = mysql_fetch_array($result_bitems)) {
				$sql_impType = "SELECT ImprintType FROM products WHERE id=$row_bitems[ProductID] LIMIT 1";
				$result_impType = mysql_query($sql_impType);
				$row_impType = mysql_fetch_assoc($result_impType);
				
				if(($optLoc == "Front" || $optLoc == "Back") && $row_impType["ImprintType"] == "Shirt") {
					$n = 0;
					$size1 = "";
					foreach($arTextVals1 as $text1val) {
						$arText1Temp = explode("(", $text1val);
						$size1 = str_replace("(", "",str_replace(") ","",$arText1Temp[1]));
						$opt1Text .= '<input type="hidden" name="'.$optLoc.'1_sizeName[]" value="'.trim($arText1Temp[0]).'" /><p class="sizeText">'.trim($arText1Temp[0]).'</p>';
						$opt1Text .= '<input size="3" type="text" class="textOpt" id="'.$optLoc.'_sn'.$n.'" name="'.$optLoc.'1_sizeValue[]" value="'.$size1.'" />';
						$n++;
					}
					
					$n2 = 0;
					$size2 = "";
					foreach($arTextVals2 as $text2val) {
						$arText2Temp = explode("(", $text2val);
						$size2 = str_replace("(", "",str_replace(") ","",$arText2Temp[1]));
						$opt2Text .= '<input type="hidden" name="'.$optLoc.'2_sizeName[]" value="'.trim($arText2Temp[0]).'" /><p class="sizeText">'.trim($arText2Temp[0]).'</p>';
						$opt2Text .= '<input type="text" class="textOpt" id="'.$optLoc.'_sn'.$n2.'" name="'.$optLoc.'2_sizeValue[]" value="'.$size2.'" />';
						$n2++;
					}
				} elseif($optLoc == "Short" && $row_impType["ImprintType"] == "Short") {
					$n=0;
					$size1 = "";
					foreach($arTextVals1 as $text1val) {
						$arText1Temp = explode("(", $text1val);
						$size1 = str_replace("(", "",str_replace(") ","",$arText1Temp[1]));
						$opt1Text .= '<input type="hidden" name="'.$optLoc.'1_sizeName[]" value="'.trim($arText1Temp[0]).'" /><p class="sizeText">'.trim($arText1Temp[0]).'</p>';
						$opt1Text .= '<input size="3" type="text" class="textOpt" id="'.$optLoc.'_sn'.$n.'" name="'.$optLoc.'1_sizeValue[]" value="'.$size1.'" />';
						$n++;
					}
					
					$n2 = 0;
					$size2 = "";
					foreach($arTextVals2 as $text2val) {
						$arText2Temp = explode("(", $text2val);
						$size2 = str_replace("(", "",str_replace(") ","",$arText2Temp[1]));
						$opt2Text .= '<input type="hidden" name="'.$optLoc.'2_sizeName[]" value="'.trim($arText2Temp[0]).'" /><p class="sizeText">'.trim($arText2Temp[0]).'</p>';
						$opt2Text .= '<input type="text" class="textOpt" id="'.$optLoc.'_sn'.$n2.'" name="'.$optLoc.'2_sizeValue[]" value="'.$size2.'" />';
						$n2++;
					}
				} elseif($optLoc == "Socks" && $row_impType["ImprintType"] == "Socks") {
					$n = 0;
					$size1 = "";
					foreach($arTextVals1 as $text1val) {
						$arText1Temp = explode("(", $text1val);
						$size1 = str_replace("(", "",str_replace(") ","",$arText1Temp[1]));
						$opt1Text .= '<input type="hidden" name="'.$optLoc.'1_sizeName[]" value="'.trim($arText1Temp[0]).'" /><p class="sizeText">'.trim($arText1Temp[0]).'</p>';
						$opt1Text .= '<input size="3" type="text" id="'.$optLoc.'_sn'.$n.'" class="textOpt" name="'.$optLoc.'1_sizeValue[]" value="'.$size1.'" />';
						$n++;
					}
					
					$n2 = 0;
					$size2 = "";
					foreach($arTextVals2 as $text2val) {
						$arText2Temp = explode("(", $text2val);
						$size2 = str_replace("(", "",str_replace(") ","",$arText2Temp[1]));
						$opt2Text .= '<input type="hidden" name="'.$optLoc.'2_sizeName[]" value="'.trim($arText2Temp[0]).'" /><p class="sizeText">'.trim($arText2Temp[0]).'</p>';
						$opt2Text .= '<input type="text" class="textOpt" id="'.$optLoc.'_sn'.$n2.'" name="'.$optLoc.'2_sizeValue[]" value="'.$size2.'" />';
						$n2++;
					}
				}
			}			
		} else {
			$sql_item = "SELECT * FROM shopping_cart WHERE id=$scid LIMIT 1";
			$result_item = mysql_query($sql_item);
			$row_item = mysql_fetch_assoc($result_item);
			
			$sql_size = "SELECT Size FROM product_options WHERE ProductID=$prodID AND ColorSKU='$row_item[ColorSKU]' AND SizeSKU='$row_item[SizeSKU]' LIMIT 1";
			$result_size = mysql_query($sql_size);
			$row_size = mysql_fetch_assoc($result_size);
			
			$n=0;
			$size1 = "";
			foreach($arTextVals1 as $text1val) {
				$arText1Temp = explode("(", $text1val);
				$size1 = str_replace("(", "",str_replace(") ","",$arText1Temp[1]));
				$opt1Text .= '<input type="hidden" name="'.$optLoc.'1_sizeName[]" value="'.trim($arText1Temp[0]).'" /><p class="sizeText">'.trim($arText1Temp[0]).'</p>';
				$opt1Text .= '<input size="3" type="text" class="textOpt" id="'.$optLoc.'_sn'.$n.'" name="'.$optLoc.'1_sizeValue[]" value="'.$size1.'" />';
				$n++;
			}
			
			$n2=0;
			$size2 = "";
			foreach($arTextVals2 as $text2val) {
				$arText2Temp = explode("(", $text2val);
				$size2 = str_replace("(", "",str_replace(") ","",$arText2Temp[1]));
				$opt2Text .= '<input type="hidden" name="'.$optLoc.'2_sizeName[]" value="'.trim($arText2Temp[0]).'" /><p class="sizeText">'.trim($arText2Temp[0]).'</p>';
				$opt2Text .= '<input type="text" class="textOpt" id="'.$optLoc.'_sn'.$n2.'" name="'.$optLoc.'2_sizeValue[]" value="'.$size2.'" />';
				$n2++;
			}
		}
		
		if($n2>$n) {
			$n = $n2;
		}
		// END SIZE INFORMATION FOR MULTIPLE QTY'S //////////////////////////////////////////////
		
		if($optLoc == "Front") {
			switch($optType) {
				case "logo_name":
					$optType  = "name";
					$optType2 = "logo";
					$imgLoc2 = $optLoc."_pocket";
					break;
				case "name_number":
					$optType  = "name";
					$optType2 = "number";
					$imgLoc2 = $optLoc."_pocket";
					break;
				case "logo_number":
					$optType  = "logo";
					$optType2 = "number";
					$imgLoc2 = $optLoc."_pocket";
					break;
				case "pocketlogo":
					$imgLoc = $optLoc."_pocket";
					break;
				case "chestlogo":
					break;
				case "pocketlogo_number":
					$optType = "pocketlogo";
					$optType2 = "number";
					$imgLoc = $optLoc."_pocket";
					$imgLoc2 = $optLoc."_main";
					break;
				case "chestlogo_number":
					$optType = "chestlogo";
					$optType2 = "number";
					$imgLoc2 = $optLoc."_pocket";
					break;
			}
		} elseif($optLoc == "Back") {
			switch($optType) {
				case "name":
					$imgLoc = $optLoc."_name";
					break;
				case "logo_name":
					$optType  = "logo";
					$optType2 = "name";
					$imgLoc2 = $optLoc."_name";
					break;
				case "name_number":
					$optType  = "number";
					$optType2 = "name";
					$imgLoc2 = $optLoc."_name";
					break;
				case "logo_number":
					$optType  = "logo";
					break;
			}
		}
		
		switch($optType) {
			case "chestlogo":
				$optTypeQry = "logo";
				break;
			case "pocketlogo":
				$optTypeQry = "pocket";
				break;
			default:
				$optTypeQry = $optType;
		}
		
	    $sql_opts = "SELECT o.id FROM imprint_options o, imprint_images i, products p WHERE p.ImprintCatID = o.ImprintCategory AND o.id = i.OptionID AND o.Type = '$optTypeQry' AND p.id=$prodID AND i.Type ='$optLoc' LIMIT 1";
		$result_opts = mysql_query($sql_opts);
		$row_opts = mysql_fetch_assoc($result_opts);
		$sql_imgs = "SELECT * FROM imprint_images WHERE OptionID=$row_opts[id] AND Type='$optLoc' AND Parent=0";
		$result_imgs = mysql_query($sql_imgs);
		?>
        <input type="hidden" id="<?=$optLoc;?>opt1Loc" name="<?=$optLoc;?>opt1Loc" value="<?=$optLoc;?>" />
        <input type="hidden" id="<?=$optLoc?>opt1Image" name="<?=$optLoc?>opt1Image" value="<?=$row_scimp["Opt1Image"];?>" />
        <input type="hidden" id="<?=$optLoc?>opt1Type" name="<?=$optLoc?>opt1Type" value="<?=$optType;?>" />
        <input type="hidden" id="<?=$optLoc;?>opt1Color" name="<?=$optLoc;?>opt1Color" value="<?=$row_scimp["Opt1Color"];?>" />
        <div class="selImage">
        	<div class="selImageHdr">Step 1: Select <?=$optType;?></div>
			<img class="selImgHover" id="<?=$optLoc;?>_lrgImage" src="" />
			<?php
            while($row_imgs = mysql_fetch_array($result_imgs)) {
                echo '<img class="'.$optLoc.'_optImages" id="'.$row_imgs["id"].'" src="cpadmin/logo/'.$row_imgs["Image"].'" />';	
            }
            ?>
        </div>
        <div class="selColor">
        	<div class="selImageHdr">Step 2: Select Color</div>
            <div id="<?=$optLoc;?>_colors">Select <?=$optType;?> first</div>
        </div>
        <?php
			$step = 3;
			if($optType == "name" || $optType == "number") {
				?>
                <div class="selName">
                	<div class="selNameHdr">Step <?=$step;?>: Enter <?=ucfirst($optType);?></div>
                    	<?=$opt1Text;?>
						<input type="button" class="continue repeat" id="<?=$optLoc;?>_rs" name="<?=$optLoc;?>_rs" value="Repeat these Entries" />
                    </div>
                </div>
				<?php
				$step++;
			}
			
			if($optType == "chestlogo" || $optType == "pocketlogo") {
				?>
					<div class="selTeam">
						<div class="selTeamHdr">Step <?=$step;?>: Enter Team Name</div>
						<input type="text" class="textOpt optFull" id="<?=$optLoc;?>_opt1Team" name="<?=$optLoc;?>_opt1Team" value="<?=$row_scimp["Opt1Team"];?>" />
					</div>
				<?php
			}
		?>
        <script>
			$(".<?=$optLoc;?>_optImages").click(function(){
				$("#<?=$optLoc;?>_colors").html('<img src="images/loader.gif" />');
				$("#<?=$optLoc;?>_colors").load("includes/inc_imprint.php",{
					"type":"colors", 
					"optID":$(this).attr("id"), 
					"imgLoc":"<?=$imgLoc;?>", 
					"optValBox":"opt1Image"
				});
				$("#<?=$imgLoc;?>").attr("src", $(this).attr("src"));
				$("#<?=$optLoc;?>opt1Image").val($(this).attr("src"));
			});
			$(".<?=$optLoc;?>_optImages").hover(
				function(){
					$("#<?=$optLoc;?>_lrgImage").attr('src', $(this).attr("src"));
					$("#<?=$optLoc;?>_lrgImage").css('display','block');
					
					$("#<?=$optLoc;?>_lrgImage").css('left', $(this).position().left+40);
					$("#<?=$optLoc;?>_lrgImage").css('top', $(this).position().top+40);
				}, function(){
					$("#<?=$optLoc;?>_lrgImage").attr('src', '');
					$("#<?=$optLoc;?>_lrgImage").css('display','none');
				}
			);
		</script>
        <?php
		if($optType2 != "") {
			?>
            <div class="clear"></div>
            <hr />
            <input type="hidden" id="<?=$optLoc;?>opt2Loc" name="<?=$optLoc;?>opt2Loc" value="<?=$optLoc;?>" />
            <input type="hidden" id="<?=$optLoc?>opt2Image" name="<?=$optLoc?>opt2Image" value="<?=$row_scimp["Opt2Image"];?>" />
            <input type="hidden" id="<?=$optLoc?>opt2Type" name="<?=$optLoc?>opt2Type" value="<?=$optType2;?>" />
            <input type="hidden" id="<?=$optLoc;?>opt2Color" name="<?=$optLoc;?>opt2Color" value="<?=$row_scimp["Opt2Color"];?>" />
            <?php
			$sql_opts2 = "SELECT o.id FROM imprint_options o, products p WHERE p.ImprintCatID = o.ImprintCategory AND o.Type = '$optType2' LIMIT 1";
			$result_opts2 = mysql_query($sql_opts2);
			$row_opts2 = mysql_fetch_assoc($result_opts2);
			$sql_imgs2 = "SELECT * FROM imprint_images WHERE OptionID=$row_opts2[id] AND Type='$optLoc' AND Parent=0";
			$result_imgs2 = mysql_query($sql_imgs2);
			?>
            <div class="selImage">
            	<div class="selImageHdr">Select <?=$optType2;?></div>
                <?php
					while($row_imgs2 = mysql_fetch_array($result_imgs2)) {
						echo '<img class="optImages2" id="'.$row_imgs2["id"].'" src="cpadmin/logo/'.$row_imgs2["Image"].'" />';	
					}
				?>
            </div>
            <div class="selColor">
            	<div class="selImageHdr">Select Color</div>
                <div id="<?=$optLoc;?>_colors2">Select <?=$optType2;?> first</div>
            </div>
            <?php
				if($optType2 == "name" || $optType2 == "number") {
			?>
					<div class="selName">
						<div class="selNameHdr">Enter <?=ucfirst($optType2);?></div>
							<?=$opt2Text;?>
							<input type="button" class="continue repeat" id="<?=$optLoc;?>_rs" name="<?=$optLoc;?>_rs" value="Repeat these Entries" />
						</div>
					</div>
					<?php
				}
				
				if($optType2 == "chestlogo" || $optType2 == "pocketlogo") {
					?>
						<div class="selTeam">
							<div class="selTeamHdr">Enter Team Name</div>
							<input type="text" class="textOpt optFull" id="<?=$optLoc;?>_opt2Team" name="<?=$optLoc;?>_opt2Team" value="<?=$row_scimp["Opt2Team"];?>" />
						</div>
					<?php
				}
			?>
            <script>
				$(".optImages2").click(function() {
					$("#<?=$optLoc;?>_colors2").html('<img src="images/loader.gif" />');
					$("#<?=$optLoc;?>_colors2").load("includes/inc_imprint.php",{
						"type":"colors", 
						"optID":$(this).attr("id"), 
						"imgLoc":"<?=$imgLoc2;?>",
						"optValBox":"opt2Image"
					});
					$("#<?=$imgLoc2;?>").attr("src", $(this).attr("src"));
					$("#<?=$optLoc;?>opt2Image").val($(this).attr("src"));
				});
			</script>
            <?php
		} 
			?>
		<script>
			$(".repeat").click(function(){
				var n = <?=$n;?>;
				var id = $(this).attr("id");
				id = id.replace("_rs","");
				
				for(var i=0;i<n;i++) {
					$("input[id$='_sn"+i+"']").val($("#"+id+"_sn"+i).val());
				}
			});
		</script>
		<?php	
		mysql_close($conn);
		exit();
	}
	// END EDIT ::::::::::::::::::::::::
	
	if($_POST["type"] == "colors") {
		$optID = $_POST["optID"];
		$imgLoc = $_POST["imgLoc"];
		$optValBox = $_POST["optValBox"];
		$optValColor = $_POST["optValColor"];
		$arfield = explode("_",$imgLoc);
		$field = $arfield[0];
		$sql_colors = "SELECT * FROM imprint_images WHERE id=$optID OR Parent=$optID";
		$result_colors = mysql_query($sql_colors) or die(mysql_error());
		while($row_colors = mysql_fetch_array($result_colors)) {
			echo '<div class="optColors '.$field.'_colorOpt" id="color_'.$row_colors["id"].'" name="'.$row_colors["Image"].'" title="'.stripslashes($row_colors["ColorName"]).'" style="background-color: #'.$row_colors["Color"].'"></div>';
		}
		?>
        <script>
			$(".<?=$field;?>_colorOpt").click(function() {
				$("#<?=$imgLoc;?>").attr("src","cpadmin/logo/"+$(this).attr("name"));
				$("#<?=$field.$optValBox;?>").val("cpadmin/logo/"+$(this).attr("name"));
				$("#<?=$field.$optValColor;?>").val($(this).attr("title"));
			});
		</script>
        <?php
		mysql_close($conn);
		exit();
	}
	
	if($_POST["type"] == "impPricing") {
		$prodID = $_POST["prodID"];
		$optType = $_POST["optType"];
		$optLoc = $_POST["optLoc"];
		$sql_price = "SELECT o.*, i.Type AS Location, p.id FROM imprint_options o, imprint_images i, products p WHERE p.ImprintCatID = o.ImprintCategory AND o.id = i.OptionID AND p.id = '$prodID' AND o.Type='$optType' AND i.Type='$optLoc' LIMIT 1";
		$result_price = mysql_query($sql_price);
		$row_price = mysql_fetch_assoc($result_price);
		
		if ($optLoc == "Back") {
			echo '<p class="impHeader">Quantity Discount - Number</p>';
		} else {
			echo '<p class="impHeader">Quantity Discount - Logo</p>';
		}
		?>
			<p class="price fourth"><?=stripslashes($row_price["Option4"]);?></p>
			<p class="price third"><?=stripslashes($row_price["Option3"]);?></p>
			<p class="price second"><?=stripslashes($row_price["Option2"]);?></p>
			<p class="price first"><?=stripslashes($row_price["Option1"]);?></p>
			<!-- p class="price default">Non Member</p -->
			<p class="price default">&nbsp;</p>
			<p class="price fourth">$<?=number_format($row_price["Option4Price"],2);?></p>
			<p class="price third">$<?=number_format($row_price["Option3Price"],2);?></p>
			<p class="price second">$<?=number_format($row_price["Option2Price"],2);?></p>
			<p class="price first">$<?=number_format($row_price["Option1Price"],2);?></p>
			<!-- p class="price default">$<?=number_format($row_price["DefaultPrice"],2);?></p -->
			<p class="price default">&nbsp;</p>
		<?php
		mysql_close($conn);
		exit();
	}
?>