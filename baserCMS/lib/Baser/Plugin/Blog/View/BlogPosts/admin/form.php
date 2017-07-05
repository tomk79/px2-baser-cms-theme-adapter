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
 * [ADMIN] ブログ記事 フォーム
 */
if($this->request->params['Site']['use_subdomain']) {
	$targetSite = BcSite::findByUrl($this->request->params['Content']['url']);
	$previewUrl = $targetSite->getPureUrl($this->request->params['Content']['url'] . 'archives/' . $this->BcForm->value('BlogPost.no')) . '?host=' . $targetSite->host;
} else {
	$previewUrl = $this->request->params['Content']['url'] . '/archives/' . $this->BcForm->value('BlogPost.no');
}
$this->BcBaser->css('admin/ckeditor/editor', array('inline' => true));
$statuses = array(0 => '非公開', 1 => '公開');
$this->BcBaser->link('&nbsp;', array('controller' => 'blog', 'action' => 'preview', $blogContent['BlogContent']['id'], $previewId, 'view'), array('style' => 'display:none', 'id' => 'LinkPreview'));
$this->BcBaser->js('Blog.admin/blog_posts/form', false, array('id' => 'AdminBlogBLogPostsEditScript',
	'data-fullurl' => $this->BcContents->getUrl($this->request->params['Content']['url'] . '/archives/' . $this->BcForm->value('BlogPost.no'), true, $this->request->params['Site']['use_subdomain']),
	'data-previewurl' => $previewUrl
));
?>


<div id="AddTagUrl" style="display:none"><?php echo $this->BcBaser->url(array('plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'ajax_add')) ?></div>
<div id="AddBlogCategoryUrl" style="display:none"><?php echo $this->BcBaser->url(array('plugin' => 'blog', 'controller' => 'blog_categories', 'action' => 'ajax_add', $blogContent['BlogContent']['id'])) ?></div>


<?php /* BlogContent.idを第一引数にしたいが為にURL直書き */ ?>
<?php if ($this->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('BlogPost', array('type' => 'file', 'url' => array('controller' => 'blog_posts', 'action' => 'add', $blogContent['BlogContent']['id']), 'id' => 'BlogPostForm')) ?>
	<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $this->BcForm->create('BlogPost', array('type' => 'file', 'url' => array('controller' => 'blog_posts', 'action' => 'edit', $blogContent['BlogContent']['id'], $this->BcForm->value('BlogPost.id'), 'id' => false), 'id' => 'BlogPostForm')) ?>
<?php endif; ?>
<?php echo $this->BcForm->input('BlogPost.id', array('type' => 'hidden')) ?>
<?php echo $this->BcForm->input('BlogPost.blog_content_id', array('type' => 'hidden', 'value' => $blogContent['BlogContent']['id'])) ?>
<?php echo $this->BcForm->hidden('BlogPost.mode') ?>


<?php if (empty($blogContent['BlogContent']['use_content'])): ?>
	<?php echo $this->BcForm->hidden('BlogPost.content') ?>
<?php endif ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
	<?php if ($this->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head" style="width:53px"><?php echo $this->BcForm->label('BlogPost.no', 'NO') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->value('BlogPost.no') ?>
				<?php echo $this->BcForm->input('BlogPost.no', array('type' => 'hidden')) ?>
			</td>
		</tr>
		<tr>
			<th class="col-head" style="width:53px"><?php echo $this->BcForm->label('BlogPost.url', 'URL') ?></th>
			<td class="col-input">
				<span class="url"><?php echo $this->BcBaser->getUri(urldecode($this->request->params['Content']['url']) . '/archives/' . $this->BcForm->value('BlogPost.no')) ?></span>　
				<?php echo $this->BcForm->button('URLコピー', ['class' => 'small-button', 'style' => 'font-weght:normal', 'id' => 'BtnCopyUrl']) ?>
			</td>
		</tr>
	<?php endif; ?>
	<?php if ($categories): ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.blog_category_id', 'カテゴリー') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogPost.blog_category_id', array('type' => 'select', 'options' => $categories, 'escape' => false)) ?>&nbsp;
				<?php if($hasNewCategoryAddablePermission): ?>
					<?php echo $this->BcForm->button('新しいカテゴリを追加', array('id' => 'BtnAddBlogCategory')) ?>
				<?php endif ?>
				<?php $this->BcBaser->img('admin/ajax-loader-s.gif', array('style' => 'vertical-align:middle;display:none', 'id' => 'BlogCategoryLoader', 'class' => 'loader')) ?>
				<?php echo $this->BcForm->error('BlogPost.blog_category_id') ?>
			</td>
		</tr>
	<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.name', 'タイトル') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogPost.name', array('type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true, 'counter' => true)) ?>
				<?php echo $this->BcForm->error('BlogPost.name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.eye_catch', 'アイキャッチ画像') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->file('BlogPost.eye_catch', array('imgsize' => 'thumb', 'width' => '300')) ?>
				<?php echo $this->BcForm->error('BlogPost.eye_catch') ?>
			</td>
		</tr>
	<?php if (!empty($blogContent['BlogContent']['use_content'])): ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.content', '概要') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->ckeditor('BlogPost.content', array(
					'editorWidth' => 'auto',
					'editorHeight' => '120px',
					'editorToolType' => 'simple',
					'editorEnterBr' => @$siteConfig['editor_enter_br']
				)); ?>
				<?php echo $this->BcForm->error('BlogPost.content') ?>
			</td>
		</tr>
	<?php endif ?>
	</table>
</div>

<div class="section" style="text-align: center">
	<?php
	echo $this->BcForm->input('BlogPost.detail', array_merge(array(
        'type' => 'editor',
		'editor' => @$siteConfig['editor'],
		'editorUseDraft' => true,
		'editorDraftField' => 'detail_draft',
		'editorWidth' => 'auto',
		'editorHeight' => '480px',
		'editorEnterBr' => @$siteConfig['editor_enter_br']
			), $editorOptions))
	?>
		<?php echo $this->BcForm->error('BlogPost.detail') ?>
</div>

<div class="section">
	<table cellpadding="0" cellspacing="0" class="form-table">
		<?php if (!empty($blogContent['BlogContent']['tag_use'])): ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogTag.BlogTag', 'タグ') ?></th>
			<td class="col-input">
				<div class="clearfix" id="BlogTags" style="padding:5px">
					<?php echo $this->BcForm->input('BlogTag.BlogTag', array('type' => 'select', 'multiple' => 'checkbox', 'options' => $this->BcForm->getControlSource('BlogPost.blog_tag_id'))); ?>
				</div>
				<?php echo $this->BcForm->error('BlogTag.BlogTag') ?>
				<?php echo $this->BcForm->input('BlogTag.name', array('type' => 'text')) ?>
				<?php echo $this->BcForm->button('新しいタグを追加', array('id' => 'BtnAddBlogTag')) ?>
				<?php $this->BcBaser->img('admin/ajax-loader-s.gif', array('style' => 'vertical-align:middle;display:none', 'id' => 'TagLoader', 'class' => 'loader')) ?>
			</td>
		</tr>
		<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.status', '公開状態') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('BlogPost.status', array('type' => 'radio', 'options' => $statuses)) ?>
				<?php echo $this->BcForm->error('BlogPost.status') ?>
				&nbsp;&nbsp;
				<?php echo $this->BcForm->dateTimePicker('BlogPost.publish_begin', array('size' => 12, 'maxlength' => 10), true) ?>
				&nbsp;〜&nbsp;
				<?php echo $this->BcForm->dateTimePicker('BlogPost.publish_end', array('size' => 12, 'maxlength' => 10), true) ?><br />
				<?php echo $this->BcForm->input('BlogPost.exclude_search', array('type' => 'checkbox', 'label' => 'サイト内検索の検索結果より除外する')) ?>
				<?php echo $this->BcForm->error('BlogPost.publish_begin') ?>
				<?php echo $this->BcForm->error('BlogPost.publish_end') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.user_id', '作成者') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php if (isset($user) && $user['user_group_id'] == Configure::read('BcApp.adminGroupId')): ?>
					<?php echo $this->BcForm->input('BlogPost.user_id', array(
						'type' => 'select',
						'options' => $users
					)); ?>
					<?php echo $this->BcForm->error('BlogPost.user_id') ?>
				<?php else: ?>
					<?php if (isset($users[$this->BcForm->value('BlogPost.user_id')])): ?>
					<?php echo $users[$this->BcForm->value('BlogPost.user_id')] ?>
					<?php endif ?>
					<?php echo $this->BcForm->hidden('BlogPost.user_id') ?>
				<?php endif ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('BlogPost.posts_date', '投稿日') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->dateTimePicker('BlogPost.posts_date', array('size' => 12, 'maxlength' => 10), true) ?>
				<?php echo $this->BcForm->error('BlogPost.posts_date') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<!-- button -->
<div class="submit">
	<?php if ($this->action == 'admin_add'): ?>
		<?php echo $this->BcForm->button('プレビュー', array('div' => false, 'class' => 'button', 'id' => 'BtnPreview')) ?>
		<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
	<?php elseif ($this->action == 'admin_edit'): ?>
		<?php echo $this->BcForm->button('プレビュー', array('div' => false, 'class' => 'button', 'id' => 'BtnPreview')) ?>
		<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
		<?php $this->BcBaser->link('削除', array('action' => 'delete', $blogContent['BlogContent']['id'], $this->BcForm->value('BlogPost.id')), array('class' => 'submit-token button', 'id' => 'BtnDelete'), sprintf('%s を本当に削除してもいいですか？\n※ ブログ記事はゴミ箱に入らず完全に消去されます。', $this->BcForm->value('BlogPost.name')), false); ?>
	<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>
