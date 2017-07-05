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
 * [ADMIN] ウィジェットエリア編集
 */
$this->BcBaser->js('admin/widget_areas/form', false, ['id' => 'AdminWidgetFormScript',
	'data-delWidgetUrl' => $this->BcBaser->getUrl(array('controller' => 'widget_areas', 'action' => 'del_widget', $this->BcForm->value('WidgetArea.id'))),
	'data-currentAction' =>$this->request->action
]);
?>


<?php if ($this->request->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('WidgetArea', array('url' => array('action' => 'add'))) ?>
<?php elseif ($this->request->action == 'admin_edit'): ?>
	<?php echo $this->BcForm->create('WidgetArea', array('url' => array('action' => 'update_title', 'id' => false))) ?>
<?php endif ?>

<?php echo $this->BcForm->hidden('WidgetArea.id') ?>

<?php echo $this->BcForm->label('WidgetArea.name', 'ウィジェットエリア名') ?>&nbsp;
<?php echo $this->BcForm->input('WidgetArea.name', array('type' => 'text', 'size' => 40, 'autofocus' => true)) ?>&nbsp;
<span class="submit"><?php echo $this->BcForm->end(array('label' => 'エリア名を保存する', 'div' => false, 'class' => 'button btn-red', 'id' => 'WidgetAreaUpdateTitleSubmit')) ?></span>
<?php $this->BcBaser->img('admin/ajax-loader-s.gif', array('style' => 'vertical-align:middle;display:none', 'id' => 'WidgetAreaUpdateTitleLoader')) ?>
<?php echo $this->BcForm->error('WidgetArea.name') ?>

<?php if (!empty($widgetInfos)): ?>

	<?php echo $this->BcForm->create('WidgetArea', array('url' => array('action' => 'update_sort', $this->BcForm->value('WidgetArea.id'), 'id' => false))) ?>
	<?php echo $this->BcForm->input('WidgetArea.sorted_ids', array('type' => 'hidden')) ?>
	<?php echo $this->BcForm->end() ?>

	<div id="WidgetSetting" class="clearfix" >

		<!-- 利用できるウィジェット -->
		<div id="SourceOuter">
			<div id="Source">

				<h2>利用できるウィジェット</h2>
				<?php foreach ($widgetInfos as $widgetInfo) : ?>
					<h3><?php echo $widgetInfo['title'] ?></h3>
					<?php
					$widgets = array();
					foreach ($widgetInfo['paths'] as $path) {
						$Folder = new Folder($path);
						$files = $Folder->read(true, true, true);
						$widgets = array();
						foreach ($files[1] as $file) {
							$widget = array('name' => '', 'title' => '', 'description' => '', 'setting' => '');
							ob_start();
							$key = 'Widget';
							// タイトルや説明文を取得する為、elementを使わず、includeする。
							// コントローラーでインクルードした場合、コントローラー内でヘルパ等が読み込まれていないのが原因で
							// エラーとなるのでここで読み込む
							include $file;
							$widget['name'] = basename($file, $this->ext);
							$widget['title'] = $title;
							$widget['description'] = $description;
							$widget['setting'] = ob_get_contents();
							$widgets[] = $widget;
							ob_end_clean();
						}
					}
					?>
					<?php foreach ($widgets as $widget): ?>

						<div class="ui-widget-content draggable widget" id="Widget<?php echo Inflector::camelize($widget['name']) ?>">
							<div class="head"><?php echo $widget['title'] ?></div>
						</div>

						<div class="description"><?php echo $widget['description'] ?></div>

						<div class="ui-widget-content sortable widget template <?php echo $widget['name'] ?>" id="<?php echo Inflector::camelize($widget['name']) ?>">
							<div class="clearfix">
								<div class="widget-name display-none"><?php echo $widget['name'] ?></div>
								<div class="del">削除</div>
								<div class="action">設定</div>
								<div class="head"><?php echo $widget['title'] ?></div>
							</div>
							<div class="content" style="text-align:right">
								<p class="widget-name"><small><?php echo $widget['title'] ?></small></p>
								<?php echo $this->BcForm->create('Widget', array('url' => array('controller' => 'widget_areas', 'action' => 'update_widget', $this->BcForm->value('WidgetArea.id')), 'class' => 'form')) ?>
								<?php echo $this->BcForm->input('Widget.id', array('type' => 'hidden', 'class' => 'id')) ?>
								<?php echo $this->BcForm->input('Widget.type', array('type' => 'hidden', 'value' => $widget['title'])) ?>
								<?php echo $this->BcForm->input('Widget.element', array('type' => 'hidden', 'value' => $widget['name'])) ?>
								<?php echo $this->BcForm->input('Widget.plugin', array('type' => 'hidden', 'value' => $widgetInfo['plugin'])) ?>
								<?php echo $this->BcForm->input('Widget.sort', array('type' => 'hidden')) ?>
								<?php echo $this->BcForm->label('Widget.name', 'タイトル') ?>&nbsp;
								<?php echo $this->BcForm->input('Widget.name', array('type' => 'text', 'class' => 'name')) ?><br />
								<?php echo $widget['setting'] ?><br />
								<?php $this->BcBaser->img('admin/ajax-loader-s.gif', array('style' => 'vertical-align:middle;display:none', 'id' => 'WidgetUpdateWidgetLoader', 'class' => 'loader')) ?>
								<?php echo $this->BcForm->input('Widget.use_title', array('type' => 'checkbox', 'label' => 'タイトルを表示', 'class' => 'use_title', 'checked' => 'checked')) ?>
								<?php echo $this->BcForm->input('Widget.status', array('type' => 'checkbox', 'label' => '利用する', 'class' => 'status')) ?>
								<?php echo $this->BcForm->end(array('label' => '保存', 'div' => false, 'id' => 'WidgetUpdateWidgetSubmit', 'class' => 'button')) ?>
							</div>
						</div>
					<?php endforeach ?>
				<?php endforeach ?>
			</div>
		</div>

		<!-- 利用中のウィジェット -->
		<div id="TargetOuter">
			<div id="Target">

				<h2>利用中のウィジェット <?php $this->BcBaser->img('admin/ajax-loader-s.gif', array(
						'style' => 'vertical-align:middle;display:none',
						'id' => 'WidgetAreaUpdateSortLoader',
						'class' => 'loader')); ?></h2>

				<?php if ($this->BcForm->value('WidgetArea.widgets')): ?>
					<?php foreach ($this->BcForm->value('WidgetArea.widgets') as $widget): ?>

						<?php $key = key($widget) ?>
						<?php $enabled = '' ?>
						<?php if ($widget[$key]['status']): ?>
				<?php $enabled = ' enabled' ?>
			<?php endif ?>

						<div class="ui-widget-content sortable widget setting <?php echo $widget[$key]['element'] ?><?php echo $enabled ?>" id="Setting<?php echo $widget[$key]['id'] ?>">
							<div class="clearfix">
								<div class="widget-name display-none"><?php echo $widget[$key]['element'] ?></div>
								<div class="del">削除</div>
								<div class="action">設定</div>
								<div class="head"><?php echo $widget[$key]['name'] ?></div>
							</div>
							<div class="content" style="text-align:right">
								<p><small><?php echo $widget[$key]['type'] ?></small></p>
								<?php echo $this->BcForm->create('Widget', array('url' => array('controller' => 'widget_areas', 'action' => 'update_widget', $this->BcForm->value('WidgetArea.id'), 'id' => false), 'class' => 'form', 'id' => 'WidgetUpdateWidgetForm' . $widget[$key]['id'])) ?>
								<?php echo $this->BcForm->input($key . '.id', array('type' => 'hidden', 'class' => 'id')) ?>
								<?php echo $this->BcForm->input($key . '.type', array('type' => 'hidden')) ?>
								<?php echo $this->BcForm->input($key . '.element', array('type' => 'hidden')) ?>
								<?php echo $this->BcForm->input($key . '.plugin', array('type' => 'hidden')) ?>
								<?php echo $this->BcForm->input($key . '.sort', array('type' => 'hidden')) ?>
								<?php echo $this->BcForm->label($key . 'name', 'タイトル') ?>&nbsp;
								<?php echo $this->BcForm->input($key . '.name', array('type' => 'text', 'class' => 'name')) ?><br />
								<?php $this->BcBaser->element('widgets/' . $widget[$key]['element'], array('key' => $key, 'plugin' => $widget[$key]['plugin'], 'mode' => 'edit'), array('plugin' => $widget[$key]['plugin'])) ?><br />
								<?php $this->BcBaser->img('admin/ajax-loader-s.gif', array('style' => 'vertical-align:middle;display:none', 'id' => 'WidgetUpdateWidgetLoader' . $widget[$key]['id'], 'class' => 'loader')) ?>
								<?php echo $this->BcForm->input($key . '.use_title', array('type' => 'checkbox', 'label' => 'タイトルを表示', 'class' => 'use_title')) ?>
								<?php echo $this->BcForm->input($key . '.status', array('type' => 'checkbox', 'label' => '利用する', 'class' => 'status')) ?>
								<?php echo $this->BcForm->end(array('label' => '保存', 'div' => false, 'id' => 'WidgetUpdateWidgetSubmit' . $widget[$key]['id'], 'class' => 'button')) ?>
							</div>
						</div>
		<?php endforeach; ?>
	<?php endif; ?>
			</div>
		</div>
	</div>
<?php endif; ?>
