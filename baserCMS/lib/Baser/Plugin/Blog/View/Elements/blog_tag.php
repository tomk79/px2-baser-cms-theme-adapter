<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [PUBLISH] タグ
 */
?>


<?php if (!empty($this->Blog->blogContent['tag_use'])): ?>
	<?php if (!empty($post['BlogTag'])) : ?>
		<div class="tag">タグ：<?php $this->Blog->tag($post) ?></div>
	<?php endif ?>
<?php endif ?>
