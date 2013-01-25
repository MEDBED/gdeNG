/** 
 * winDim v2.0.2, 2010-04-12
 * 
 * retourne les dimentions intérieurs de la fenêtre
 * 
 * @see			http://webbricks.org/bricks/winDim/
 *
 * @returns		{Object}
 * 
 * =============================================================================
 */
function winDim() {
	var W,H,
		i = window,
		d = document,
		de = d.documentElement,
		db = d.body;
		
	if ( i.innerWidth ) { // autres que IE
		W = i.innerWidth;
		H = i.innerHeight;
	} else if ( de.clientWidth ) { // IE8
		W = de.clientWidth;
		H = de.clientHeight;
	}
	else { // IE6
		W = db.clientWidth;
		H = db.clientHeight;
	}

	return {w:W, h:H} ;
}