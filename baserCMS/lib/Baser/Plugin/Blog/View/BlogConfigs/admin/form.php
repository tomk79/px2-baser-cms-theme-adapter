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
 * [ADMIN] ブログ設定 フォーム
 *
 * 1.6.10 では利用していない＆不完全
 */
?>


<!-- title -->
<h2><?php $this->BcBaser->contentsTitle() ?></h2>

<h3>WordPressデータの取り込み</h3>
<p>WordPressから出力したXMLデータを取込みます。（<a href="http://ja.wordpress.org/" target="_blank">WordPress</a> 2.8.4 のみ動作確認済）</p>

<div class="align-center">
	<?php echo $this->BcForm->create('BlogPost', ['url' => ['action' => 'import'], 'enctype' => 'multipart/form-data']) ?>
	<?php echo $this->BcForm->input('Import.blog_content_id', array('type' => 'select', 'options' => $blogContentList)) ?>
	<?php echo $this->BcForm->input('Import.user_id', array('type' => 'select', 'options' => $userList)) ?>
	<?php echo $this->BcForm->input('Import.file', array('type' => 'file')) ?>
	<?php echo $this->BcForm->end(array('label' => '取り込む', 'div' => false, 'class' => 'btn-orange button')) ?>
</div>
