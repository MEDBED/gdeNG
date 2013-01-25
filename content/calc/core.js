var calculatorObject = {
	defaultValue : '0.00',
	stringResult : '',
	operators : ['+', '-', '*', '/'],
	sequence : [],
	cycleFinished : false,
	urlCalculate : 'mod/calculate.php',
	isNumber : function(value){
		return typeof value !== 'undefined' && !isNaN(parseFloat(value)) && isFinite(value);
	},
	calculate : function() {
		jQuery.post(calculatorObject.urlCalculate, { sequence : calculatorObject.sequence }, function(data) {
			if (!data.error) {
				$('#calculatorResult').html(data.result);				
				calculatorObject.cycleFinished = true;
				calculatorObject.sequence = [];
			}
		}, 'json');
	},
	trigger : function(obj) {
		if (obj.length > 0) {
			obj.find('ul li').live('click', function() {
			
				$(this).siblings('li').removeClass('active');
				$(this).addClass('active');
				
				var thisItem = $(this).attr('data-value');
				
				var thisValue = $('#calculatorResult').text();
				
				switch(thisItem) {
					
					case '=':
					
					calculatorObject.sequence.push(thisValue);
					calculatorObject.calculate();
					
					break;
					
					case 'c':
					
					calculatorObject.sequence = [];
					$('#calculatorResult').html(calculatorObject.defaultValue);
					
					break;
					
					case calculatorObject.operators[0]:
					case calculatorObject.operators[1]:
					case calculatorObject.operators[2]:
					case calculatorObject.operators[3]:
					
					// if after clicking equal symbol we've clicked the operator
					// then simply set the cycleFinished to false
					// to allow further calculation of the result
					if (calculatorObject.cycleFinished) {
						calculatorObject.cycleFinished = false;
					}
					
					// add value from the field to array - suppose to be integer
					// if not will be overwritten by the following statement
					calculatorObject.sequence.push(thisValue);
					
					if (calculatorObject.sequence.length > 0) {
						
						// if the last element in array is integer it's all good
						// add operator to array
						if (calculatorObject.isNumber(calculatorObject.sequence[calculatorObject.sequence.length-1])) {
							calculatorObject.sequence.push(thisItem);
						// otherwise overwrite the last item
						} else {
							calculatorObject.sequence[calculatorObject.sequence.length-1] = thisItem;
						}
					
					} else {
					
						calculatorObject.sequence.push(calculatorObject.defaultValue);
						calculatorObject.sequence.push(thisItem);
						
					}
					
					$('#calculatorResult').html(thisItem);
					
					break;
					
					default:
					
					// if current value is not an operator
					if (jQuery.inArray(thisValue, calculatorObject.operators) === -1) {
						if (thisValue !== calculatorObject.defaultValue) {
							if (calculatorObject.cycleFinished) {
								calculatorObject.cycleFinished = false;
								$('#calculatorResult').html(thisItem);
							} else {
								$('#calculatorResult').html(thisValue + thisItem);
							}
						} else {
							$('#calculatorResult').html(thisItem);
						}
					// otherwise - if it is
					} else {
						$('#calculatorResult').html(thisItem);
					}
					
				}
						
				
			});
		}
	}
};
$(function() {

	calculatorObject.trigger($('#calculator'));

});