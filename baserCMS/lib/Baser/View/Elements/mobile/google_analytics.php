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
 * [PUBLISH] モバイル用 Google Analytics 画像タグ
 *
 * PHP5以上のみ対応
 */
?>
<!--nocache-->
	<?php
	$baseUrl = $this->BcBaser->getUrl("/mobile/ga");
	if (!empty($_SERVER["HTTP_REFERER"])) {
		$referer = $_SERVER["HTTP_REFERER"];
	} else {
		$referer = "-";
	}
	if (!empty($_SERVER["REQUEST_URI"])) {
		$path = $_SERVER["REQUEST_URI"];
	} else {
		$path = '';
	}

	$url = $baseUrl . "?";
	$url .= "&utmn=" . rand(0, 0x7fffffff);
	$url .= "&utmr=" . urlencode($referer);
	$url .= "&utmp=" . urlencode($path);
	$url .= "&guid=ON";
	echo '<img src="' . str_replace("&", "&amp;", $url) . '" width="1" height="1" />';
	?>
<!--/nocache-->