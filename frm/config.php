<?php

// DEV server info 
/*
define('DB_NAME', 'socnet_forms'); 
define('DB_USER', 'socnet_forms'); 
define('DB_PASSWORD', 'oUD6TVU3ete!'); 
*/

// PRODUCTION server info
define('DB_NAME', 'soccer1_forms'); 
define('DB_USER', 'soccer1_formuser'); 
define('DB_PASSWORD', '7Udr*X9}~;n+'); 

define('DB_HOST', 'localhost'); 
	
/** Admin user and password **/
define('ADMIN_USER','richard');
define('ADMIN_PASSWORD','Pede7424!');
	
/** Data folder **/
/** Folder settings for your CSS files and upload folder **/
define('DATA_DIR', './data');  //CSS files folder
define('UPLOAD_DIR', './data'); //for maximum security, set full path to any folder outside your document root

/** File upload limit **/
define('UPLOAD_FILETYPE_ALLOW',''); //allow only these filetypes to be uploaded
define('UPLOAD_FILETYPE_DENY','php;php3;php4;php5;phtml;exe;pl;cgi;html;htm;js'); //do not allow these filetypes to be uploaded

/** CAPTCHA settings **/
/** To use reCAPTCHA you must get an API key from http://recaptcha.net/api/getkey **/
define('RECAPTCHA_PUBLIC_KEY','6LewRscSAAAAACFK7-ZQh3TLmrrFBv0P34YxaN08');
define('RECAPTCHA_PRIVATE_KEY','6LewRscSAAAAAEJMh4TNBg-8quipwPk-qVQ3I40u');

/** Set below to 'true' if you prefer to use the internal CAPTCHA implementation instead of reCAPTCHA **/
/** GD extension must be enabled in order to use this option **/
define('USE_INTERNAL_CAPTCHA', true);

/** SMTP settings **/
/** Don't modify it unless you know you are using SMTP **/
define('USE_SMTP',false); //set this to 'true' to use SMTP
/*
define('SMTP_HOST','localhost');
define('SMTP_PORT',25);
define('SMTP_AUTH',false); //if your SMTP require authentification, set this to 'true'
define('SMTP_USERNAME','YOUR_SMTP_USERNAME');
define('SMTP_PASSWORD','YOUR_SMTP_PASSWORD');
*/
define('SMTP_SECURE',false); //set this to 'true' if your server is using secure SMTP (TLS/SSL)

/** Default notification email settings **/
define('NOTIFICATION_MAIL_FROM','customerservice@soccerone.com'); //default is 'no-reply@yourdomain.com'
define('NOTIFICATION_MAIL_FROM_NAME','SoccerOne'); //default is 'MachForm'
define('NOTIFICATION_MAIL_SUBJECT','Website contact received'); //default is '{form_name} [#{entry_no}]'

/** Current MachForm Version **/
define('MACHFORM_VERSION','2.3');

error_reporting(E_ALL ^ E_NOTICE);
@header("Content-Type: text/html; charset=UTF-8"); 
?>
