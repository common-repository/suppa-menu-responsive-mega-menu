/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referring to this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'codetempIcons\'">' + entity + '</span>' + html;
	}
	var icons = {
		'ct-caret-right': '&#xe602;',
		'ct-caret-down': '&#xe601;',
		'ct-search': '&#xe603;',
		'ct-angle-right': '&#xe600;',
		'ct-angle-left': '&#xe605;',
		'ct-angle-down': '&#xe606;',
		'ct-angle-up': '&#xe607;',
		'ct-caret-up': '&#xe604;',
		'ct-caret-left': '&#xe608;',
		'ct-magic': '&#xe609;',
		'ct-wrench': '&#xe60a;',
		'ct-text-height': '&#xe60b;',
		'ct-twitter': '&#xe60c;',
		'ct-reorder': '&#xe60d;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/ct-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
