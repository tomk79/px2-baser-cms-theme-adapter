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
 * [ADMIN] ヘルプ
 */
if (empty($help)) {
	return;
}
?>


<div id="Help" class="clearfix">

	<h2 class="head">
		<?php $this->BcBaser->img('admin/head_help.png', array('width' => 69, 'height' => 16, 'alt' => 'ヘルプ')) ?>
	</h2>

	<div class="body">
		<?php $this->BcBaser->element('helps/' . $help) ?>
	</div>

	<div class="clearfix close">
		<div id="CloseHelp">
			<a><?php $this->BcBaser->img('admin/btn_close.png', array('width' => 14, 'height' => 14, 'alt' => 'Close', 'class' => 'btn')) ?></a>
		</div>
	</div>

<!-- / #Help --></div>