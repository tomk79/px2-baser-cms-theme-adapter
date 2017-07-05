
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * baserAjaxDataList 設定
 */
$.extend($.baserAjaxDataList.config, {
	methods		: {
	/**
	 * 削除
	 */
		del: {
			button: '.btn-delete',
			confirm: 'このデータを本当に削除してもよろしいですか？\n※ 削除したデータは元に戻すことができません。', 
			result: function(row, result) {
				var config = $.baserAjaxDataList.config;
				if(result) {
					$(config.pageTotalNum).html(Number($(config.pageTotalNum).html()) - 1);
					$(config.pageEndNum).html(Number($(config.pageEndNum).html()) - 1);
					row.fadeOut(300, function(){
						row.remove();
						if($(config.dataList+" tbody td").length) {
							$.baserAjaxDataList.initList();
							$(config.dataList+" tbody tr").removeClass('even odd');
							$.yuga.stripe();
						} else {
							$.baserAjaxDataList.load(document.location.href);
						}
					});

				} else {
					$(config.alertBox).html('削除に失敗しました。');
					$(config.alertBox).fadeIn(500);
				}
			}
		},
	/**
	 * コピー
	 */
		copy: {
			button: '.btn-copy',
			confirm: '',
			result: function(row, result) {
				var config = $.baserAjaxDataList.config;
				if(result) {

					$(config.pageTotalNum).html(Number($(config.pageTotalNum).html()) + 1);
					$(config.pageEndNum).html(Number($(config.pageEndNum).html()) + 1);
					row.after(result);
					$.baserAjaxDataList.initList();
					row.next().hide().fadeIn(300, function(){
						$(config.dataList+" tbody tr").removeClass('even odd');
						$.yuga.stripe();
					});
					
				} else {
					$(config.alertBox).html('コピーに失敗しました。');
					$(config.alertBox).fadeIn(500);
				}
			}
		},
	/**
	 * 公開処理
	 */
		publish: {
			button: '.btn-publish',
			confirm: '',
			result: function(row, result) {
				var config = $.baserAjaxDataList.config;
				if(result) {
					row.removeClass('disablerow');
					row.removeClass('unpublish');
					row.addClass('publish');
					row.find('.status').html('○');
					$.baserAjaxDataList.config.methods.unpublish.initList();
					$.baserAjaxDataList.config.methods.publish.initList();
				} else {
					$(config.alertBox).html('公開処理に失敗しました。');
					$(config.alertBox).fadeIn(500);
				}
			},
			initList: function() {
				var config = $.baserAjaxDataList.config;
				$(config.dataList+" tbody tr .btn-publish").hide();
				$(config.dataList+" tbody tr.unpublish .btn-publish").show();
			}
		},
	/**
	 * 非公開処理
	 */
		unpublish: {
			button: '.btn-unpublish',
			confirm: '',
			result: function(row, result) {
				var config = $.baserAjaxDataList.config;
				if(result) {
					row.removeClass('publish');
					row.addClass('disablerow');
					row.addClass('unpublish');
					row.find('.status').html('―');
					$.baserAjaxDataList.config.methods.unpublish.initList();
					$.baserAjaxDataList.config.methods.publish.initList();
				} else {
					$(config.alertBox).html('非公開処理に失敗しました。');
					$(config.alertBox).fadeIn(500);
				}
			},
			initList: function() {
				var config = $.baserAjaxDataList.config;
				$(config.dataList+" tbody tr .btn-unpublish").hide();
				$(config.dataList+" tbody tr.publish .btn-unpublish").show();
			}
		}
	}
});