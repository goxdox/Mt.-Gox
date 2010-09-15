(function($) {
    $.extend({
    	// public interface: $.tmpl
    	tmpl : function(tmpl, vals) {
    		var rgxp, repr;
    		
			// default to doing no harm
			tmpl = tmpl   || '';
			vals = vals || {};
    		
    		// regular expression for matching our placeholders; e.g., #{my-cLaSs_name77}
    		rgxp = /#\{([^{}]*)}/g;
    		
    		// function to making replacements
    		repr = function (str, match) {
				return typeof vals[match] === 'string' || typeof vals[match] === 'number' ? vals[match] : str;
			};
			
			return tmpl.replace(rgxp, repr);
		}
	});
})(jQuery);