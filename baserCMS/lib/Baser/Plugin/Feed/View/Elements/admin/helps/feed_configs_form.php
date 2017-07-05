<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] フィード設定一覧　ヘルプ
 */
?>


<p>フィード設定の基本項目を入力します。<br />
	フィードごとにデザインを変更する場合には、画面下の「オプション」をクリックしてテンプレート名を変更します。<br />
	<small>※ テンプレート名を変更した場合は、新しい名称のテンプレートを作成しアップロードする必要があります。</small><br />
	<a href="http://basercms.net/manuals/designers/7.html" target="_blank" class="outside-link">フィード読み込み部分のテンプレートを変更する</a></p>
<ul>
	<li>一つの設定につき、フィードは複数登録する事ができます。複数登録した場合は、複数のフィードを合わせた上で日付順に並び替えられます。</li>
	<li>フィードを追加するには、画面下の「フィード一覧」の「新規追加」ボタンをクリックします。</li>
</ul>

<?php if ($this->request->action == 'admin_edit'): ?>
<div class="section">
	<h3 id="headHowTo">フィードの読み込み方法</h3>
	<p>以下のjavascriptを読み込みたい場所に貼り付けてください。</p>
	<textarea cols="100" rows="2" onclick="this.select(0,this.value.length)" readonly="readonly">
<?php $this->BcBaser->js('/feed/ajax/' . $this->request->data['FeedConfig']['id']) ?>
	</textarea>
	<br />
	<p>また、フィードの読み込みにはjQueryが必要ですので事前に読み込んでおく必要があります。</p>
	<h4>jQueryの読み込み例</h4>
	<textarea cols="100" rows="2" onclick="this.select(0,this.value.length)" readonly="readonly"><?php echo $this->BcHtml->script('admin/vendors/jquery-2.1.4.min', array('once' => false)) ?></textarea>
</div>
<?php endif ?>
