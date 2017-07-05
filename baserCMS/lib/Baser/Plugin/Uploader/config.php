<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Uploader
 * @since			baserCMS v 3.0.10
 * @license			http://basercms.net/license/index.html
 */

$title = 'アップローダー';
$description = 'Webページやブログ記事で、画像等のファイルを貼り付ける事ができます。';
$author = 'baserCMS Users Community';
$url = 'http://basercms.net';
$adminLink = array('admin' => true, 'plugin' => 'uploader', 'controller' => 'uploader_files', 'action' => 'index');
if(!is_writable(WWW_ROOT.'files')){
	$viewFilesPath = str_replace(ROOT,'',WWW_ROOT).'files';
	$installMessage = '登録ボタンをクリックする前に、サーバー上の '.$viewFilesPath.' に書き込み権限を与えてください。';
}
