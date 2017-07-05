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
 * [管理画面] サイト設定 フォーム
 */
?>


<h2>baserCMS環境</h2>

<ul class="section">
	<li>設置フォルダ： <?php echo ROOT . DS ?></li>
	<li>セーフモード：<?php if ($safeModeOn): ?>On<?php else: ?>Off<?php endif ?>
	<li>データベース： <?php echo $datasource ?></li>
	<li>baserCMSバージョン： <?php echo $baserVersion ?></li>
	<li>CakePHPバージョン： <?php echo $cakeVersion ?></li>
</ul>

<h2>PHP環境</h2>

<iframe src="<?php $this->BcBaser->url(array('action' => 'phpinfo')) ?>" class="phpinfo" width="100%" height="100%" style="min-height:600px"></iframe>