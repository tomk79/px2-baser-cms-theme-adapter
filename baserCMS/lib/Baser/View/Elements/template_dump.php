<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 3.0.10
 * @license			http://basercms.net/license/index.html
 */

/**
 * 利用しているテンプレート一覧
 *
 * デバッグモード２以上で表示
 */
 if(empty($this->_viewFilesLog)) return;
 ?>


<table class="cake-sql-log" id="cakeSqlLog_%s" summary="Cake SQL Log" cellspacing="0">
	<tr><th>Nr</th><th>Template</th></tr>
<?php $count = 1 ?>
<?php foreach($this->_viewFilesLog as $log): ?>
	<tr>
		<td><?php echo $count ?>.</td>
		<td><?php echo $log ?></td>
	</tr>
	<?php $count++ ?>
<?php endforeach ?>
</table>
