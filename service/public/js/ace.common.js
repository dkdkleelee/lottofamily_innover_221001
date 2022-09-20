
/*
 * Date Format 1.2.3
 * (c) 2007-2009 Steven Levithan <stevenlevithan.com>
 * MIT license
 *
 * Includes enhancements by Scott Trenda <scott.trenda.net>
 * and Kris Kowal <cixar.com/~kris.kowal/>
 *
 * Accepts a date, a mask, or a date and a mask.
 * Returns a formatted version of the given date.
 * The date defaults to the current date/time.
 * The mask defaults to dateFormat.masks.default.
 */

var SyntaxError = function(error) {
	alert(error);
};

var dateFormat = function () {
	var	token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
		timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
		timezoneClip = /[^-+\dA-Z]/g,
		pad = function (val, len) {
			val = String(val);
			len = len || 2;
			while (val.length < len) val = "0" + val;
			return val;
		};

	// Regexes and supporting functions are cached through closure
	return function (date, mask, utc) {
		var dF = dateFormat;

		// You can't provide utc if you skip other args (use the "UTC:" mask prefix)
		if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
			mask = date;
			date = undefined;
		}

		// Passing date through Date applies Date.parse, if necessary
		date = date ? new Date(date) : new Date;
		if (isNaN(date)) throw SyntaxError("invalid date");

		mask = String(dF.masks[mask] || mask || dF.masks["default"]);

		// Allow setting the utc argument via the mask
		if (mask.slice(0, 4) == "UTC:") {
			mask = mask.slice(4);
			utc = true;
		}

		var	_ = utc ? "getUTC" : "get",
			d = date[_ + "Date"](),
			D = date[_ + "Day"](),
			m = date[_ + "Month"](),
			y = date[_ + "FullYear"](),
			H = date[_ + "Hours"](),
			M = date[_ + "Minutes"](),
			s = date[_ + "Seconds"](),
			L = date[_ + "Milliseconds"](),
			o = utc ? 0 : date.getTimezoneOffset(),
			flags = {
				d:    d,
				dd:   pad(d),
				ddd:  dF.i18n.dayNames[D],
				dddd: dF.i18n.dayNames[D + 7],
				m:    m + 1,
				mm:   pad(m + 1),
				mmm:  dF.i18n.monthNames[m],
				mmmm: dF.i18n.monthNames[m + 12],
				yy:   String(y).slice(2),
				yyyy: y,
				h:    H % 12 || 12,
				hh:   pad(H % 12 || 12),
				H:    H,
				HH:   pad(H),
				M:    M,
				MM:   pad(M),
				s:    s,
				ss:   pad(s),
				l:    pad(L, 3),
				L:    pad(L > 99 ? Math.round(L / 10) : L),
				t:    H < 12 ? "a"  : "p",
				tt:   H < 12 ? "am" : "pm",
				T:    H < 12 ? "A"  : "P",
				TT:   H < 12 ? "AM" : "PM",
				Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
				o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
				S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
			};

		return mask.replace(token, function ($0) {
			return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
		});
	};
}();

// Some common format strings
dateFormat.masks = {
	"default":      "ddd mmm dd yyyy HH:MM:ss",
	shortDate:      "m/d/yy",
	mediumDate:     "mmm d, yyyy",
	longDate:       "mmmm d, yyyy",
	fullDate:       "dddd, mmmm d, yyyy",
	shortTime:      "h:MM TT",
	mediumTime:     "h:MM:ss TT",
	longTime:       "h:MM:ss TT Z",
	isoDate:        "yyyy-mm-dd",
	isoTime:        "HH:MM:ss",
	isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
	isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};

// Internationalization strings
dateFormat.i18n = {
	dayNames: [
		"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
		"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
	],
	monthNames: [
		"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
		"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
	]
};

Date.prototype.format = function (mask, utc) {
	return dateFormat(this, mask, utc);
};



String.prototype.number_format=function(){
	return this.replace(/(\d)(?=(?:\d{3})+(?!\d))/g,'$1,');
}


$("input:text[numberOnly]").live("keyup", function() {
	$(this).val( $(this).val().replace(/[^0-9]/gi,"") );
});
$("input:text[datetimeOnly]").live("keyup", function(){
	$(this).val( $(this).val().replace(/[^0-9:\-]/gi,"") );
});



/* vim: set expandtab tabstop=4 shiftwidth=4: */ 
// +--------------------------------------------------------+ 
// | Copyright : Song Hyo-Jin <shj at xenosi.de>            | 
// +--------------------------------------------------------+ 
// | License : BSD                                          | 
// +--------------------------------------------------------+ 
// 
// $Id: number_formatting.js, 2012. 4. 6. crucify Exp $ 
/*
String.prototype.toInt = function() { 
  var pm = /^-/.test(this) ? -1 : 1; 
  return this.replace(/\..*$/g, '').replace(/[^\d]/g, '') * pm; 
} 
String.prototype.toNum = function() { 
  var pm = /^-/.test(this) ? -1 : 1; 
  return this.replace(/(\.[^\.]+)\..*$/g, '$1').replace(/[^\d\.]/g, '') * pm; 
} 
String.prototype.reverse = function() { 
  return this.match(/./g).reverse().join(''); 
} 
String.prototype.numberFormat = function() { 
  var num = (this.toNum() + '').split(/\./); 
  var res = []; 
  res.push(num[0].reverse().replace(/(\d{3})(?=\d)/g, '$1,').reverse()); 
  if(num.length > 1) res.push(num[1].replace(/(\d{3})(?=\d)/g, '$1,')); 
  return res.join('.'); 
} 
Number.prototype.numberFormat = function() { 
  return (this + '').numberFormat(); 
} 

String.prototype.humanFormat = function() { 
  if(this == '' || this == '0') return 0; 
  return this.toNum().humanFormat(); 
} 
Number.prototype.humanFormat = function() { 
  if(this == 0) return 0; 
  var units = ['', 'k', 'm', 'g', 't', 'p', 'e', 'z', 'y']; 
  var idx = Math.floor(Math.log(this) / Math.log(1000)); 
  if(idx == 0) return this; 
  return (Math.ceil(this / Math.pow(1000, idx) * 100) / 100) + units[idx]; 
} 

Number.prototype.strpad = function(c, f) { 
  return (this + '').strpad(c, f); 
} 
String.prototype.strpad = function(c, f) { 
  if(!f && f != 0) f = '0'; 
  f = f + ''; 
  var res = this; 
  while(res.length < c) res = f + res; 
  return res; 
}
*/



String.prototype.numberFormat = function(decimals) { 
  if(this == '' || this == '0') return 0; 
  return number_format(this, decimals)
} 
Number.prototype.numberFormat = function(decimals) { 
  if(this == 0) return 0; 
  return number_format(this, decimals)
} 


function number_format(number, decimals, dec_point, thousands_sep) {
  //  discuss at: http://phpjs.org/functions/number_format/
  // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: davook
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Theriault
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Michael White (http://getsprink.com)
  // bugfixed by: Benjamin Lupton
  // bugfixed by: Allan Jensen (http://www.winternet.no)
  // bugfixed by: Howard Yeend
  // bugfixed by: Diogo Resende
  // bugfixed by: Rival
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  //  revised by: Luke Smith (http://lucassmith.name)
  //    input by: Kheang Hok Chin (http://www.distantia.ca/)
  //    input by: Jay Klehr
  //    input by: Amir Habibi (http://www.residence-mixte.com/)
  //    input by: Amirouche
  //   example 1: number_format(1234.56);
  //   returns 1: '1,235'
  //   example 2: number_format(1234.56, 2, ',', ' ');
  //   returns 2: '1 234,56'
  //   example 3: number_format(1234.5678, 2, '.', '');
  //   returns 3: '1234.57'
  //   example 4: number_format(67, 2, ',', '.');
  //   returns 4: '67,00'
  //   example 5: number_format(1000);
  //   returns 5: '1,000'
  //   example 6: number_format(67.311, 2);
  //   returns 6: '67.31'
  //   example 7: number_format(1000.55, 1);
  //   returns 7: '1,000.6'
  //   example 8: number_format(67000, 5, ',', '.');
  //   returns 8: '67.000,00000'
  //   example 9: number_format(0.9, 0);
  //   returns 9: '1'
  //  example 10: number_format('1.20', 2);
  //  returns 10: '1.20'
  //  example 11: number_format('1.20', 4);
  //  returns 11: '1.2000'
  //  example 12: number_format('1.2000', 3);
  //  returns 12: '1.200'
  //  example 13: number_format('1 000,50', 2, '.', ' ');
  //  returns 13: '100 050.00'
  //  example 14: number_format(1e-8, 8, '.', '');
  //  returns 14: '0.00000001'

  number = (number + '')
    .replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k)
        .toFixed(prec);
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
    .split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '')
    .length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1)
      .join('0');
  }
  return s.join(dec);
}


// py <-> m2
function convert(obj, type) {
	var val = $(obj).val();
	val = val.replace(/,/g, '');

	if(type == 'py') {
		var py = val/3.3;
		$('#'+type).val(py.numberFormat());
	} else {
		var m2 = val*3.3;
		$('#'+type).val(m2.numberFormat());
	}

}

// 숫자 한글읽기로 변환
function viewKorean(num, id) {
	var num = num.replace(/,/g , "");
    var hanA = new Array("","일","이","삼","사","오","육","칠","팔","구","십");
    var danA = new Array("","십","백","천","","십","백","천","","십","백","천","","십","백","천");
    var result = "";
	for(i=0; i<num.length; i++) {		
		str = "";
		han = hanA[num.charAt(num.length-(i+1))];
		if(han != "")
			str += han+danA[i];
		if(i == 4) str += "만";
		if(i == 8) str += "억";
		if(i == 12) str += "조";
		result = str + result;
	}
	if(num != 0)
		result = result;

	if(id) {
	    $('#'+id).html(result);
	} else {
		return result;
	}
}

function showNoty(text, id) {

	new Noty({
		text: text,
		type: 'success',
		theme: 'metroui',
		layout: 'topRight',
		timeout: 10000,
		progressBar: true,
		animation   : {
			open : 'animated bounceInRight',
			close: 'animated bounceOutRight',
			speed : 200
		},
		closeWith: ['button'],
		callbacks: {
			afterClose: function() {
				$.playSound('/service/public/js/served');

				if(id) { 
					$.ajax({
							url: "/service/public/lotto_common_process.php",
							type: "post",
							data: "proc=setSentNoty&id=" + id,
							cache: false,
							
							async: true,
							success: function (data) { 
								//
							}
					});
				}
			}
		}
	}).show();
}

$(document).ready(function() {
	$('#check_all').on('click', function() {
		if($(this).prop('checked')) {
			$('input[name^="chk["]').prop('checked', true);
		} else {
			$('input[name^="chk["]').prop('checked', false);
		}
	});
});
