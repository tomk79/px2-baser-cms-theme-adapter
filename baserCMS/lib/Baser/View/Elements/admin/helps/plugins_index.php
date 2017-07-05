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
 * [ADMIN] プラグイン一覧　ヘルプ
 */
?>


<p>baserCMSのプラグインの管理を行います。<br />
	初期状態では、メールフォーム・フィードリーダー・ブログの３つのプラグインが標準プラグインとして同梱されており、
	インストールも完了しています。各プラグインの<?php $this->BcBaser->img('admin/icn_tool_manage.png') ?>から各プラグインの管理が行えます。</p>
<div class="example-box">
	<div class="head">新しいプラグインのインストール方法</div>
	<ol>
		<li>app/Plugin/ フォルダに、入手したプラグインのフォルダをアップロードします。</li>
		<li>プラグイン一覧に、新しいプラグインが表示されますので、その行の<?php $this->BcBaser->img('admin/icn_tool_install.png') ?>をクリックします。</li>
		<li>登録画面が表示されますので「登録」ボタンをクリックしてインストールを完了します。</li>
	</ol>
</div>