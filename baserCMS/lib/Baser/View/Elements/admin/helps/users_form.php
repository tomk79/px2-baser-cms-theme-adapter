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
 * [ADMIN] ユーザー編集　ヘルプ
 */
?>


<ul>
	<li>ログイン用のユーザーアカウントを登録する事ができます。</li>
	<?php if ($this->request->action == 'admin_edit'): ?><li>パスワード欄は変更する場合のみ入力します。</li><?php endif ?>
</ul>
