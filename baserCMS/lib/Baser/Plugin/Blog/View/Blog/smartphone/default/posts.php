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
 * [PUBLISH] 記事一覧
 *
 * BaserHelper::blogPosts( コンテンツ名, 件数 ) で呼び出す
 * （例）<?php $this->BcBaser->blogPosts('news', 3) ?>
 */
?>


<?php if ($posts): ?>
	<ul class="post-list">
		<?php foreach ($posts as $key => $post): ?>
			<?php $class = array('clearfix', 'post-' . ($key + 1)) ?>
			<?php if ($this->BcArray->first($posts, $key)): ?>
				<?php $class[] = 'first' ?>
			<?php elseif ($this->BcArray->last($posts, $key)): ?>
				<?php $class[] = 'last' ?>
			<?php endif ?>
			<li class="<?php echo implode(' ', $class) ?>">
				<?php $this->Blog->postLink($post, '<span class="date">' . $this->Blog->getPostDate($post, 'Y.m.d') . '</span><br />' . '<span class="title">' . $this->Blog->getPostTitle($post, false) . '</span>') ?>
			</li>
		<?php endforeach ?>
	</ul>
	<?php else: ?>
	<p class="no-data">記事がありません</p>
<?php endif ?>