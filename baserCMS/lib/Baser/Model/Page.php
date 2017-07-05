<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * ページモデル
 * 
 * @package Baser.Model
 * @property Content $Content
 */
class Page extends AppModel {

/**
 * ビヘイビア
 *
 * @var array
 */
	public $actsAs = array('BcSearchIndexManager', 'BcCache', 'BcContents');

/**
 * 更新前のページファイルのパス
 * 
 * @var string
 */
	public $oldPath = '';

/**
 * ファイル保存可否
 * true の場合、ページデータ保存の際、ページテンプレートファイルにも内容を保存する
 * テンプレート読み込み時などはfalseにして保存しないようにする
 * 
 * @var boolean
 */
	public $fileSave = true;

/**
 * 検索テーブルへの保存可否
 *
 * @var boolean
 */
	public $searchIndexSaving = true;

/**
 * 公開WebページURLリスト
 * キャッシュ用
 * 
 * @var mixed
 */
	protected $_publishes = -1;

/**
 * WebページURLリスト
 * キャッシュ用
 * 
 * @var mixed
 */
	protected $_pages = -1;

/**
 * 最終登録ID
 * モバイルページへのコピー処理でスーパークラスの最終登録IDが上書きされ、
 * コントローラーからは正常なIDが取得できないのでモバイルページへのコピー以外の場合だけ保存する
 *
 * @var int
 */
	private $__pageInsertID = null;

/**
 * バリデーション
 *
 * @var array
 */
	public $validate = array(
		'contents' => array(
			array('rule' => array('phpValidSyntax'),
				'message' => 'PHPの構文エラーが発生しました。'),
			array('rule' => array('maxByte', 64000),
				'message' => '本稿欄に保存できるデータ量を超えています。')
		),
		'draft' => array(
			array('rule' => array('phpValidSyntax'),
				'message' => 'PHPの構文エラーが発生しました。'),
			array('rule' => array('maxByte', 64000),
				'message' => '草稿欄に保存できるデータ量を超えています。')
		),
	);

/**
 * beforeSave
 *
 * @param array $options
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if (!$this->fileSave) {
			return true;
		}

		// 保存前のページファイルのパスを取得
		if ($this->exists() && !empty($this->data['Content'])) {
			$this->oldPath = $this->getPageFilePath(
				$this->find('first', array(
					'conditions' => array('Page.id' => $this->data['Page']['id']),
					'recursive' => 0)
				)
			);
		} else {
			$this->oldPath = '';
		}
		
		// 新しいページファイルのパスが開けるかチェックする
		$result = true;
		if(!empty($this->data['Content'])) {
			if (!$this->checkOpenPageFile($this->data)) {
				$result = false;
			}
		}
		return $result;
	}

/**
 * 最終登録IDを取得する
 *
 * @return	int
 */
	public function getInsertID() {
		if (!$this->__pageInsertID) {
			$this->__pageInsertID = parent::getInsertID();
		}
		return $this->__pageInsertID;
	}

/**
 * ページテンプレートファイルが開けるかチェックする
 * 
 * @param	array	$data	ページデータ
 * @return	boolean
 */
	public function checkOpenPageFile($data) {
		$path = $this->getPageFilePath($data);
		$File = new File($path);
		if ($File->open('w')) {
			$File->close();
			$File = null;
			return true;
		} else {
			return false;
		}
	}

/**
 * afterSave
 * 
 * @param boolean $created
 * @param array $options
 * @return boolean
 */
	public function afterSave($created, $options = array()) {

		$data = $this->data;
		// タイトルタグと説明文を追加
		if (empty($data['Page']['id'])) {
			$data['Page']['id'] = $this->id;
		}

		if ($this->fileSave) {
			$this->createPageTemplate($data);
		}

		// 検索用テーブルに登録
		if ($this->searchIndexSaving) {
			if (empty($data['Content']['exclude_search'])) {
				$this->saveSearchIndex($this->createSearchIndex($data));
			} else {
				$this->deleteSearchIndex($data['Page']['id']);
			}
		}

	}

/**
 * 検索用データを生成する
 *
 * @param array $data
 * @return array
 */
	public function createSearchIndex($data) {
		if (!isset($data['Page']) || !isset($data['Content'])) {
			return false;
		}
		$page = $data['Page'];
		$content = $data['Content'];
		if (!isset($content['publish_begin'])) {
			$content['publish_begin'] = '';
		}
		if (!isset($content['publish_end'])) {
			$content['publish_end'] = '';
		}

		if (!$content['title']) {
			$content['title'] = Inflector::camelize($content['name']);
		}
		
		// $this->idに値が入ってない場合もあるので
		if (!empty($page['id'])) {
			$modelId = $page['id'];
		} else {
			$modelId = $this->id;
		}
		
		$host = '';
		$url = $content['url'];
		$site = BcSite::findById($content['site_id']);
		if($site->useSubDomain) {
			$host = $site->alias;
			if($site->domainType == 1) {
				$host .= '.'  . BcUtil::getMainDomain();
			}
			$url = preg_replace('/^\/' . preg_quote($site->alias, '/') . '/', '', $url);
		}
		$parameters = explode('/', preg_replace("/^\//", '', $url));
		$detail = $this->requestAction(['admin' => false, 'plugin' => false, 'controller' => 'pages', 'action' => 'display'], ['?' => [
			'force' => 'true',
			'host' => $host
		], 'pass' => $parameters, 'return']);

		$detail = preg_replace('/<!-- BaserPageTagBegin -->.*?<!-- BaserPageTagEnd -->/is', '', $detail);
		$description = '';
		if(!empty($content['description'])) {
			$description = $content['description'];
		}
		$searchIndex = ['SearchIndex' => [
			'model_id'	=> $modelId,
			'type'		=> 'ページ',
			'content_id'=> $content['id'],
			'site_id'=> $content['site_id'],
			'title'		=> $content['title'],
			'detail'	=> $description . ' ' . $detail,
			'url'		=> $url,
			'status'	=> $this->isPublish($content['status'], $content['publish_begin'], $content['publish_end'])
		]];
		return $searchIndex;
	}

/**
 * beforeDelete
 *
 * @param $cascade
 * @return boolean
 */
	public function beforeDelete($cascade = true) {
		return $this->deleteSearchIndex($this->id);
	}

/**
 * DBデータを元にページテンプレートを全て生成する
 * 
 * @return boolean
 */
	public function createAllPageTemplate() {
		set_time_limit(0);
		if (function_exists('ini_set')) {
			ini_set('memory_limit ', '-1');
		}
		$pages = $this->find('all', array('recursive' => 0));
		$result = true;
		foreach ($pages as $page) {
			if (!$this->createPageTemplate($page)) {
				$result = false;
			}
		}
		return $result;
	}

/**
 * ページテンプレートを生成する
 * 
 * @param array $data ページデータ
 * @return boolean
 */
	public function createPageTemplate($data) {
		set_time_limit(0);
		if (function_exists('ini_set')) {
			ini_set('memory_limit ', '-1');
		}
		if (!isset($data['Page']) || !isset($data['Content'])) {
			return false;
		}
		$data['Page'] = array_merge(['id' => '', 'contents' => '', 'title' => '', 'description' => '', 'code' => ''], $data['Page']);
		$page = $data['Page'];
		$content = $data['Content'];
		$contents = $this->addBaserPageTag($page['id'], $page['contents'], $content['title'], @$content['description'], @$page['code']);

		// 新しいページファイルのパスを取得する
		$newPath = $this->getPageFilePath($data);

		// テーマやファイル名が変更された場合は元ファイルを削除する
		if ($this->oldPath && ($newPath != $this->oldPath)) {
			$oldFile = new File($this->oldPath);
			$oldFile->delete();
			unset($oldFile);
		}

		// ファイルに保存
		$newFile = new File($newPath, true);
		if ($newFile->open('w')) {
			$newFile->append($contents);
			$newFile->close();
			unset($newFile);
			@chmod($newPath, 0666);
			return true;
		} else {
			return false;
		}
	}

/**
 * ページファイルのパスを取得する
 * 
 * @param array $data
 * @return string
 */
	public function getPageFilePath($data) {
		
		$path = APP . 'View' . DS . 'Pages' . DS;
		
		if (!is_dir($path)) {
			mkdir($path);
			chmod($path, 0777);
		}
		
		$url = $this->Content->createUrl($data['Content']['parent_id'], 'Core', 'ContentFolder');
		if($url != '/') {
			if($data['Content']['site_id'] != 0) {
				$site = BcSite::findByUrl($url);
				if($site) {
					$url = preg_replace('/^\/' . preg_quote($site->alias, '/') . '\//', '/' . $site->name . '/', $url);
				}
			}
			$urlAry = explode('/', preg_replace('/(^\/|\/$)/', '', $url));
			foreach ($urlAry as $value) {
				$path .= $value . DS;
				if (!is_dir($path)) {
					mkdir($path, 0777);
					chmod($path, 0777);
				}
			}
		}
		return $path . $data['Content']['name'] . Configure::read('BcApp.templateExt');
	}

/**
 * ページファイルを削除する
 * 
 * @param array $data 削除対象となるレコードデータ
 * @return boolean
 */
	public function delFile($data) {
		$path = $this->getPageFilePath($data);
		if ($path) {
			return @unlink($path);
		}
		return true;
	}

/**
 * 本文にbaserが管理するタグを追加する
 * 
 * @param string $id ID
 * @param string $contents 本文
 * @param string $title タイトル
 * @param string $description 説明文
 * @param string $code コード
 * @return string 本文の先頭にbaserCMSが管理するタグを付加したデータ
 */
	public function addBaserPageTag($id, $contents, $title, $description, $code) {
		$tag = array();
		$tag[] = '<!-- BaserPageTagBegin -->';
		$title = str_replace("'", "\'", str_replace("\\", "\\\\'", $title));
		$description = str_replace("'", "\'", str_replace("\\", "\\\\'", $description));
		$tag[] = '<?php $this->BcBaser->setTitle(\'' . $title . '\') ?>';
		$tag[] = '<?php $this->BcBaser->setDescription(\'' . $description . '\') ?>';

		if ($id) {
			$tag[] = '<?php $this->BcBaser->setPageEditLink(' . $id . ') ?>';
		}
		if ($code) {
			$tag[] = trim($code);
		}
		$tag[] = '<!-- BaserPageTagEnd -->';
		return implode("\n", $tag) . "\n\n" . $contents;
	}

/**
 * コントロールソースを取得する
 *
 * @param string $field フィールド名
 * @param array $options
 * @return mixed $controlSource コントロールソース
 */
	public function getControlSource($field, $options = array()) {
		switch ($field) {
			case 'user_id':
			case 'author_id':
				$controlSources[$field] = $this->Content->User->getUserList($options);
				break;
		}

		if (isset($controlSources[$field])) {
			return $controlSources[$field];
		} else {
			return false;
		}
	}

/**
 * ページファイルを登録する
 * ※ 再帰処理
 *
 * @param string $targetPath
 * @param string $parentCategoryId
 * @return array 処理結果 all / success
 */
	public function entryPageFiles($targetPath, $parentId = '') {
		if ($this->Behaviors->loaded('BcCache')) {
			$this->Behaviors->unload('BcCache');
		}
		if ($this->Content->Behaviors->loaded('BcCache')) {
			$this->Content->Behaviors->unload('BcCache');
		}

		$this->fileSave = false;
		$Folder = new Folder($targetPath);
		$files = $Folder->read(true, true, true);
		$Folder = null;
		$insert = 0;
		$update = 0;
		$all = 0;

		// フォルダの取得・登録
		$folderName = basename($targetPath);

		if ($folderName == 'templates') {
			return array('all' => 0, 'insert' => 0, 'update' => 0);
		}
		$basePath = APP . 'View' . DS . 'Pages';
		$url = str_replace($basePath, '', $targetPath);
		$url = str_replace(DS, '/', $url);
		$Site = ClassRegistry::init('Site');
		$url = preg_replace('/^\//', '', $url);
		$urlAry = explode('/', $url);
		$siteId = 0;
		if(!empty($urlAry[0])) {
			$site = $Site->find('first', ['conditions' => ['Site.name' => $urlAry[0]]]);
			if($site) {
				if($site['Site']['alias']) {
					$urlAry[0] = $site['Site']['alias'];
				}
				$siteId = $site['Site']['id'];
			} else {
				$siteId = 0;
			}
		}
		
		if($urlAry[0]) {
			$url = '/' . implode('/', $urlAry) . '/';	
		} else {
			$url = '/';
		}
		
		$contentId = '';
		
		if ($folderName != 'Pages') {
			// カテゴリ名の取得
			// 標準では設定されてないので、利用する場合は、あらかじめ bootstrap 等で宣言しておく
			$folderTitles = Configure::read('Baser.pageCategoryTitles');
			$folderTitle = -1;
			if ($folderTitles) {
				$folderNames = explode('/', $url);
				foreach ($folderNames as $key => $value) {
					if (isset($folderTitles[$value])) {
						if (count($folderNames) == ($key + 1)) {
							$folderTitle = $folderTitles[$value]['title'];
						} elseif (isset($folderTitles[$value]['children'])) {
							$folderTitles = $folderTitles[$value]['children'];
						}
					}
				}
			}

			$ContentFolder = ClassRegistry::init('ContentFolder');
			$entityId = $this->Content->field('entity_id', ['Content.url' => $url, 'Content.type' => 'ContentFolder']);
			if ($entityId) {
				$content = $ContentFolder->find('first', array('conditions' => array('ContentFolder.id' => $entityId), 'recursive' => 0));
				$contentId = $content['Content']['id'];
				if ($folderTitle != -1) {
					$content['Content']['title'] = $folderTitle;
					$ContentFolder->set($content);
					$ContentFolder->save();
				}
			} else {
				$content = ['Content' => [
					'parent_id' => $parentId,
					'name' => $folderName,
					'site_id' => $siteId
				]];
				if ($folderTitle == -1) {
					$content['Content']['title'] = $folderName;
				} else {
					$content['Content']['title'] = $folderTitle;
				}
				$ContentFolder->cacheQueries = false;
				$ContentFolder->create($content);
				if ($ContentFolder->save()) {
					$contentId = $ContentFolder->Content->id;
				}
			}
		} else {
			$contentId = 1;
		}

		// ファイル読み込み・ページ登録
		if (!$files[1]) {
			$files[1] = array();
		}
		foreach ($files[1] as $path) {
			if (preg_match('/' . preg_quote(Configure::read('BcApp.templateExt')) . '$/is', $path) == false) {
				continue;
			}

			$pageName = basename($path, Configure::read('BcApp.templateExt'));
			$file = new File($path);
			$contents = $file->read();
			$file->close();
			$file = null;

			// タイトル取得・置換
			$titleReg = '/<\?php\s+?\$this->BcBaser->setTitle\(\'(.*?)\'\)\s+?\?>/is';
			if (preg_match($titleReg, $contents, $matches)) {
				$title = trim($matches[1]);
				$contents = preg_replace($titleReg, '', $contents);
			} else {
				$title = Inflector::camelize($pageName);
			}

			// 説明文取得・置換
			$descriptionReg = '/<\?php\s+?\$this->BcBaser->setDescription\(\'(.*?)\'\)\s+?\?>/is';
			if (preg_match($descriptionReg, $contents, $matches)) {
				$description = trim($matches[1]);
				$contents = preg_replace($descriptionReg, '', $contents);
			} else {
				$description = '';
			}

			// PageTagコメントの削除
			$pageTagReg = '/<\!\-\- BaserPageTagBegin \-\->.*?<\!\-\- BaserPageTagEnd \-\->/is';
			$contents = preg_replace($pageTagReg, '', $contents);
			$conditions = [];
			$conditions['Content.name'] = $pageName;
			if ($contentId) {
				$conditions['Content.parent_id'] = $contentId;
			} else {
				$conditions['Content.parent_id'] = 1;
			}
			$page = $this->find('first', array('conditions' => $conditions, 'recursive' => 0));
			if ($page) {
				$chage = false;
				if ($title != $page['Content']['title']) {
					$chage = true;
				}
				if ($description != $page['Content']['description']) {
					$chage = true;
				}
				if (trim($contents) != trim($page['Page']['contents'])) {
					$chage = true;
				}
				if ($chage) {
					$page['Content']['title'] = $title;
					$page['Content']['description'] = $description;
					$page['Page']['contents'] = $contents;
					$this->set($page);
					if ($this->save()) {
						$update++;
					}
				}
			} else {
				$page = [
					'Page' => [
						'contents' => $contents,
					],
					'Content' => [
						'name' => $pageName,
						'title' => $title,
						'description' => $description
					]
				];
				$page['Content']['site_id'] = $siteId;
				if ($contentId) {
					$page['Content']['parent_id'] = $contentId;
				} else {
					$page['Content']['parent_id'] = 1;
				}
				$this->create($page);
				if ($this->save()) {
					$insert++;
				}
			}
			$all++;
		}

		// フォルダー内の登録
		if (!$files[0]) {
			$files[0] = array();
		}
		foreach ($files[0] as $file) {
			$folderName = basename($file);
			if ($folderName != '_notes' && $folderName != 'admin') {
				$result = $this->entryPageFiles($file, $contentId);
				$insert += $result['insert'];
				$update += $result['update'];
				$all += $result['all'];
			}
		}
		return array('all' => $all, 'insert' => $insert, 'update' => $update);
	}

/**
 * 固定ページとして管理されているURLかチェックする
 * 
 * $this->_pages をキャッシュとして利用する
 * URL に、拡張子 .html がついている場合も存在するとみなす
 * 
 * @param string $url URL
 * @return boolean
 */
	public function isPageUrl($url) {
		if (preg_match('/\/$/', $url)) {
			$url .= 'index';
		}

		if ($this->_pages == -1) {
			$pages = $this->find('all', array(
				'fields'	=> 'Content.url',
				'recursive' => 1
			));
			if (!$pages) {
				$this->_pages = array();
				return false;
			}
			$this->_pages = Hash::extract($pages, '{n}.Content.url');
		}
		if(in_array($url, $this->_pages)) {
			return true;
		}
		if(preg_match('/\.html$/', $url)) {
			$url = preg_replace('/\.html$/', '', $url);
			if(in_array($url, $this->_pages)) {
				return true;
			}
		}
		return false;
	}

/**
 * delete
 *
 * @param mixed $id ページID
 * @param boolean $cascade Set to true to delete records that depend on this record
 * @return boolean True on success
 */
	public function delete($id = null, $cascade = true) {
		// メッセージ用にデータを取得
		$page = $this->read(null, $id);

		/* 削除処理 */
		if (parent::delete($id, $cascade)) {

			// ページテンプレートを削除
			$this->delFile($page);

			// 公開状態だった場合、サイトマップのキャッシュを削除
			// 公開期間のチェックは行わず確実に削除
			if ($page['Content']['status']) {
				clearViewCache();
			}
			return true;
		} else {

			return false;
		}
	}

/**
 * ページデータをコピーする
 *
 * 固定ページテンプレートの生成処理を実行する必要がある為、
 * Content::copy() は利用しない
 * 
 * @param int $id ページID
 * @param int $newParentId 新しい親コンテンツID
 * @param string $newTitle 新しいタイトル
 * @param int $newAuthorId 新しいユーザーID
 * @param int $newSiteId 新しいサイトID
 * @return mixed page Or false
 */
	public function copy($id, $newParentId, $newTitle, $newAuthorId, $newSiteId = null) {
		$data = $this->find('first', ['conditions' => ['Page.id' => $id], 'recursive' => 0]);
		$url = $data['Content']['url'];
		$siteId = $data['Content']['site_id'];
		$name = $data['Content']['name'];
		unset($data['Page']['id']);
		unset($data['Page']['created']);
		unset($data['Page']['modified']);
		unset($data['Content']);
		$data['Content'] = [
			'name'		=> $name,
			'parent_id'	=> $newParentId,
			'title'		=> $newTitle,
			'author_id' => $newAuthorId,
			'site_id' 	=> $newSiteId,
			'description' => ''
		];
		if(!is_null($newSiteId) && $siteId != $newSiteId) {
			$data['Content']['site_id'] = $newSiteId;
			$data['Content']['parent_id'] = $this->Content->copyContentFolderPath($url, $newSiteId);
		}
		$this->getDataSource()->begin();
		$this->create($data);
		if ($data = $this->save()) {
			$this->getDataSource()->commit();
			return $data;
		}
		$this->getDataSource()->rollback();
		return false;
	}
	
/**
 * PHP構文チェック
 *
 * @param array $check チェック対象文字列
 * @return bool
 */
	public function phpValidSyntax($check) {
		if(empty($check[key($check)])) {
			return true;
		}

		if(!function_exists('exec')) {
			return true;
		}

		// CL版 php がインストールされてない場合はシンタックスチェックできないので true を返す
		exec('php --version 2>&1', $output, $exit);
		if($exit !== 0) {
			return true;
		}

		if(isWindows()) {
			$tmpName = tempnam(TMP, "syntax");
			$tmp = new File($tmpName);
			$tmp->open("w");
			$tmp->write($check[key($check)]);
			$tmp->close();
			$command = sprintf("php -l %s 2>&1", escapeshellarg($tmpName));
			exec($command, $output, $exit);
			$tmp->delete();
		} else {
			$format = 'echo %s | php -l 2>&1';
			$command = sprintf($format, escapeshellarg($check[key($check)]));
			exec($command, $output, $exit);
		}

		if($exit === 0) {
			return true;
		}
		$message = 'PHPの構文エラーです： ' . PHP_EOL . implode(' ' . PHP_EOL, $output);
		return $message;
	}

	public function getParentPageTemplate($id) {
		return 'default';
	}

/**
 * 固定ページテンプレートリストを取得する
 *
 * @param $contentId
 * @param $theme
 * @return array
 */
	public function getPageTemplateList($contentId, $theme) {
		if(!is_array($theme)) {
			$theme = [$theme];
		}
		$pageTemplates = [];
		foreach($theme as $value) {
			$pageTemplates = array_merge($pageTemplates, BcUtil::getTemplateList('Pages/templates', '', $value));
		}

		if($contentId != 1) {
			$ContentFolder = ClassRegistry::init('ContentFolder');
			$parentTemplate = $ContentFolder->getParentTemplate($contentId, 'page');
			$searchKey = array_search($parentTemplate, $pageTemplates);
			if ($searchKey !== false) {
				unset($pageTemplates[$searchKey]);
			}
			$pageTemplates = ['' => '親フォルダの設定に従う（' . $parentTemplate . '）'] + $pageTemplates;
		}
		return $pageTemplates;
	}

/**
 * URLより固定ページデータを探す
 * 
 * @param string $url
 * @param bool $publish
 * @return array|bool
 */
	public function findByUrl($url, $publish = true) {
		$url = preg_replace('/^\//', '', $url);
		$conditions = [
			'Content.url' => $this->getUrlPattern($url),
			'Content.type' => 'Page',
			['or' => [
				['Site.status' => true],
				['Site.status' => null]
			]]
		];
		if($publish) {
			$conditions = array_merge($conditions, $this->Content->getConditionAllowPublish());
		}
		$record = $this->find('first', [
			'conditions' => $conditions, 
			'order' => 'Content.url DESC', 
			'cache' => false,
			'joins' => [[
				'type' => 'LEFT',
				'table' => 'sites',
				'alias' => 'Site',
				'conditions' => "`Content`.`site_id`=`Site`.`id`",
				]
			]
		]);
		if(!$record) {
			return false;
		}
		if($record && empty($record['Site']['id'])) {
			$record['Site'] = $this->Content->Site->getRootMain()['Site'];
		}
		return $record;
	}
	
}
