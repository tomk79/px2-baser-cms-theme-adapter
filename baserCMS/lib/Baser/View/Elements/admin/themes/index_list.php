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
 * [ADMIN] テーマ一覧　テーブル
 */
?>


<script type="text/javascript">
$(function(){
	$(".theme-popup").colorbox({inline:true, width:"60%"});
});
</script>


<div id="CurrentTheme" class="clearfix">
	<h2>現在のテーマ</h2>
	<?php if ($currentTheme): ?>
		<div class="current-theme-left">
			<div class="theme-screenshot">
				<?php if ($currentTheme['screenshot']): ?>
					<?php $this->BcBaser->img('/theme/' . $currentTheme['name'] . '/screenshot.png', array('alt' => $currentTheme['title'])) ?>
				<?php else: ?>
					<?php $this->BcBaser->img('admin/no-screenshot.png', array('alt' => $currentTheme['title'])) ?>
				<?php endif ?>
			</div>
			<p class="row-tools">
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_manage.png', array('alt' => 'テンプレート編集', 'class' => 'btn')), array('controller' => 'theme_files', 'action' => 'index', $currentTheme['name']), array('title' => 'テンプレート編集')) ?>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_edit.png', array('alt' => 'テーマ情報設定', 'class' => 'btn')), array('action' => 'edit', $currentTheme['name']), array('title' => 'テーマ情報設定')) ?>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/icn_tool_copy.png', array('alt' => 'テーマコピー', 'class' => 'btn')), array('action' => 'ajax_copy', $currentTheme['name']), array('title' => 'テーマコピー', 'class' => 'btn-copy')) ?>
			</p>
		</div>

		<div class="theme-info">
			<p class="theme-name"><strong><?php echo $currentTheme['title'] ?></strong>&nbsp;(&nbsp;<?php echo $currentTheme['name'] ?>&nbsp;)</p>
			<p class="theme-version">バージョン：<?php echo $currentTheme['version'] ?></p>
			<p class="theme-author">制作者：<?php if (!empty($currentTheme['url']) && !empty($currentTheme['author'])): ?>
					<?php $this->BcBaser->link($currentTheme['author'], $currentTheme['url'], array('target' => '_blank')) ?>
				<?php else: ?>
					<?php echo $currentTheme['author'] ?>
				<?php endif ?>
			</p>
		</div>
		<br /><br />
		<?php if ($defaultDataPatterns && $this->BcBaser->isAdminUser()): ?>
			<?php echo $this->BcForm->create('Theme', ['url' => ['action' => 'load_default_data_pattern']]) ?>
			<?php echo $this->BcForm->input('Theme.default_data_pattern', array('type' => 'select', 'options' => $defaultDataPatterns)) ?>
			<?php echo $this->BcForm->submit('初期データ読込', array('class' => 'button-small', 'div' => false, 'id' => 'BtnLoadDefaultDataPattern')) ?>
			<?php echo $this->BcForm->end() ?>
		<?php endif ?>
		<br /><br /><br /><br />
		<div class="theme-description"><?php echo nl2br($currentTheme['description']) ?></div>
	<?php else: ?>
		<p>現在、テーマが選択されていません。</p>
	<?php endif ?>
</div>

<ul class="list-panel clearfix">
	<?php if (!empty($datas)): ?>
		<?php foreach ($datas as $data): ?>
			<?php $this->BcBaser->element('themes/index_row', array('data' => $data)) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<?php if(strtotime('2014-03-31 17:00:00') >= time()): ?>
		<li class="no-data">変更できるテーマがありません。<br /><a href="http://basercms.net/themes/index" target="_blank">baserCMSの公式サイト</a>では無償のテーマが公開されています。</li>
		<?php else: ?>
		<li class="no-data">変更できるテーマがありません。<br /><a href="https://market.basercms.net/" target="_blank">baserマーケット</a>でテーマをダウンロードしましょう。</li>
		<?php endif ?>
	<?php endif; ?>
</ul>