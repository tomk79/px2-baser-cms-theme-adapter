<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Controller.Component
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */
App::uses('BcReplacePrefixComponent', 'Controller/Component');
App::uses('Controller', 'Controller');


/**
 * 偽コントローラ
 *
 * @package       Cake.Test.Case.Controller.Component
 */
class BcReplacePrefixTestController extends Controller {

	public $components = array('BcReplacePrefix');

	public $plugin = array('Mail', 'admin');

}

/**
 * BcReplacePrefixComponentのテスト
 */
class BcReplacePrefixComponentTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.BlogCategory',
		'baser.Default.BlogContent',
		'baser.Default.BlogComment',
		'baser.Default.BlogTag',
		'baser.Default.SearchIndex',
		'baser.Default.FeedDetail',
		'baser.Default.SiteConfig',
		'baser.Default.UserGroup',
		'baser.Default.Favorite',
		'baser.Default.Page',
		'baser.Default.Permission',
		'baser.Default.Plugin',
		'baser.Default.User',
	);

	public $components = array('BcReplacePrefix');

	public function setUp() {
		parent::setUp();

		// コンポーネントと偽のテストコントローラをセットアップする
		$request = new CakeRequest();
		$response = $this->getMock('CakeResponse');
		$this->Controller = new BcReplacePrefixTestController($request, $response);

		$collection = new ComponentCollection();
		$collection->init($this->Controller);
		$this->BcReplacePrefix = new BcReplacePrefixComponent($collection);
		$this->BcReplacePrefix->request = $request;
		$this->BcReplacePrefix->response = $response;

		$this->Controller->Components->init($this->Controller);

		Router::reload();
		Router::connect('/:controller/:action/*');
	}

	public function tearDown() {
		parent::tearDown();
		unset($this->Controller);
		unset($this->BcReplacePrefix);
	}

/**
 * プレフィックスの置き換えを許可するアクションを設定する
 *
 * $this->Replace->allow('action', 'action',...);
 *
 * @param string $action
 * @param string $action
 * @param string ... etc.
 * @return void
 */
	public function testAllow() {
		$this->BcReplacePrefix->allowedPureActions = array( 'a' => 'hoge1', 'b' => 'hoge2');
		$this->BcReplacePrefix->allow(array('a' => 'hoge3', 'c' => 'hoge4'));
		
		$result = $this->BcReplacePrefix->allowedPureActions;
		$expected = array('a' => 'hoge3', 'b' => 'hoge2', 'c' => 'hoge4');
		$this->assertEquals($expected, $result, 'プレフィックスの置き換えを許可するアクションを設定が正しくありません');
	}

/**
 * startup
 * 
 * @param string $pre actionのprefix
 * @param string $action action名
 * @param string $methods $Controller->methods の値
 * @param boolean $view viewファイルの作成を行うか
 * @param array $expected 期待値
 * @dataProvider startupDataProvider
 */
	public function testStartup($pre, $action, $methods, $view, $expected) {

		// 初期化
		$this->Controller->params['prefix'] = $pre;
		$this->Controller->action = $action;
		$this->Controller->methods = array($methods);

		$this->BcReplacePrefix->allowedPureActions = array('action');

		if ($view) {
			$this->Controller->name = 'Test';
			$FolderPath = ROOT . '/app/webroot/Test' . DS . $pre . DS;
			$filename = 'action.ctp';
			$Folder = new Folder();
			$Folder->create($FolderPath);
			touch($FolderPath . $filename);
		}

		// Initializes
		$this->BcReplacePrefix->initialize($this->Controller);

		// 実行
		$this->BcReplacePrefix->startup($this->Controller);

		if ($view) {
			$Folder->delete(ROOT . '/app/webroot/Test');
		}

		$this->assertEquals($expected[0], $this->Controller->action, 'startupが正しく動作していません');
		$this->assertEquals($expected[1], $this->Controller->layoutPath, 'startupが正しく動作していません');
		$this->assertEquals($expected[2], $this->Controller->subDir, 'startupが正しく動作していません');
	}

	public function startupDataProvider() {
		return array(
			array('pre', 'pre_action', 'admin_action', false, array('admin_action', 'admin', 'admin')),
			array(null, 'pre_action', 'admin_action', false, array('pre_action', null, null)),
			array('pre', null, 'admin_action', false, array(null, null, null)),
			array('pre', 'pre_action', null, false, array('pre_action', null, null)),
			array('pre', 'pre_action', 'admin_action', true, array('admin_action', 'pre', 'pre')),
			array(null, 'action', 'admin_action', true, array('admin_action', null, null)),
		);
	}

/**
 * beforeRender
 */
	public function testBeforeRender() {
		$this->Controller->request->params['prefix'] = 'front';
		$this->BcReplacePrefix->beforeRender($this->Controller);
		$result = $this->Controller->request->params['prefix'];
		$this->assertEmpty($result, 'beforeRenderが正しく動作していません');
	}

/**
 * Return all possible paths to find view files in order
 */
	public function testGetViewPaths() {

		$this->Controller->theme = 'hoge-theme';
		$this->Controller->plugin = 'Mail';

		$result = $this->BcReplacePrefix->getViewPaths($this->Controller);
		$expected = array(
			ROOT . '/app/webroot/theme/hoge-theme/',
			ROOT . '/lib/Baser/Plugin/Mail/View/',
			ROOT . '/app/webroot/',
			ROOT . '/app/View/',
			ROOT . '/lib/Baser/View/',
  	);
  	$this->assertEquals($expected, $result, 'Viewのパスを正しく取得できません');

	}

}
