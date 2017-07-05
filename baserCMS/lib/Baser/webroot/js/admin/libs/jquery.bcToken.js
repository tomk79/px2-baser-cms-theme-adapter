/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * bcTokenプラグイン
 * 
 * フロントエンドでCakePHPのセキュリティコンポーネントのトークンの管理等を行う
 */

(function($) {
	$(function() {
		$.bcToken = {

		/**
		 * トークン
		 */
			key: null,

		/**
		 * トークンを取得済かどうか 
		 */
			requested: false,

		/**
		 * トークンを取得中かどうか 
		 */
			requesting: false,

		/**
		 * トークンURL
		 */
			url: null,

		/**
		 * デフォルトトークンURL
		 */
			defaultUrl: '/' + $.bcUtil.adminPrefix + '/dashboard/ajax_get_token?requestview=false',

		/**
		 * トークンを取得しているかどうかチェックし、取得していない場合取得する
		 * コールバック処理を登録する前提となっており、コールバック処理完了後、再度、新しいトークンを取得する
		 * 
		 * @param callback
		 * @param config
		 */
			check: function(callback, config) {
				if($.bcToken.requesting) {
					var timer = setInterval(function () {
						if (!$.bcToken.requesting) {
							clearInterval(timer);
							callback();
							$.bcToken.update(null, config);
						}
					}, 100);
				} else {
					if(!$.bcToken.key) {
						$.bcToken.update(callback, config);
					} else {
						callback();
						$.bcToken.update(null, config);
					}
				}
			},

		/**
		 * 新しいトークンをサーバーより取得する
		 * 
		 * @param callback
		 * @param config
		 */
			update: function(callback, config) {
				var _config = {
					type: 'GET'
				};
				if(config !== undefined) {
					config = $.extend(_config, config);
				} else {
					config = _config;
				}
				$.bcToken.requesting = true;
				$.bcUtil.ajax($.baseUrl + this.url, function(result) {
					$.bcToken.key = result;
					$.bcToken.requesting = false;
				}, $.extend(true, {}, config)).done(function() {
					if(callback) {
						callback();
						$.bcToken.update(null, config);
					}
				});
			},

		/**
		 * トークンを取得した空のフォームを取得する
		 * コールバック処理の引数として利用可能
		 * @param url
		 * @param callback
		 * @param config
		 */
			getForm: function (url, callback, config) {
				var form = $('<form/>');
				form.attr('action', url).attr('method', 'post');
				$.bcToken.check(function() {
					form.append($.bcToken.getHiddenToken());
					callback(form);
				}, config);
			},

		/**
		 * トークン用の hidden タグを取得する
		 * 
		 * @returns {*}
		 */
			getHiddenToken: function() {
				return $('<input name="data[_Token][key]" type="hidden">').val($.bcToken.key);
			},

		/**
		 * 指定したURLに対しトークンを付加した上でPOST送信を行う 
		 * @param url
		 */
			submitToken: function (url) {
				$.bcToken.getForm(url, function(form) {
					$('body').append(form);
					form.submit();	
				}, {hideLoader: false});
			},

		/**
		 * 指定したセレクターのリンクのクリックイベントについて、
		 * トークン付加前提のフォーム送信処理に置き換える
		 * 
		 * @param selector
		 */
			replaceLinkToSubmitToken: function(selector) {
				$(selector).each(function() {
					if($(this).attr('onclick')) {
						var regex = /if \(confirm\("(.+?)"\)/;
						var result = $(this).attr('onclick').match(regex);
						if(result) {
							$(this).attr('data-confirm-message', result[1]);
							$(this).get(0).onclick = '';
							$(this).removeAttr('onclick');
						}
					}
				});
				$(selector).click(function() {
					if($(this).attr('data-confirm-message')) {
						var message = $(this).attr('data-confirm-message');
						message = message.replace(/\\u([a-fA-F0-9]{4})/g, function(matchedString, group) {
							return String.fromCharCode(parseInt(group, 16));
						}).replace(/\\n/g, '\n');
						if(!confirm(message)) {
							return false;
						}
					}
					$.bcToken.submitToken($(this).attr('href'));
					return false;
				});
			},

		/**
		 * トークン発行URLのセット
		 * @param url トークン発行URL。nullの場合はデフォルトURL
		 */
			setTokenUrl: function (url) {
				this.url = url != null ? url : this.defaultUrl;
				return this;
			}

		};
		$.bcToken.setTokenUrl();
	});
})(jQuery);