<?php
/******************************************************************************
 MachForm
  
 Copyright 2007 Appnitro Software. This code cannot be redistributed without
 permission from http://www.appnitro.com/
 
 More info at: http://www.appnitro.com/
 ******************************************************************************/

	
/** Hook samples **
	//sample customizing captcha look and feel
	function form2_hook_pre_captcha(){
		$capthca_theme =<<<EOT
<script>
var RecaptchaOptions = {
   theme : 'blackglass'
};
</script>
EOT;
		return $capthca_theme;
	}
	
**/
?>