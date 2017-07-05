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
 * [PUBLISH] サイト内検索フォームウィジェット
 * 
 * $this->BcBaser->widgetArea('ウィジェットエリアNO') で呼び出す
 * 管理画面で設定されたウィジェットエリアNOは、 $widgetArea で参照できる
 */

if (Configure::read('BcRequest.isMaintenance')) {
	return;
}
if (!empty($this->passedArgs['num'])) {
	$url = array('plugin' => null, 'controller' => 'search_indices', 'action' => 'search', 'num' => $this->passedArgs['num']);
} else {
	$url = array('plugin' => null, 'controller' => 'search_indices', 'action' => 'search');
}
$folders = $this->BcContents->getContentFolderList($this->request->params['Site']['id'], ['excludeId' => $this->BcContents->getSiteRootId($this->request->params['Site']['id'])]);
?>


<div class="widget widget-search-box widget-search-box-<?php echo $id ?>">
	<?php echo $this->BcForm->create('SearchIndex', ['type' => 'get', 'url' => $url]) ?>
	<?php echo $this->BcForm->input('SearchIndex.q') ?>
	<?php echo $this->BcForm->hidden('SearchIndex.s', ['value' => $this->request->params['Site']['id']]) ?>
	<?php echo $this->BcForm->submit('検索', array('div' => false, 'class' => 'submit_button')) ?>
	<?php echo $this->BcForm->end() ?>
</div>