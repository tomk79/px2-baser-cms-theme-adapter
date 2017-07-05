<?php

/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.View.Helper
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */
App::uses('BcAppView', 'View');
App::uses('BcContentsHelper', 'View/Helper');

/**
 * BcPage helper library.
 *
 * @package Baser.Test.Case
 * @property BcContentsHelper $BcContents
 */
class BcContentsHelperTest extends BaserTestCase {

/**
 * Fixtures
 * @var array 
 */
	public $fixtures = array(
		'baser.View.Helper.BcContentsHelper.ContentBcContentsHelper',
		'baser.Default.SiteConfig',
		'baser.Default.Site',
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.Favorite',
		'baser.Default.Permission',
		'baser.Default.ThemeConfig',
	);

/**
 * View
 * 
 * @var View
 */
	protected $_View;

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->_View = new BcAppView();
		$this->_View->helpers = array('BcContents');
		$this->_View->loadHelpers();
		$this->BcContents = $this->_View->BcContents;
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		Router::reload();
		parent::tearDown();
	}

/**
 * ページリストを取得する
 * 
 * @param int $pageCategoryId カテゴリID
 * @param int $level 関連データの階層	
 * @param int $expectedCount 期待値
 * @param string $expectedTitle  
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getPageListDataProvider
 */
	public function testGetTree($id, $level, $expectedCount, $expectedTitle, $message = null) {
		$result = $this->BcContents->getTree($id, $level);
		$resultTitle = null;
		$resultCount = null;
		switch ($level) {
			case 1:
				if (!empty($result[0]['Content']['title'])) {
					$resultTitle = $result[0]['Content']['title'];
					$resultCount = count($result);
				}
				break;
			case 2:
				if ($result) {
					foreach ($result as $data) {
						if ($data['children']) {
							$resultTitle = $data['children'][0]['Content']['title'];
							$resultCount = count($data['children']);
						}
					}
				}
				break;
			case 3:
				if ($result) {
					foreach ($result as $data) {
						if ($data['children']) {
							foreach ($data['children'] as $data2) {
								if ($data2['children']) {
									$resultTitle = $data2['children'][0]['Content']['title'];
									$resultCount = count($data2['children']);
								}
							}
						}
					}
				}
				break;
		}
		$this->assertEquals($expectedCount, $resultCount, 'カウントエラー：' . $message);
		$this->assertEquals($expectedTitle, $resultTitle, 'タイトルエラー：' . $message);
	}

	public function getPageListDataProvider() {
		return array(
			// PC版
			array(1, 1, 7, 'トップページ', 'PC版１階層目のデータが正常に取得できません'),
			array(1, 2, 4, 'サービス', 'PC版２階層目のデータが正常に取得できません'),
			array(1, 3, 1, 'サブサービス１', 'PC版３階層目のデータが正常に取得できません'),
			// ケータイ
			array(2, 1, 3, 'トップページ', 'ケータイ版１階層目のデータが正常に取得できません'),
			// スマホ
			array(3, 1, 7, 'トップページ', 'スマホ版１階層目のデータが正常に取得できません'),
			array(3, 2, 1, 'サービス１', 'スマホ版２階層目のデータが正常に取得できません')
		);
	}

/**
 * @dataProvider isSiteRelatedDataProvider
 */
	public function testIsSiteRelated($expect, $data) {
		$result = $this->BcContents->isSiteRelated($data);
		$this->assertEquals($expect, $result);
	}

	public function isSiteRelatedDataProvider() {
		return [
			[true, ['Site' => ['relate_main_site' => true], 'Content' => ['main_site_content_id' => 1, 'alias_id' => 1, 'type' => 'BlogContent']]],
			[false, ['Site' => ['relate_main_site' => false], 'Content' => ['main_site_content_id' => 1, 'alias_id' => 1, 'type' => 'BlogContent']]],
			[false, ['Site' => ['relate_main_site' => true], 'Content' => ['main_site_content_id' => null, 'alias_id' => 1, 'type' => 'BlogContent']]],
			[false, ['Site' => ['relate_main_site' => true], 'Content' => ['main_site_content_id' => 1, 'alias_id' => null, 'type' => 'BlogContent']]],
			[true, ['Site' => ['relate_main_site' => true], 'Content' => ['main_site_content_id' => 1, 'alias_id' => null, 'type' => 'ContentFolder']]]
		];
	}

/**
 * アクションが利用可能かどうか確認する
 * isActionAvailable
 *
 * @param string $type コンテンツタイプ
 * @param string $action アクション
 * @param int $entityId コンテンツを特定するID
 * @param bool $expect 期待値
 * @dataProvider isActionAvailableDataProvider
 */
	public function testIsActionAvailable($type, $action, $entityId, $userGroup, $expect) {
		$_SESSION['Auth'][BcUtil::authSessionKey('admin')]['user_group_id'] = $userGroup;
		App::uses('BcContentsComponent', 'Controller/Component');
		$BcContentsComponent = new BcContentsComponent(new ComponentCollection());
		$BcContentsComponent->setupAdmin();
		$View = new BcAppView();
		$View->set('contentsSettings', $BcContentsComponent->settings['items']);
		$View->helpers = array('BcContents');
		$View->loadHelpers();
		$View->BcContents->setup();
		$result = $View->BcContents->isActionAvailable($type, $action, $entityId);
		$this->assertEquals($expect, $result);
	}

	public function isActionAvailableDataProvider() {
		return [
			// 管理ユーザー
			['Default', 'admin_index', 1, 1, false], // 存在しないアクション
			['ContentFolder', 'icon', 1, 1, true], // 存在するアクション
			['ContentFolder', 'add', 1, 1, true], // 存在するアクション
			['ContentFolder', 'edit', 1, 1, true], // 存在するアクション
			['ContentFolder', 'delete', 1, 1, true], // 存在するアクション
			['ContentAlias', 'icon', 1, 1, true], // 存在するアクション
			['BlogContent', 'manage', 1, 1, true], // 存在するアクション
			['MailContent', 'manage', 1, 1, true], // 存在するアクション
			['Page', 'copy', 1, 1, true], // 存在するアクション
			// 運営ユーザー
			['ContentFolder', 'hoge', 2, 2, false], // 存在しないアクション
			['Page', 'add', 2, 2, true], // 存在するアクション（権限あり）
			['Page', 'edit', 2, 2, true], // 存在するアクション（権限あり）
			['Page', 'delete', 1, 2, true], // 存在するアクション（権限あり）
			['ContentFolder', 'edit', 1, 2, false], // 存在するアクション（権限なし）
			['ContentAlias', 'add', 1, 2, false], // 存在するアクション（権限なし）
			['ContentLink', 'add', 1, 2, false], // 存在するアクション（権限なし）
			['BlogContent', 'add', 1, 2, false], // 存在するアクション（権限なし）
			['MailContent', 'edit', 2, 2, false], // 存在するアクション（権限なし）
		];
	}

/**
 * コンテンツIDよりURLを取得する
 * getUrlById
 *
 * 
 */
	public function testGetUrlById() {
		$this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	}

/**
 * フルURLを取得する
 * getUrl
 *
 * 
 */
	public function testGetUrl() {
		$this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	}

/**
 * プレフィックスなしのURLを取得する
 * getPureUrl
 *
 * 
 */
	public function testGetPureUrl() {
		$this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	}

/**
 * 現在のURLを元に指定したサブサイトのURLを取得する
 * getCurrentRelatedSiteUrl
 * フロントエンド専用メソッド
 * @param string $siteName 
 * @param mixed|string $expect 期待値
 * @dataProvider getCurrentRelatedSiteUrlDataProvider
 */
	public function testGetCurrentRelatedSiteUrl($siteName, $expect) {
		$this->BcContents->request = $this->_getRequest('/');  
		$_SERVER['HTTP_USER_AGENT'] = 'iPhone';
		$result = $this->BcContents->getCurrentRelatedSiteUrl($siteName);
		$this->assertEquals($expect, $result);
	}

	public function getCurrentRelatedSiteUrlDataProvider() {
		return [
			// 戻り値が空でないもの（）
			['smartphone', '/s/'],
			['mobile', '/m/'],
			// $siteNameの値が空の場合、返り値も空
			['', ''],
			['hoge', ''],
		];
	}

/**
 * 関連サイトのコンテンツを取得
 * getRelatedSiteContents
 * フロントエンド専用メソッド
 * @param int $id コンテンツID = Null
 * @param array $options
 * @param array | false $expect 期待値
 * @dataProvider getRelatedSiteContentsDataProvider
*/
	public function testGetRelatedSiteContents($id, $options, $expect) {
		$this->BcContents->request = $this->_getRequest('/');
		$_SERVER['HTTP_USER_AGENT'] = 'iPhone';
		$result = $this->BcContents->getRelatedSiteContents($id, $options);
		$this->assertEquals($expect, $result[1]['Content']['id']);                       
	}
	public function getRelatedSiteContentsDataProvider() {
		return [
			// コンテンツIDが空 オプションも空
			[null, [], 9],
			// コンテンツIDが空  オプション excludeIds 0~1
			['', ['excludeIds' => [0]], 10],
			['', ['excludeIds' => [1]], 10],
			// コンテンツIDが空  オプション excludeIds 2~
			['', ['excludeIds' => [2]], 9],
			['', ['excludeIds' => [99]], 9],
			// コンテンツIDに値が入っていれば、false
			['1', ['excludeIds' => [1]], false],
			['hoge', [], false],
		];
	}

/**
 * 関連サイトのリンク情報を取得する
 * フロントエンド専用メソッド
 * getRelatedSiteLinks
 * @param int $id
 * @param array $options
 * @param array $expect 期待値
 * @dataProvider getRelatedSiteLinksDataProvider
*/
	public function testGetRelatedSiteLinks($id, $options, $expect) {
		$this->BcContents->request = $this->_getRequest('/');
		$_SERVER['HTTP_USER_AGENT'] = 'iPhone';
		$result = $this->BcContents->getRelatedSiteLinks($id, $options);
		$this->assertEquals($expect, $result);      
	}	
	public function getRelatedSiteLinksDataProvider() {
		return [
			// IDが空 オプションも空
			[null, [], [['prefix' => '','name' => 'パソコン', 'url'=>'/index'],['prefix' => 'mobile','name' => 'ケータイ', 'url'=>'/m/index'],['prefix' => 'smartphone','name' => 'スマートフォン', 'url'=>'/s/index']]],
			// IDが空  オプション excludeIds 0~2
			['', ['excludeIds' => [0]], [ 0 => ['prefix' => 'mobile', 'name' => 'ケータイ', 'url' => '/m/index'],1 =>['prefix' => 'smartphone', 'name' => 'スマートフォン', 'url' => '/s/index'] ] ],
			[false, ['excludeIds' => [1]], [['prefix' => '','name' => 'パソコン', 'url'=>'/index'],['prefix' => 'smartphone','name' => 'スマートフォン', 'url'=>'/s/index']]],
			[0, ['excludeIds' => [2]], [['prefix' => '','name' => 'パソコン', 'url'=>'/index'],['prefix' => 'mobile','name' => 'ケータイ', 'url'=>'/m/index']]],
			// IDが空  オプション excludeIds 3~
			[0, ['excludeIds' => [3]], [['prefix' => '','name' => 'パソコン', 'url'=>'/index'],['prefix' => 'mobile','name' => 'ケータイ', 'url'=>'/m/index'],['prefix' => 'smartphone','name' => 'スマートフォン', 'url'=>'/s/index']]],
			[0, ['excludeIds' => [99]], [['prefix' => '','name' => 'パソコン', 'url'=>'/index'],['prefix' => 'mobile','name' => 'ケータイ', 'url'=>'/m/index'],['prefix' => 'smartphone','name' => 'スマートフォン', 'url'=>'/s/index']]],
			// IDに値が入っていれば、false
			[1, ['excludeIds' => [0]], []],
			['hoge', [], []],
		];
	}	

/**
 * コンテンツ設定を Json 形式で取得する
 * getJsonSettings
*/
	public function testGetJsonSettings() {
		App::uses('BcContentsComponent', 'Controller/Component');
		$BcContentsComponent = new BcContentsComponent(new ComponentCollection());
		$BcContentsComponent->setupAdmin();
		$View = new BcAppView();
		$View->set('contentsSettings', $BcContentsComponent->settings['items']);
		$View->helpers = array('BcContents');
		$View->loadHelpers();
		$View->BcContents->setup();
		$result = $View->BcContents->getJsonSettings();
		// JSON形式が正しいかどうか		
		$this->assertTrue(is_string($result) && is_array(json_decode($result, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false);
	}
/**
 * @param string $expect 期待値
 * @param string $no
 * @dataProvider getJsonSettingsDataProvider
*/
	public function testGetJsonSettingsEquals($expect,$no) {
		App::uses('BcContentsComponent', 'Controller/Component');
		$BcContentsComponent = new BcContentsComponent(new ComponentCollection());
		$BcContentsComponent->setupAdmin();
		$View = new BcAppView();
		$View->set('contentsSettings', $BcContentsComponent->settings['items']);
		$View->helpers = array('BcContents');
		$View->loadHelpers();
		$View->BcContents->setup();
		// 　getJsonSettingsで取得した値がsettingsの値と等しいかどうか
		$result = json_decode($View->BcContents->getJsonSettings(),true);
		$result = $result[$no]['title'];
		$this->assertEquals($expect, $result);      
	}
	public function getJsonSettingsDataProvider() {
		return [
			['無所属コンテンツ','Default'],
			['フォルダー','ContentFolder'],
			['ブログ','BlogContent'],
		];
	}

/**
 * データが公開状態にあるか確認する
 *
 */
	public function testIsAllowPublish() {
		$this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	}

/**
 * 親コンテンツを取得する
 * 
 */
	public function testGetParent() {
		$this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	}	
/**
 * フォルダリストを取得する
 * 
 */	
	public function testGetContentFolderList() {
		$this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	}
	
/**
 * サイトIDからサイトルートとなるコンテンツを取得する
 * 
 */	
	public function testGetSiteRoot() {
		$this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	}
	
/**
 * サイトIDからコンテンツIDを取得する
 * getSiteRootId
 * 
 * @param int $siteId
 * @param string|bool $expect 期待値
 * @dataProvider getSiteRootIdDataProvider
 */	
	public function testGetSiteRootId($siteId, $expect) {
		$result = $this->BcContents->getSiteRootId($siteId);
		$this->assertEquals($expect, $result);                       
	}
	public function getSiteRootIdDataProvider() {
		return [
			// 存在するサイトID（0~2）を指定した場合
			[0, 1],
			[1, 2],
			[2, 3],
			// 存在しないサイトIDを指定した場合
			[3, false],
			[99, false],
		];
	}
}
