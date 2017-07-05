<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] ウィジェットエリア一覧　ヘルプ
 */
?>


<p>一つのウィジェットエリアは、左側の「利用できるウィジェット」からお好きなウィジェットを複数選択して作成する事ができます。</p>
<ul>
	<li>まず、わかりやすい「ウィジェットエリア名」を決めて入力します。（例）サイドバー等</li>
	<li>「エリア名を保存する」ボタンをクリックすると「利用できるウィジェット」と「利用中のウィジェット」の二つの領域が表示されます</li>
	<li>「利用できるウィジェット」の中から利用したいウィジェットをドラッグして「利用中のウィジェット」の中でドロップします。</li>
	<li>ウィジェットの設定欄が開きますので必要に応じて入力し「保存」ボタンをクリックします。</li>
</ul>
<h5>ポイント</h5>
<ul>
	<li>「利用中のウィジェット」はドラッグアンドドロップで並び替える事ができます。</li>
	<li>一時的に利用しない場合は、削除せずにウィジェット設定の「利用する」チェックを外しておくと同じ設定のまま後で利用する事ができます。</li>
	<?php if ($this->request->action == 'admin_edit'): ?>
		<li>システム設定より設定できる標準ウィジェットエリアの他、個別にウィジェットを配置する場合は、テンプレートや、ページ記事中（ソース）に次のコードを貼り付けます。</li>
	<?php endif ?>
</ul>
<?php if ($this->request->action == 'admin_edit'): ?>
	<pre>&lt;?php $this->BcBaser->element('widget_area', array('no'=> <?php echo $this->BcForm->value('WidgetArea.id') ?> )) ?&gt;</pre>
<?php endif ?>