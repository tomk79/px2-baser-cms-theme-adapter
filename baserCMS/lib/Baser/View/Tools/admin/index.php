<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<div class="section">
	<h2>サーバーキャッシュ削除</h2>
	<p>baserCMSは、表示速度向上の為、サーバーサイドのキャッシュ機構利用しています。<br>これによりテンプレートを直接編集した際など、変更内容が反映されない場合がありますので、その際には、サーバーサイドのキャッシュを削除します。</p>
	<?php $this->BcBaser->link('サーバーキャッシュを削除する', array('controller' => 'site_configs', 'action' => 'del_cache'), array('class' => 'submit-token button-small', 'confirm' => "サーバーキャッシュを削除します。いいですか？")) ?>　
</div>

<div class="section">
    <h2>コンテンツ管理</h2>
    <p>コンテンツ管理のツリー構造で並べ替えがうまくいかなくなった場合に、ツリー構造をリセットして正しいデータの状態に戻します。リセットを実行した場合、階層構造はリセットされてしまうのでご注意ください。</p>
	<?php $this->BcBaser->link('ツリー構造をチェックする', array('controller' => 'tools', 'action' => 'verity_contents_tree'), array('class' => 'submit-token button-small')) ?>　
	<?php $this->BcBaser->link('ツリー構造リセット', array('controller' => 'tools', 'action' => 'reset_contents_tree'), array('class' => 'submit-token button-small', 'confirm' => "コンテンツ管理のツリー構造をリセットします。本当によろしいですか？")) ?>　
</div>

<div class="section">
	<h2>固定ページテンプレート</h2>
	<p>別サーバーへの移設時には、固定ページ機能を正常動作させる為、固定ページテンプレート書出を実行してください。<br>また、固定ページテンプレートを直接編集した場合、データベースに反映する為には、固定ページテンプレート読込を実行します。</p>
	<?php $this->BcBaser->link('固定ページテンプレート書出', array('controller' => 'pages', 'action' => 'write_page_files'), array('class' => 'submit-token button-small', 'confirm' => "データベース内のページデータを、ページテンプレートとして /app/View/Pages 内に全て書出します。本当によろしいですか？")) ?>　
	<?php $this->BcBaser->link('固定ページテンプレート読込', array('controller' => 'pages', 'action' => 'entry_page_files'), array('class' => 'submit-token button-small', 'confirm' => "/app/View/Pages フォルダ内のページテンプレートを全て読み込みます。本当によろしいですか？")) ?>　
</div>

<div class="section">
	<h2>アセットファイル</h2>
	<p>管理システム用のアセットファイル（画像、CSS、Javascript）を削除したり、コアパッケージよりサイトルートフォルダに再配置します。<br>削除した場合、直接コアパッケージのアセットファイルを参照する事になりますが、表示速度が遅くなりますので注意が必要です。</p>
	<?php $this->BcBaser->link('アセットファイル削除', array('controller' => 'tools', 'action' => 'delete_admin_assets'), array('class' => 'submit-token button-small', 'confirm' => "サイトルートに配置された、管理システム用のアセットファイルを削除します。本当によろしいですか？")) ?>　
	<?php $this->BcBaser->link('アセットファイル再配置', array('controller' => 'tools', 'action' => 'deploy_admin_assets'), array('class' => 'submit-token button-small', 'confirm' => "管理システム用のアセットファイルをサイトルートに再配置します。本当によろしいですか？")) ?>　
</div>

<div class="section">
	<h2>スペシャルサンクスクレジット</h2>
	<p>baserCMSの開発や運営、普及にご協力頂いた方々をご紹介します。</p>
	<?php $this->BcBaser->link('クレジットを表示', 'javascript:void(0)', array('class' => 'button-small', 'id' => 'BtnCredit')) ?>
</div>