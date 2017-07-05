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
 * [PUBLISH] インストーラー初期ページ
 */
?>


<div class="step-1">

	<div class="em-box"><?php echo Configure::read('BcApp.title') ?>のインストールを開始します。<br />
		よろしければ「インストール開始」ボタンをクリックしてください。</div>

	<div class="section">
		<p>baserCMSではファイルベースのデータベースをサポートしています。SQLite３ を利用すれば、インストールにデータベースサーバーは必要ありません。</p>

		<p><small>※ 膨大なデータの操作、データベースによる複雑な処理が必要な場合は、MySQLなどのデータベースサーバーの利用を推奨します。</small></p>
	</div>

	<div class="submit">
		<form action="<?php echo $this->request->base ?>/installations/step2" method="post">
			<button class='btn-red button' id='startbtn' type='submit' ><span>インストール開始</span></button>
		</form>
	</div>

</div>