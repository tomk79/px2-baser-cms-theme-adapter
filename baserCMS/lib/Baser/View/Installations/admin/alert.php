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

/**
 * [PUBLISH] インストール警告ページ
 */
?>


<p>インストールを開始するにはデバッグモードが -1 である必要があります。</p>
<p>デバッグモードを変更するには次の手順のとおり操作してください。</p>

<ul>
	<li>次のファイルを開きます。<br />
		<pre>/app/Config/install.php</pre>
	<li>
	<li>install.phpより次の行を見つけます。<br />
		<pre>Configure::write('debug', 0);</pre>
	</li>
	<li>0 の部分を、 -1 に書き換えます。</li>
	<li>編集したファイルをサーバーにアップロードします。</li>
</ul>

<ul><li><?php $this->BcBaser->link('baserCMSを初期化するにはコチラから', '/installations/reset') ?></li></ul>
