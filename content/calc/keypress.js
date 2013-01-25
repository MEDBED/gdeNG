function CheckKeyAndApply(e) {
	var keyNum;
	var keyChar;
	var numCheck;

	if(window.event) // IE8 and earlier
	{
	keyNum = e.keyCode;
	}
	else if(e.which) // IE9/Firefox/Chrome/Opera/Safari
	{
	keyNum = e.which;
	}
	
	// Enter key exception
	keyChar = keyNum;
	if (keyChar != 13) {
		keyChar = String.fromCharCode(keyChar);
	}
	ApplyClick(keyChar);
}

function ApplyClick(keyPressed) {
	var actualKey = CheckKeyExceptions(keyPressed);
	var $key = $("#key_" + actualKey);
	if ($key != null) {
		$key.trigger('click');
	}
}

function CheckKeyExceptions(keyPressed) {
	var returnKey = keyPressed;
	switch (keyPressed) {
		case "+":
			returnKey = "plus";
			break;
		case "-":
			returnKey = "minus";
			break;
		case "*":
			returnKey = "multiply";
			break;
		case "/":
			returnKey = "slash";
			break;
		case "=":
			returnKey = "equal";
			break;
		case ".":
			returnKey = "period";
			break;
		case 13:
			returnKey = "equal";
			break;
		default:
			break;
	}
	
	return returnKey;
}