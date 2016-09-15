function make_safe($variable) {
	$variable = mysql_real_escape_string(trim($variable));
	return $variable;
}