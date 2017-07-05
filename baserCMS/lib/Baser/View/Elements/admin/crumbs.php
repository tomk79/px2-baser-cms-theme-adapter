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
 * [ADMIN] パンくずナビゲーション
 */
if ($this->viewPath != 'dashboard') {
	$this->BcBaser->addCrumb($this->BcBaser->getImg('admin/btn_home.png', array('width' => 15, 'height' => 12, 'alt' => 'Home')), array('plugin' => null, 'controller' => 'dashboard'));
}
$crumbs = $this->BcBaser->getCrumbs();
if (!empty($crumbs)) {
	foreach ($crumbs as $key => $crumb) {
		if ($this->BcArray->last($crumbs, $key + 1)) {
			if ($crumbs[$key + 1]['name'] == $crumb['name']) {
				continue;
			}
		}
		if ($this->BcArray->last($crumbs, $key)) {
			if ($this->viewPath != 'home' && $crumb['name']) {
				$this->BcBaser->addCrumb('<strong>' . $crumb['name'] . '</strong>');
			} elseif ($this->name == 'CakeError') {
				$this->BcBaser->addCrumb('<strong>404 NOT FOUND</strong>');
			}
		} else {
			$this->BcBaser->addCrumb(strip_tags($crumb['name']), $crumb['url']);
		}
	}
}
?>


<div id="Crumb">
	<?php if (!empty($user)): ?>
		<?php $this->BcBaser->crumbs(' &gt; ') ?>&nbsp;
	<?php else: ?>
		&nbsp;
	<?php endif ?>
<!-- / #Crumb  --></div>
