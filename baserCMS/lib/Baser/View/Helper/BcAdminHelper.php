<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * Include files
 */
App::uses('AppHelper', 'View/Helper');

/**
 * BcAdminヘルパー
 *
 * @package Baser.View.Helper
 */
class BcAdminHelper extends AppHelper {

/**
 * 管理システムグローバルメニューの利用可否確認
 * 
 * @return boolean
 */
	public function isAdminGlobalmenuUsed() {
		if (!BC_INSTALLED) {
			return false;
		}
		if (Configure::read('BcRequest.isUpdater')) {
			return false;
		}
		$user = $this->_View->get('user');
		if (!$user) {
			return false;
		}
		$UserGroup = ClassRegistry::init('UserGroup');
		return $UserGroup->isAdminGlobalmenuUsed($user['user_group_id']);
	}

/**
 * ログインユーザーがシステム管理者かチェックする
 * 
 * @return boolean 
 */
	public function isSystemAdmin() {
		$user = $this->_View->getVar('user');
		if (empty($this->request->params['admin']) || !$user) {
			return false;
		}
		if ($user['user_group_id'] == Configure::read('BcApp.adminGroupId')) {
			return true;
		}
		return false;
	}

}
