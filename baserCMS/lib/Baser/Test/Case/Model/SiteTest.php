<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Model
 * @since			baserCMS v 4.0.2
 * @license			http://basercms.net/license/index.html
 */
App::uses('Site', 'Model');

/**
 * SiteTest class
 *
 * @property Site $Site
 * @package Baser.Test.Case.Model
 */
class SiteTest extends BaserTestCase {

/**
 * Fixtures
 * 
 * @var array
 */
	public $fixtures = array(
		'baser.Default.Site',
		'baser.Default.ContentFolder',
		'baser.Default.Content',
		'baser.Default.User',
		'baser.Default.SiteConfig'
	);

/**
 * Set Up 
 */
	public function setUp() {
		parent::setUp();
		$this->Site = ClassRegistry::init('Site');
	}

/**
 * Tear Down
 */
	public function tearDown() {
		unset($this->Site);
		parent::tearDown();
	}

/**
 * testResetDevice
 */
	public function testResetDevice() {
		$this->Site->resetDevice();
		$sites = $this->Site->find('all', ['recursive' => -1]);
		foreach($sites as $site) {
			$this->assertEquals($site['Site']['device'], '');
			$this->assertFalse($site['Site']['same_main_url']);
			$this->assertFalse($site['Site']['auto_redirect']);
			$this->assertFalse($site['Site']['auto_link']);
		}
	}

/**
 * testResetDevice
 */
	public function testResetLang() {
		$this->Site->resetLang();
		$sites = $this->Site->find('all', ['recursive' => -1]);
		foreach($sites as $site) {
			$this->assertEquals($site['Site']['lang'], '');
			$this->assertFalse($site['Site']['same_main_url']);
			$this->assertTrue($site['Site']['auto_redirect']);
		}
	}

/**
 * サイトリストを取得 
 * 
 * @param int $mainSiteId メインサイトID
 * @param array $options
 * @param array $expects
 * @param string $message
 * @dataProvider getSiteListDataProvider
 */
	public function testGetSiteList($mainSiteId, $options, $expects, $message) {
		$result = $this->Site->getSiteList($mainSiteId, $options);
		$this->assertEquals($expects, $result, $message);
	}
	
	public function getSiteListDataProvider() {
		return [
			[null, [], [0 => 'パソコン', 1 => 'ケータイ', 2 => 'スマートフォン'], '全てのサイトリストの取得ができません。'],
			[0, [], [1 => 'ケータイ', 2 => 'スマートフォン'], 'メインサイトの指定ができません。'],
			[1, [], [], 'メインサイトの指定ができません。'],
			[null, ['excludeIds' => [0,2]], [1 => 'ケータイ'], '除外指定ができません。'],
			[null, ['excludeIds' => 1], [0 => 'パソコン', 2 => 'スマートフォン'], '除外指定ができません。'],
			[null, ['excludeIds' => 0], [1 => 'ケータイ', 2 => 'スマートフォン'], '除外指定ができません。'],
			[null, ['includeIds' => [0, 2]], [0 => 'パソコン', 2 => 'スマートフォン'], 'ID指定ができません。'],
			[null, ['includeIds' => 1], [1 => 'ケータイ'], 'ID指定ができません。'],
		];
	}
	
}
