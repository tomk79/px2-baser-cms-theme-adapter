/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

(function($){
	$.bcDashboard = {
		ajax: function(url, selector) {
			$.bcUtil.ajax($.baseUrl + url, function(result){
				if(result) {
					$(selector).hide();
					$(selector).html(result);
					$(selector).slideDown(500);
				}
			},{
				'loaderType': 'inner',
				'loaderSelector': selector
			});
		}
	}
})(jQuery);