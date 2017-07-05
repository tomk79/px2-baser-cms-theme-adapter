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
 * [ADMIN] パーミッション管理メニュー
 */
?>


<?php if ($usePermission): ?>
	<tr>
		<th>アクセス制限設定管理メニュー</th>
		<td>
			<ul class="cleafix">
				<li><?php $this->BcBaser->link('アクセス制限設定一覧', array('controller' => 'permissions', 'action' => 'index', $this->request->params['pass'][0])) ?></li>
				<li><?php $this->BcBaser->link('アクセス制限設定新規追加', array('controller' => 'permissions', 'action' => 'add', $this->request->params['pass'][0])) ?></li>
			</ul>
		</td>
	</tr>
<?php endif; ?>