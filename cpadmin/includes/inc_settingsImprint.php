<?php

	session_start();
	require 'db.php';
?> 
        <table border="0">
            <tr>
                  <th style="width:100px;">ID</th>
                  <th style="width:420px; padding-left: 13px;">Product Name</th>
                  <th style="width:200px;">SKU</th>
                  <th style="width:150px;">Brand</th>
                  <th style="width:110px; text-align:center;">Action</th>
            </tr>
            <?php
				$imprintProduct_sql = "SELECT * FROM products WHERE ProductType='Bundle'";
				$imprintProduct_Obj = mysql_query($imprintProduct_sql);
				if (@mysql_num_rows($imprintProduct_Obj)) {
					$c_num = 0;
					while($imprintProduct=mysql_fetch_array($imprintProduct_Obj)) {
						if($c_num==0) {
							$color = "#e7e7e7";
							$c_num++;
						} else {
							$color = "#dbdbdb";
							$c_num = 0;
						}
			?>
		            <tr>
		            	<td style="background-color: <?=$color;?>;"><?=$imprintProduct["id"];?></td>
		                <td style="background-color: <?=$color;?>;">
							<?=$imprintProduct["BrowserName"].' '.$imprintProduct["BrowserName2"].' '.$imprintProduct["BrowserName3"];?>
						</td>
						<td style="background-color: <?=$color;?>;"><?=$imprintProduct["RootSKU"]?></td>
		                <td style="background-color: <?=$color;?>;"><?=$imprintProduct["Brand"]?></td>
		                <td style="text-align:center;background-color: <?=$color;?>;">
		                	<a href="imprintMessage.php?type=edit&id=<?=$imprintProduct["id"];?>"><img class="cmsedit" src="images/E.png" /></a>
		                	<!--<img class="cmsdelete" id="<?=$imprintProduct["id"];?>" style="cursor: pointer;" src="images/D.png" />-->
		                </td>
		            </tr>
            <?php
					}
				} else {
			?>
			<tr>
				<td colspan="5">
					--No Data Found--
				</td>
			</tr>
			<?php } ?>
          </table>
          
          <script type="javascript">
		  	$(".cmsedit").hover(function(){
							$(this).attr("src", "images/E_hover.png");
						},function() {
							$(this).attr("src", "images/E.png");
						});
			$(".cmsdelete").hover(function(){
							$(this).attr("src", "images/D_hover.png");
						},function() {
							$(this).attr("src", "images/D.png");
						});
		  	$(".cmsdelete").click(function() {
				var del = confirm("Delete this page?");
				
				if(del) {
					$.post("imprintMessage.php", {"type":"delete", "id":+$(this).attr("id")}, 
						function(data) {
							location.reload();
						});
				}
			});
		  </script>

      