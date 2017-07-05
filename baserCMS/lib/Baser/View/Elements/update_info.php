<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * コンテンツ更新情報
 */
?>


<?php if(!$this->BcBaser->isHome()): ?>
<div class="update-info clearfix">
	<dl>
<?php if($createdDate): ?>
		<dt>作成日</dt>
		<dd><?php echo $createdDate ?></dd>
<?php endif ?>
<?php if($modifiedDate): ?>
		<dt>最終更新日</dt>
		<dd><?php echo $modifiedDate ?></dd>
<?php endif ?>
	</dl>
</div>
<?php endif ?>