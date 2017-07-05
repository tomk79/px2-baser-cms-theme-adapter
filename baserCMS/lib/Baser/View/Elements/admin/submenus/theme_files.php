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
 * [ADMIN] テーマファイル管理メニュー
 */
$types = array(
	'Layouts'	=> 'レイアウト',
	'Elements'	=> 'エレメント',
	'Emails'	=> 'Eメール',
	'etc'		=> 'コンテンツ',
	'css'		=> 'CSS',
	'img'		=> 'イメージ',
	'js'		=> 'Javascript'
);
if ($theme == 'core') {
	$themeFiles = array(0 => array('name' => '', 'title' => 'コア'));
	$Plugin = ClassRegistry::init('Plugin');
	$plugins = $Plugin->find('all', array('fields' => array('name', 'title')));
	$themeFiles = am($themeFiles, Hash::extract($plugins, '{n}.Plugin'));
} else {
	$themeFiles = array(0 => array('name' => '', 'title' => $theme));
}
?>


<?php foreach ($themeFiles as $themeFile): ?>
	<tr>
		<th>[<?php echo $themeFile['title'] ?>] テーマ管理メニュー</th>
		<td>
			<ul class="cleafix">
				<?php foreach ($types as $key => $type): ?>
					<li><?php $this->BcBaser->link($type . '一覧', array('action' => 'index', $theme, $themeFile['name'], $key)) ?></li>
				<?php endforeach ?>
			</ul>
		</td>
	</tr>
<?php endforeach ?>