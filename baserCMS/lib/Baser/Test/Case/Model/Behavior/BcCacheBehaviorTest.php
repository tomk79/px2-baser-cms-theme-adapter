<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Model.Behavior
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */

/**
 * BcCacheBehaviorTest class
 * 
 * @package Baser.Test.Case.Model
 */
class BcCacheBehaviorTest extends BaserTestCase {

	public $fixtures = array();

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Page = ClassRegistry::init('Page');
		$this->BcCacheBehavior = ClassRegistry::init('BcCacheBehavior');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Page);
		unset($this->BcCacheBehavior);
		parent::tearDown();
	}


/**
 * キャッシュフォルダが空ならtrue
 *
 * @var boolean
 */
	public $emptyCache = true;

/**
 * 指定したフォルダが空が調べる
 *
 * testDelCache(), testDelAssockCache()で使用
 */
	public function isEmptyDir($dir_path) {
    return (count(glob($dir_path . "/*")) == 0);
	}

/**
 * 関連モデルのキャッシュフォルダが空か調べる
 */
	public function isEmptyDirAssock(Model $model, $recursive = 0) {
		if ($recursive <= 3) {
			$recursive++;
			$assocTypes = array('hasMany', 'hasOne', 'belongsTo', 'hasAndBelongsToMany');
			foreach ($assocTypes as $assocType) {
				if ($model->{$assocType}) {
					foreach ($model->{$assocType} as $assoc) {
						$className = $assoc['className'];
						list($plugin, $className) = pluginSplit($className);
						if (isset($model->{$className})) {
							// フォルダが空かチェックする
							$path = CACHE_DATA_PATH . $model->tablePrefix . $model->table;
							if (!$this->isEmptyDir($path)) {
								$this->emptyCache = false;
							}

							$this->isEmptyDirAssock($model->{$className}, $recursive);
						}
					}
				}
			}
		}
	}

/**
 * 関連モデルのキャッシュフォルダにダミーファイルを生成
 */
	public function createDummyFileAssoc(Model $model, $recursive = 0) {
		if ($recursive <= 3) {
			$recursive++;
			$assocTypes = array('hasMany', 'hasOne', 'belongsTo', 'hasAndBelongsToMany');
			foreach ($assocTypes as $assocType) {
				if ($model->{$assocType}) {
					foreach ($model->{$assocType} as $assoc) {
						$className = $assoc['className'];
						list($plugin, $className) = pluginSplit($className);
						if (isset($model->{$className})) {
							// ダミーファイル作成
							$path = CACHE_DATA_PATH . $model->tablePrefix . $model->table;
							touch($path . DS . 'hoge');

							$this->createDummyFileAssoc($model->{$className}, $recursive);
						}
					}
				}
			}
		}
	}


/**
 * キャッシュフォルダーを生成する
 */
	public function testCreateCacheFolder() {
		$this->Page->createCacheFolder();
		$path = CACHE_DATA_PATH . $this->Page->tablePrefix . $this->Page->table;
		
		// フォルダが生成されているかチェック
		$this->assertFileExists($path);
	}

/**
 * キャッシュ処理
 * 
 * @param Model $model
 * @param int $expire
 * @param string $method
 * @args mixed
 * @return mixed
 */
	public function testReadCache() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * データキャッシュのパスを指定する
 * 
 * @param string $dir 
 */
	public function testChangeCachePath() {
		$table = 'hoge';
		$this->BcCacheBehavior->changeCachePath($table);

		// 設定した情報を取得
		$cacheConfig = Cache::config('_cake_data_');
		$result = $cacheConfig['settings']['path'];

		// 設定したパスが正しいかチェック
		$expected = CACHE_DATA_PATH . $table . DS;
		$this->assertEquals($expected, $result, 'データキャッシュのパスを正しく指定できません');

	}

/**
 * キャッシュを削除する
 * 
 * @param Model $model
 * @return void
 */
	public function testDelCache() {
		$path = CACHE_DATA_PATH . $this->Page->tablePrefix . $this->Page->table;

		// ダミーファイル作成
		touch($path . DS . 'hoge');

		$this->Page->delCache();
				
		// キャッシュフォルダが空かチェック
		$result = $this->isEmptyDir($path);
		$this->assertTrue($result);
	}


/**
 * 関連モデルを含めてキャッシュを削除する
 * 
 * BcCacheBehaviorのBeforeSave, afterDeleteのテストも兼ねます
 */
	public function testDelAssockCache() {
		$this->createDummyFileAssoc($this->Page);
		$this->Page->delAssockCache();
		$this->isEmptyDirAssock($this->Page);

		$this->assertTrue($this->emptyCache, '関連モデルを含めてキャッシュを削除できません');
	}


}
