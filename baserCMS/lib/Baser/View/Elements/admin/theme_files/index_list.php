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
 * [ADMIN] テーマファイル一覧　テーブル
 */
?>


<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
<thead>
	<tr>
		<th style="width:160px" class="list-tool">
			<div>
				<?php if ($this->BcBaser->isAdminUser() && $theme != 'core'): ?>
				<?php echo $this->BcForm->checkbox('ListTool.checkall', array('title' => '一括選択')) ?>
				<?php echo $this->BcForm->input('ListTool.batch', array('type' => 'select', 'options' => array('del' => '削除'), 'empty' => '一括処理')) ?>
				<?php echo $this->BcForm->button('適用', array('id' => 'BtnApplyBatch', 'disabled' => 'disabled')) ?>
				<?php endif ?>
				<?php if ($path): ?>
					<?php $this->BcBaser->link($this->BcBaser->getImg('admin/up.gif', array('alt' => '上へ移動')), array('action' => 'index', $theme, $plugin, $type, dirname($path)), array('title' => '上へ移動')) ?>
				<?php endif ?>
			</div>
		</th>
		<th>フォルダ名／テーマファイル名</th>
	</tr>
</thead>
<tbody>
	<?php if (!empty($themeFiles)): ?>
		<?php foreach ($themeFiles as $data): ?>
			<?php $this->BcBaser->element('theme_files/index_row', array('data' => $data)) ?>
		<?php endforeach; ?>
	<?php else: ?>
	<tr>
		<td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
	<?php endif; ?>
</tbody>
</table>