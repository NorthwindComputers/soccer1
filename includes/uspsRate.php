<?php
// http://www.marksanborn.net/php/calculating-usps-shipping-rates-with-php/

class uspsRate {
// to use the function make a call to 
// uspsRate($weight,$dest_zip,$PostalCode)

function uspsRate($weight, $dest_zip, $origin_zip) {   
    $userName = '628NORTH2278'; // Your USPS Username
    $url = "http://Production.ShippingAPIs.com/ShippingAPI.dll";
    $ch = curl_init();

    // set the target url
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);

    // parameters to post
    curl_setopt($ch, CURLOPT_POST, 1);

    $data = "API=RateV4&XML=<RateV4Request USERID=\"$userName\"><Package ID=\"1ST\"><Service>PRIORITY</Service><ZipOrigination>";
	$data .= $origin_zip;
	$data .= "</ZipOrigination><ZipDestination>";
	$data .= $dest_zip;
	$data .= "</ZipDestination><Pounds>";
	$data .= $weight;
 	$data .= "</Pounds><Ounces>0</Ounces><Container>VARIABLE</Container><Size>REGULAR</Size><Machinable>TRUE</Machinable></Package></RateV4Request>";

    // send the POST values to USPS
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result=curl_exec ($ch);
    $data = strstr($result, '<?');

    // echo '<!-- '. $data. ' -->'; // TESTING USE ONLY

    $xml_parser = xml_parser_create();
    xml_parse_into_struct($xml_parser, $data, $vals, $index);
    xml_parser_free($xml_parser);
    $params = array();
    $level = array();
    foreach ($vals as $xml_elem) {
        if ($xml_elem['type'] == 'open') {
            if (array_key_exists('attributes',$xml_elem)) {
                list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
            } else {
            $level[$xml_elem['level']] = $xml_elem['tag'];
            }
        }
        if ($xml_elem['type'] == 'complete') {
            $start_level = 1;
            $php_stmt = '$params';
            while($start_level < $xml_elem['level']) {
                $php_stmt .= '[$level['.$start_level.']]';
                $start_level++;
            }
            $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
            eval($php_stmt);
        }
    }
    curl_close($ch);
    // echo '<pre>'; print_r($params); echo'</pre>'; // Uncomment to see xml tags
    return $params['RATEV4RESPONSE']['1ST']['1']['RATE'];
} // end function
} // end class
?>