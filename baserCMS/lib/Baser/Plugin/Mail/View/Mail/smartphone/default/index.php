<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [PUBLISH] メールフォーム
 */
$this->BcBaser->css('admin/jquery-ui/ui.all', array('inline' => true));
$this->BcBaser->js(array('admin/vendors/jquery-ui-1.11.4.min', 'admin/vendors/i18n/ui.datepicker-ja'), false);
?>


<h1 class="contents-head">
	<?php $this->BcBaser->contentsTitle() ?>
</h1>

<h2 class="contents-head">入力フォーム</h2>

<div class="section mail-description">
	<?php $this->Mail->description() ?>
</div>

<div class="section mail-form">
	<?php $this->BcBaser->flash() ?>
	<?php $this->BcBaser->element('mail_form') ?>
</div>
