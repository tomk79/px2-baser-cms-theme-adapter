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
 * [ADMIN] ダッシュボード
 */
$this->BcBaser->js('admin/libs/jquery.bcDashboard', false);
?>


<div id="AlertMessage" class="message" style="display:none"></div>

<?php if($panels): ?>
	<?php foreacH($panels as $key => $templates): ?>
		<?php foreach($templates as $template): ?>
<div class="float-left" style="width:33%">
	<div class="panel-box">
		<?php if($key == 'Core'): ?>
			<?php echo $this->BcBaser->element('admin/dashboard/' . $template) ?>
		<?php else: ?>
			<?php echo $this->BcBaser->element($key . '.admin/dashboard/' . $template) ?>
		<?php endif ?>		
	</div>
</div>
		<?php endforeach ?>
	<?php endforeach ?>
<?php endif ?>