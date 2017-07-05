<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Config
 * @since			baserCMS v 4.0.1
 * @license			http://basercms.net/license/index.html
 */

/**
 * 4.0.1 バージョン アップデートスクリプト
 *
 * ----------------------------------------
 * 　アップデートの仕様について
 * ----------------------------------------
 * アップデートスクリプトや、スキーマファイルの仕様については
 * 次のファイルに記載されいているコメントを参考にしてください。
 *
 * /lib/Baser/Controllers/UpdatersController.php
 *
 * スキーマ変更後、モデルを利用してデータの更新を行う場合は、
 * ClassRegistry を利用せず、モデルクラスを直接イニシャライズしないと、
 * スキーマのキャッシュが古いままとなるので注意が必要です。
 */

/**
 * contents テーブルデータ更新
 */
	$Content = ClassRegistry::init('Content');
	
    if($Content->updateAllUrl()) {
        $this->setUpdateLog('contents テーブルのデータ更新に成功しました。');
    } else {
        $this->setUpdateLog('contents テーブルのデータ更新に失敗しました。', true);
    }

/**
 * sites テーブル構造変更
 */
    if($this->loadSchema('4.0.1', '', 'sites', $filterType = 'alter')) {
        $this->setUpdateLog('sites テーブルの構造変更に成功しました。');
    } else {
        $this->setUpdateLog('sites テーブルの構造変更に失敗しました。', true);
    }
