<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.View.Helper
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('View', 'View');
App::uses('BcCkeditorHelper', 'View/Helper');

/**
 * text helper library.
 *
 * @package Baser.Test.Case.View.Helper
 * @property BcCkeditor $BcCkeditor
 */
class BcCkeditorHelperTest extends BaserTestCase {

/**
 * Fixtures
 * @var array
 */
	public $fixtures = array(
		'baser.Default.SiteConfig',
		'baser.Default.Page',
		'baser.Default.Site',
		'baser.Default.Content',
		'baser.Default.User'
	);

	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->BcCkeditor = new BcCkeditorHelper($View);
		$this->BcCkeditor->request = $this->_getRequest('/');
	}

	public function tearDown() {
		unset($this->BcCkeditor);
		parent::tearDown();
	}

/**
 * CKEditorのテキストエリアを出力する
 *
 * @param string $fieldName エディタのid, nameなどの名前を指定
 * @param array $options
 * @param boolean $expected 期待値
 * @dataProvider editorDataProvider
 */
	public function testEditor($fieldName, $options, $expected) {

		$expected = '/' . $expected . '/';
		$result = $this->BcCkeditor->editor($fieldName, $options);

		$this->assertRegExp($expected, $result);
	}

	public function editorDataProvider() {
		return array(
			array('test', array(), 'test'),
			array('test', array('editorLanguage' => 'en'), '"language":"en"'),
			array('test', array('editorSkin' => 'office2013'), '"skin":"office2013"'),
			array('test', array('editorToolbar' => array('test' => '[Anchor]')), '"test":"\[Anchor\]"'),
		);
	}

}