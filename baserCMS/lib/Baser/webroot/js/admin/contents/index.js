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
 * コンテンツ一覧
 */

$(function () {

	var contentsIndexSearchOpened = $("#SearchBoxOpened").html();
	
	$.bcTree.init({
		isAdmin: $("#AdminContentsIndexScript").attr('data-isAdmin'),
		isUseMoveContents: $("#AdminContentsIndexScript").attr('data-isUseMoveContents'),
		adminPrefix: $("#AdminContentsIndexScript").attr('data-adminPrefix'),
		editInIndexDisabled: $("#AdminContentsIndexScript").attr('data-editInIndexDisabled')
	});

	// マウスダウンイベント
	$(window).bind("mousedown", $.bcTree.updateShiftAndCtrlOnAnchor);

	// サイト変更時
	$("#ViewSettingSiteId").change(loadView);

	// 表示変更時
	$("input[name='data[ViewSetting][list_type]']").click(loadView);
	
	// 新規追加クリック時
	$("#BtnAddContent").click($.bcTree.showMenuByOuter);

	// ドラッグ＆ドロップイベント
	$(document).on("dnd_stop.vakata", $.bcTree.orderContent);
	$(document).on("dnd_start.vakata", $.bcTree.changeDnDCursor);

	$.bcUtil.disabledHideMessage = true;
	$($.bcTree).bind('loaded', function(){
		$.bcUtil.disabledHideMessage = false;
	});
	$($.baserAjaxDataList).bind('searchLoaded', function(){
		$.bcUtil.disabledHideMessage = false;
	});

	loadView();
	
	$.baserAjaxDataList.config.methods.del.confirm = 'コンテンツをゴミ箱に移動してもよろしいですか？';
	$.baserAjaxBatch.config.methods.del.confirm = '選択したデータを全てゴミ箱に移動します。よろしいですか？\n※ エイリアスは直接削除します。';
	$.baserAjaxBatch.config.methods.unpublish.result = function() {
		$.bcUtil.showLoader();
		loadTable();
	};
	$.baserAjaxBatch.config.methods.publish.result = function() {
		$.bcUtil.showLoader();
		loadTable();
	};
	$.baserAjaxDataList.config.methods.publish.result = null;
	$.baserAjaxDataList.config.methods.unpublish.result = null;
	$.baserAjaxDataList.config.methods.copy.result = function(row, result) {
		// ローダーが一瞬切れるので強制的に表示
		$.bcUtil.showLoader();
		$("#ToTop a").click();
		loadTable();
		$.bcUtil.showNoticeMessage('ターゲットと同じフォルダにコピー「' + $.parseJSON(result).title + '」を作成しました。一覧に表示されていない場合は検索してください。');
	};
	$.baserAjaxDataList.init();
	$.baserAjaxBatch.init({ url: $.baseUrl + '/' + $.bcTree.config.adminPrefix + '/contents/ajax_batch'});
	
	$("#Search").before($("#ViewSetting"));
	
	$("#BtnSearchSubmit").click(function(){
		contentsIndexSearchOpened = true;
		$("input[name='data[ViewSetting][list_type]']:eq(1)").prop('checked', true);
		loadView();
	});
	// basreAjaxDataList側の検索ボタンクリックイベントを削除
	$._data($("#BtnSearchSubmit").get(0)).events.click.shift();
	$("#BtnOpenTree").click(function(){
		$.bcTree.jsTree.open_all();
	});
	$("#BtnCloseTree").click(function(){
		$.bcTree.jsTree.close_all();
		$.bcTree.jsTree.open_node($.bcTree.jsTree.get_json(), false, false);
	});
	
/**
 * 表示初期化
 */
	function loadView(e) {

		// サイトが変わった場合はリセット
		if(e !== undefined && e.target.id == 'ViewSettingSiteId') {
			$("#BtnSearchClear").click();
			$.ajax({
				url: $.baseUrl + '/' + $.bcTree.config.adminPrefix + '/contents/ajax_get_content_folder_list/' + $(this).val(),
				type: "GET",
				dataType: "json",
				beforeSend: function(){
					$("#ContentFolderId").prop('disabled', true);
				},
				complete: function(){
					$("#ContentFolderId").removeAttr("disabled");
				},
				success: function(result){
					$("#ContentFolderId").empty();
					var optionItems = [];
					optionItems.push(new Option("指定なし", ""));
					for (key in result) {
						optionItems.push(new Option(result[key].replace(/&nbsp;/g,"\u00a0"), key));
					}
					$("#ContentFolderId").append(optionItems);
				}
			});
		}
		var mode = $("#ViewSettingMode").val();
		var listType = $("input[name='data[ViewSetting][list_type]']:checked").val();
		if(listType == undefined || mode == 'trash') {
			listType = "1";
		}
		switch(listType) {
			case "1":
				$.bcTree.load();
				$("#BtnAddContent").parent().show();
				if($("#Search").is(":hidden")) {
					contentsIndexSearchOpened = false;
				} else {
					contentsIndexSearchOpened = true;
				}
				$("#GrpChangeTreeOpenClose").show();
				break;
			case "2":
				loadTable();
				$("#BtnAddContent").parent().hide();
				if(contentsIndexSearchOpened) {
					$("#Search").show();
				} else {
					$("#Search").hide();
				}
				$("#GrpChangeTreeOpenClose").hide();
				break;
		}

	}

/**
 * 表形式のリストをロードする 
 */
	function loadTable() {
		url = $.baseUrl + '/' + $.bcTree.config.adminPrefix + '/contents/index/site_id:' + $("#ViewSettingSiteId").val() + '/list_type:2';
		$("#ContentIndexForm").attr('action', url);
		$.baserAjaxDataList.search();
	}
	
});