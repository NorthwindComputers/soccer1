<?php
/******************************************************************************
 MachForm
  
 Copyright 2007 Appnitro Software. This code cannot be redistributed without
 permission from http://www.appnitro.com/
 
 More info at: http://www.appnitro.com/
 ******************************************************************************/
	global $lang;
	
	//simple name and extended name
	$lang['name_first']			=	'First';
	$lang['name_last']			=	'Last';
	$lang['name_title']			=	'Title';
	$lang['name_suffix']		=	'Suffix';
	
	//address
	$lang['address_street']		=	'Street Address';
	$lang['address_street2']	=	'Address Line 2';
	$lang['address_city']		=	'City';
	$lang['address_state']		=	'State / Province / Region';
	$lang['address_zip']		=	'Postal / Zip Code';
	$lang['address_country']	=	'Country';

	//captcha
	$lang['captcha_required']	=	'This field is required. Please enter the letters shown in the image.';
	$lang['captcha_mismatch']	=	'The letters in the image do not match. Try again.';
	$lang['captcha_error']		=	'Error while processing, please try again.';
	$lang['captcha_title']		=	'Type the letters you see in the image below.';
	
	//date
	$lang['date_dd']			=	'DD';
	$lang['date_mm']			=	'MM';
	$lang['date_yyyy']			=	'YYYY';
	
	//price
	$lang['price_dollar_main']	=	'Dollars';
	$lang['price_dollar_sub']	=	'Cents';
	$lang['price_euro_main']	=	'Euros';
	$lang['price_euro_sub']		=	'Cents';
	$lang['price_pound_main']	=	'Pounds';
	$lang['price_pound_sub']	=	'Pence';
	$lang['price_yen']			=	'Yen';
	
	//time
	$lang['time_hh']			=	'HH';
	$lang['time_mm']			=	'MM';
	$lang['time_ss']			=	'SS';
	
	//error message
	$lang['error_title']		=	'There was a problem with your submission.';
	$lang['error_desc']			=	'Errors have been <strong>highlighted</strong> below.';
	
	//form buttons
	$lang['submit_button']		=	'Submit';
	$lang['continue_button']	=	'Continue';
	$lang['back_button']		=	'Back';
	
	//form status
	$lang['form_inactive']		=	'This form is currently inactive.';
	
	//form password
	$lang['form_pass_title']	=	'This form is password protected.';
	$lang['form_pass_desc']		=	'Please enter your password.';
	$lang['form_pass_invalid']	=	'Invalid Password!';
	
	//form review
	$lang['review_title']		=	'Review Your Entry';
	$lang['review_message']		=	'Please review your entry below. Click Submit button to finish.';
	
	//validation message 
	$lang['val_required'] 		=	'This field is required. Please enter a value.';
	$lang['val_required_file'] 	=	'This field is required. Please upload a file.';
	$lang['val_unique'] 		=	'This field requires a unique entry and this value has already been used.';
	$lang['val_integer'] 		=	'This field must be an integer.';
	$lang['val_float'] 			=	'This field must be a float.';
	$lang['val_numeric'] 		=	'This field must be a number.';
	$lang['val_min'] 			=	'This field can not be less than %s characters.';		
	$lang['val_max'] 			=	'This field can not be greater than %s characters.';
	$lang['val_range'] 			=	'This field is not in the range %s through %s characters.';
	$lang['val_email'] 			=	'This field is not in the correct email format.';
	$lang['val_website'] 		=	'This field is not in the correct website address format.';
	$lang['val_username'] 		=	'This field may only consist of a-z 0-9 and underscores.';
	$lang['val_equal'] 			=	'%s must match.';
	$lang['val_date'] 			=	'This field is not in the correct date format.';
	$lang['val_time'] 			=	'This field is not in the correct time format.';
	$lang['val_phone'] 			=	'Please enter a valid phone number.';
	$lang['val_filetype']		=	'The filetype you are attempting to upload is not allowed.';
	
	//fields on excel/csv
	$lang['export_num']			=	'No.';
	$lang['date_created']		=	'Date Created';
	$lang['date_updated']		=	'Date Updated';
	$lang['ip_address']			=	'IP Address';			
?>