<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Config
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * ブログインストーラー
 *
 * @package			Blog.Config
 */

/**
 * データベース初期化
 */
	$this->Plugin->initDb('Blog');

/**
 * ブログ記事の投稿日を更新
 */
	$BlogPost = ClassRegistry::init('Blog.BlogPost');
	$BlogPost->contentSaving = false;
	$datas = $BlogPost->find('all', array('recursive' => -1));
	if ($datas) {
		$ret = true;
		foreach ($datas as $data) {
			$data['BlogPost']['posts_date'] = date('Y-m-d H:i:s');
			unset($data['BlogPost']['eye_catch']);
			$BlogPost->set($data);
			if (!$BlogPost->save($data)) {
				$ret = false;
			}
		}
	}