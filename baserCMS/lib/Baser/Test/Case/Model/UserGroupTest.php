<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Model
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */
App::uses('UserGroup', 'Model');
App::uses('Permission', 'Model');

/**
 * UserGroupTest class
 * 
 * class NonAssosiationUserGroup extends UserGroup {
 *  public $name = 'UserGroup';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 */
class UserGroupTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.Permission',
		'baser.Default.Page',
		'baser.Default.Favorite',
		'baser.Default.Site',
		'baser.Default.SiteConfig',
		'baser.Default.Content'
	);

	public function setUp() {
		parent::setUp();
		$this->UserGroup = ClassRegistry::init('UserGroup');
		$this->Permission = ClassRegistry::init('Permission');
	}

	public function tearDown() {
		unset($this->UserGroup);
		unset($this->Permission);
		parent::tearDown();
	}

/**
 * validate
 */
	public function test必須チェック() {
		$this->UserGroup->create(array(
			'UserGroup' => array(
				'name' => '',
				'title' => '',
				'auth_prefix' => '',
			)
		));
		$this->assertFalse($this->UserGroup->validates());

		$this->assertArrayHasKey('name', $this->UserGroup->validationErrors);
		$this->assertEquals('ユーザーグループ名を入力してください。', current($this->UserGroup->validationErrors['name']));
		$this->assertArrayHasKey('title', $this->UserGroup->validationErrors);
		$this->assertEquals('表示名を入力してください。', current($this->UserGroup->validationErrors['title']));
		$this->assertArrayHasKey('auth_prefix', $this->UserGroup->validationErrors);
		$this->assertEquals('認証プレフィックスを入力してください。', current($this->UserGroup->validationErrors['auth_prefix']));
	}

	public function test桁数チェック正常系() {
		$this->UserGroup->create(array(
			'UserGroup' => array(
				'name' => '12345678901234567890123456789012345678901234567890',
				'title' => '12345678901234567890123456789012345678901234567890',
			)
		));
		$this->assertTrue($this->UserGroup->validates());
	}

	public function test桁数チェック異常系() {
		$this->UserGroup->create(array(
			'UserGroup' => array(
				'name' => '123456789012345678901234567890123456789012345678901',
				'title' => '123456789012345678901234567890123456789012345678901',
			)
		));
		$this->assertFalse($this->UserGroup->validates());

		$this->assertArrayHasKey('name', $this->UserGroup->validationErrors);
		$this->assertEquals('ユーザーグループ名は50文字以内で入力してください。', current($this->UserGroup->validationErrors['name']));
		$this->assertArrayHasKey('title', $this->UserGroup->validationErrors);
		$this->assertEquals('表示名は50文字以内で入力してください。', current($this->UserGroup->validationErrors['title']));
	}

	public function test半角英数チェック異常系() {
		$this->UserGroup->create(array(
			'UserGroup' => array(
				'name' => '１２３ａｂｃ',
			)
		));
		$this->assertFalse($this->UserGroup->validates());

		$this->assertArrayHasKey('name', $this->UserGroup->validationErrors);
		$this->assertEquals('ユーザーグループ名は半角のみで入力してください。', current($this->UserGroup->validationErrors['name']));
	}

	public function test既存ユーザーグループチェック異常系() {
		$this->UserGroup->create(array(
			'UserGroup' => array(
				'name' => 'admins',
			)
		));
		$this->assertFalse($this->UserGroup->validates());

		$this->assertArrayHasKey('name', $this->UserGroup->validationErrors);
		$this->assertEquals('既に登録のあるユーザーグループ名です。', current($this->UserGroup->validationErrors['name']));
	}


/**
 * 関連するユーザーを管理者グループに変更し保存する
 */
	public function testBeforeDelete() {

		$this->UserGroup->data['UserGroup']['id'] = 2;
		$this->UserGroup->delete(2);

		// 削除したユーザグループに属していたユーザーを取得
		$data = $this->UserGroup->User->find('all', array(
			'conditions' => array('User.id' => 2),
			'fields' => 'User.user_group_id',
			'recursive' => 'User.user_group_id',
			)
		);
		$result = $data[0]['User']['user_group_id'];
		
		$this->assertEquals(1, $result, '削除したユーザーグループに属するユーザーを、管理者グループに変更できません');
	} 

/**
 * 管理者グループ以外のグループが存在するかチェックする
 */
	public function testCheckOtherAdmins() {
		$result = $this->UserGroup->checkOtherAdmins();
		$this->assertEquals(true, $result, '管理者グループ以外のグループは存在します');
	}

/**
 * 認証プレフィックスを取得する
 *
 * @param int $id ユーザーグループID
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getAuthPrefixDataProvider
 */
	public function testGetAuthPrefix($id, $expected, $message = null) {
		$result = $this->UserGroup->getAuthPrefix($id);
		$this->assertEquals($expected, $result, $message);
	}

	public function getAuthPrefixDataProvider() {
		return array(
			array(1, 'admin', 'プレフィックスが一致しません'),
			array(2, 'operator', 'プレフィックスが一致しません'),
			array(3, false, '存在しないユーザーグループです'),
		);
	}

/**
 * ユーザーグループデータをコピーする
 * 
 * @param int $id ユーザーグループID
 * @param array $data DBに挿入するデータ
 * @param boolean $recursive Permissionもcopyをするかしないか
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider copyDataProvider
 */
	public function testCopy($id, $data, $recursive, $expected, $message = null) {

		$this->UserGroup->copy($id, $data, $recursive);

		if (!$recursive) {

			$copiedId = $this->UserGroup->getInsertID(); // コピーしたデータのID
			$result = $this->UserGroup->find('all', array(
					'conditions' => array(
						'UserGroup.id' => $copiedId
					),
					'recursive' => 0,
					'fields' => array('name', 'title')

				)
			);
		} else {
			$copiedId = $this->UserGroup->Permission->getInsertID(); // コピーしたデータのID
			$result = $this->UserGroup->Permission->find('all', array(
					'conditions' => array(
						'Permission.id' => $copiedId
					),
					'recursive' => 0,
					'fields' => array('Permission.name','Permission.user_group_id')
				)
			);
		};
		
		$this->assertEquals($expected, $result);
	}

	public function copyDataProvider() {
		return array(
			array(1, array(), false,
				array(array('UserGroup' => array(
						'name' => 'admins_copy',
						'title' => 'システム管理_copy',
						'id' => '3'
					)
				)
			), 'id指定でデータをコピーできません'),
			array(null,
				array('UserGroup' => array('name' => 'hogename', 'title' => 'hogetitle')),
				false,
				array(array('UserGroup' => array(
						'name' => 'hogename_copy',
						'title' => 'hogetitle_copy',
						'id' => '3'
					)
				)
			), 'data指定でデータをコピーできません'),
			array(2, array(), true,
				array(array('Permission' => array(
						'name' => 'エディタテンプレート呼出',
						'user_group_id' => '3'
					)
				)
			), 'id指定でPermissionのデータをコピーできません'),
			array(null,
				array('UserGroup' => array('id' => 2, 'name' => 'hogename', 'title' => 'hogetitle')),
				true,
				array(array('Permission' => array(
						'name' => 'エディタテンプレート呼出',
						'user_group_id' => '3'
					)
				)
			), 'data指定でPermissionのデータをコピーできません'),
		);
	}

/**
 * グローバルメニューを利用可否確認
 * 
 * @param string $id
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider isAdminGlobalmenuUsedDataProvider
 */
	public function testIsAdminGlobalmenuUsed($id, $expected, $message = null) {
		$result = $this->UserGroup->isAdminGlobalmenuUsed($id);
		$this->assertEquals($expected, $result, $message);
	}

	public function isAdminGlobalmenuUsedDataProvider() {
		return array(
			array(1, true, 'システム管理者がグローバルメニューを利用できません'),
			array(2, 0, 'システム管理者以外のユーザーがグローバルメニューを利用できます'),
			array(99, false, '存在しないユーザーグループです'),
		);
	}
}
