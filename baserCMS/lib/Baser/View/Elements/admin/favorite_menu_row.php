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
 * [ADMIN] よく使う項目　行
 */
?>


<li id="FavoriteRow<?php echo $favorite['Favorite']['name'] ?>">
	<?php $favorite['Favorite']['url'] = preg_replace('/^\/admin\//', '/' . BcUtil::getAdminPrefix() . '/', $favorite['Favorite']['url']) ?>
	<?php $this->BcBaser->link($favorite['Favorite']['name'], $favorite['Favorite']['url'], array('title' => Router::url($favorite['Favorite']['url'], true))) ?>
	<?php echo $this->BcForm->input('Favorite.id.' . $favorite['Favorite']['id'], array('type' => 'hidden', 'value' => $favorite['Favorite']['id'], 'class' => 'favorite-id')) ?>
	<?php echo $this->BcForm->input('Favorite.name.' . $favorite['Favorite']['id'], array('type' => 'hidden', 'value' => $favorite['Favorite']['name'], 'class' => 'favorite-name')) ?>
	<?php echo $this->BcForm->input('Favorite.url.' . $favorite['Favorite']['id'], array('type' => 'hidden', 'value' => $favorite['Favorite']['url'], 'class' => 'favorite-url')) ?>
</li>