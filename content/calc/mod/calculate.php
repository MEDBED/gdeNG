<?php
if (isset($_POST['sequence'])) {
	
	$values = array_filter($_POST['sequence'], function($value) {
	    return is_numeric($value) || in_array($value, array('+', '-', '/', '*'));
	});
	
	if (!empty($values)) {
		
		$calculation = 'return ('.implode(' ', $values).');';
		$value = eval($calculation);
		echo json_encode(array('error' => false, 'result' => number_format($value, 2)));
	
	} else {
		echo json_encode(array('error' => true, 'call' => 2));
	}
	
} else {
	echo json_encode(array('error' => true, 'call' => 1));
}