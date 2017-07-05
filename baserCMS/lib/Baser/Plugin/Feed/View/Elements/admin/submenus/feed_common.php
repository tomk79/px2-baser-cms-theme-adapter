<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] フィード設定共通メニュー
 */
?>


<tr>
	<th>フィード設定メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('フィード設定一覧', array('action' => 'index')) ?></li>
			<li><?php $this->BcBaser->link('フィード設定新規追加', array('action' => 'add')) ?></li>
<?php if ($this->params['controller'] == 'feed_configs' && $this->action == 'admin_index'): ?>
			<li><?php $this->BcBaser->link('フィードキャッシュ削除', array('action' => 'delete_cache'), ['class' => 'submit-token'], 'フィードのキャッシュを削除します。いいですか？') ?></li>
<?php endif ?>
		</ul>
	</td>
</tr>
