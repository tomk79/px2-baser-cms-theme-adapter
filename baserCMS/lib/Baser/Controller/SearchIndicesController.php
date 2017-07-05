<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('HttpSocket', 'Core.Network/Http');

/**
 * 検索インデックスコントローラー
 *
 * @package	Baser.Controller
 * @property Content $Content
 * @property Site $Site
 */
class SearchIndicesController extends AppController {

/**
 * クラス名
 *
 * @var array
 */
	public $name = 'SearchIndices';

/**
 * モデル
 *
 * @var array
 */
	public $uses = array('SearchIndex', 'Page', 'Site', 'Content');

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure');

/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = array('BcText', 'BcForm');

/**
 * サブメニュー
 *
 * @var array
 */
	public $subMenuElements = ['site_configs', 'search_indices'];
	
/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		// 認証設定
		$this->BcAuth->allow('search', 'mobile_search', 'smartphone_search');

		if (!empty($this->request->params['admin'])) {
			$this->crumbs = array(
				array('name' => 'システム設定', 'url' => array('controller' => 'site_configs', 'action' => 'form')),
				array('name' => '検索インデックス管理', 'url' => array('controller' => 'search_indices', 'action' => 'index'))
			);
		}
		
		if(!BcUtil::isAdminSystem()) {
			$Content = ClassRegistry::init('Content');
			$currentSite = BcSite::findCurrent(true);
			$url = '/';
			if($this->request->params['action'] != 'search') {
				$prefix = str_replace('_search', '', $this->request->params['action']);
				if($prefix == $currentSite->name) {
					$url = '/' . $currentSite->alias . '/';
					$this->request->params['action'] = 'search';
					$this->action = 'search';
				}
			}
			$content = $Content->find('first', ['conditions' => ['Content.url' => $url], 'recursive' => 0]);
			$this->request->params['Content'] = $content['Content'];
			$this->request->params['Site'] = $content['Site'];
		}
		
	}

/**
 * コンテンツ検索
 *
 * @return void
 */
	public function search() {
		$datas = array();
		$query = array();

		$default = array('named' => array('num' => 10));
		$this->setViewConditions('SearchIndex', array('default' => $default, 'type' => 'get'));

		if (!empty($this->request->data['SearchIndex'])) {
			foreach ($this->request->data['SearchIndex'] as $key => $value) {
				$this->request->data['SearchIndex'][$key] = h($value);
			}
		}
		if (!empty($this->request->query['q'])) {
			$this->paginate = array(
				'conditions' => $this->_createSearchConditions($this->request->data),
				'order' => 'SearchIndex.priority DESC, SearchIndex.modified DESC, SearchIndex.id',
				'limit' => $this->passedArgs['num']
			);

			$datas = $this->paginate('SearchIndex');
			$query = $this->_parseQuery($this->request->query['q']);
		}
		$this->set('query', $query);
		$this->set('datas', $datas);
		$this->pageTitle = '検索結果一覧';
	}

/**
 * [MOBILE] コンテンツ検索
 */
	public function mobile_search() {
		$this->setAction('search');
	}
	
/**
 * [SMARTPHONE] コンテンツ検索
 */
	public function smartphone_search() {
		$this->setAction('search');
	}
	
/**
 * 検索キーワードを分解し配列に変換する
 *
 * @param string $query
 * @return array
 */
	protected function _parseQuery($query) {
		$query = str_replace('　', ' ', $query);
		if (strpos($query, ' ') !== false) {
			$query = explode(' ', $query);
		} else {
			$query = array($query);
		}
		return h($query);
	}

/**
 * 検索条件を生成する
 *
 * @param	array	$data
 * @return	array	$conditions
 * @access	protected
 */
	protected function _createSearchConditions($data) {
		$conditions = array('SearchIndex.status' => true);
		$query = '';
		if (!empty($data['SearchIndex']['q'])) {
			$query = $data['SearchIndex']['q'];
		}
		if (!empty($data['SearchIndex']['cf'])) {
			$data['SearchIndex']['content_filter_id'] = $data['SearchIndex']['cf'];
		}
		if (!empty($data['SearchIndex']['m'])) {
			$data['SearchIndex']['model'] = $data['SearchIndex']['m'];
		}
		if (isset($data['SearchIndex']['s'])) {
			$data['SearchIndex']['site_id'] = $data['SearchIndex']['s'];
		}
		if (!empty($data['SearchIndex']['f'])) {
			$content = $this->Content->find('first', ['fields' => ['lft', 'rght'], 'conditions' => ['Content.id' => $data['SearchIndex']['f']], 'recursive' => -1]);
			$data['SearchIndex']['rght <'] = $content['Content']['rght'];
			$data['SearchIndex']['lft >'] = $content['Content']['lft'];
		}
		
		unset($data['SearchIndex']['key']);
		unset($data['SearchIndex']['fields']);
		unset($data['SearchIndex']['_Token']);
		unset($data['SearchIndex']['q']);
		unset($data['SearchIndex']['cf']);
		unset($data['SearchIndex']['m']);
		unset($data['SearchIndex']['f']);
		unset($data['SearchIndex']['s']);
		
		$conditions = am($conditions, $this->postConditions($data));

		if ($query) {
			$query = $this->_parseQuery($query);
			foreach ($query as $key => $value) {
				$conditions['and'][$key]['or'][] = array('SearchIndex.title LIKE' => "%{$value}%");
				$conditions['and'][$key]['or'][] = array('SearchIndex.detail LIKE' => "%{$value}%");
			}
		}

		return $conditions;
	}

/**
 * [ADMIN] 検索インデックス
 * 
 * @return void
 */
	public function admin_index() {
		$this->pageTitle = '検索インデックス一覧';

		/* 画面情報設定 */
		$default = [
			'named' => ['num' => $this->siteConfigs['admin_list_num']],
			'SearchIndex' => ['site_id' => 0]
		];
		$this->setViewConditions('SearchIndex', array('default' => $default));
		$conditions = $this->_createAdminIndexConditions($this->request->data);
		$this->paginate = array(
			'conditions' => $conditions,
			'fields' => array(),
			'order' => 'SearchIndex.priority DESC, SearchIndex.modified DESC, SearchIndex.id',
			'limit' => $this->passedArgs['num']
		);
		$this->set('datas', $this->paginate('SearchIndex'));

		if ($this->request->is('ajax') || !empty($this->request->query['ajax'])) {
			$this->render('ajax_index');
			return;
		}

		$this->set('folders', $this->Content->getContentFolderList((int) $this->request->data['SearchIndex']['site_id'], ['conditions' => ['Content.site_root' => false]]));
		$this->set('sites', $this->Site->getSiteList());
		$this->search = 'search_indices_index';
		$this->help = 'search_indices_index';
	}

/**
 * [ADMIN] 検索インデックス登録
 * 
 * TODO 2013/8/8 ryuring
 * この機能は、URLより、baserCMSで管理されたコンテンツのタイトルとコンテンツ本体を取得し、検索インデックスに登録する為の機能だったが、
 * CakePHP２より、Viewの扱いが変更となった（ClassRegistryで管理されなくなった）為、requestAction 時のタイトルを取得できなくなった。
 * よって機能自体を一旦廃止する事とする。
 * 実装の際は、自動取得ではなく、手動で、タイトルとコンテンツ本体等を取得する仕様に変更する。
 * 
 * @return	void
 * @access 	public
 */
//	public function admin_add() {
//		$this->pageTitle = '検索インデックス登録';
//
//		if ($this->request->data) {
//			$url = $this->request->data['SearchIndex']['url'];
//			$url = str_replace(FULL_BASE_URL . $this->request->base, '', $url);
//
//			if (!$this->SearchIndex->find('count', array('conditions' => array('SearchIndex.url' => $url)))) {
//
//				// ルーティングのデフォルト設定を再読み込み（requestActionでルーティング設定がダブって登録されてしまう為）
//				Router::reload();
//				// URLのデータを取得
//				try {
//					$searchIndex = $this->requestAction($url, array('return' => 1));
//				} catch (Exception $e) {
//					$searchIndex = $e;
//				}
//
//				Router::reload();
//				// 元の設定を復元
//				Router::setRequestInfo($this->request);
//
//				if (!is_a($searchIndex, 'Exception')) {
//					$searchIndex = preg_replace('/<!-- BaserPageTagBegin -->.*?<!-- BaserPageTagEnd -->/is', '', $searchIndex);
//				} elseif (preg_match('/\.html/', $url)) {
//					App::uses('HttpSocket', 'Network/Http');
//					$socket = new HttpSocket();
//					// ※ Router::url() では、スマートURLオフの場合、/app/webroot/ 内のURLが正常に取得できない
//					$HttpSocketResponse = $socket->get(siteUrl() . preg_replace('/^\//', '', $url));
//					$code = $HttpSocketResponse->code;
//					if ($code != 200) {
//						unset($searchIndex);
//					} else {
//						if (preg_match('/<body>(.*?)<\/body>/is', $HttpSocketResponse->body, $matches)) {
//							$searchIndex = $matches[1];
//						} else {
//							$searchIndex = '';
//						}
//					}
//				} else {
//					unset($searchIndex);
//				}
//
//				if (isset($searchIndex)) {
//					$searchIndex = Sanitize::stripAll($searchIndex);
//					$searchIndex = strip_tags($searchIndex);
//					$data = array('SearchIndex' => array(
//							'title'		=> $this->request->data['SearchIndex']['title'],
//							'detail'	=> $searchIndex,
//							'url'		=> $url,
//							'type'		=> 'その他',
//							'status'	=> true,
//							'priority'	=> 0.5
//					));
//					$this->SearchIndex->create($data);
//					if ($this->SearchIndex->save()) {
//						$this->setMessage('検索インデックスに ' . $url . ' を追加しました。');
//						$this->redirect(array('action' => 'index'));
//					} else {
//						$this->setMessage('保存中にエラーが発生しました。', true);
//					}
//				} else {
//					$this->SearchIndex->invalidate('url', '入力したURLは存在しないか、検索インデックスに登録できるURLではありません。');
//					$this->setMessage('保存中にエラーが発生しました。', true);
//				}
//			} else {
//				$this->SearchIndex->invalidate('url', '既に登録済のURLです。');
//				$this->setMessage('入力エラーです。内容を修正してください。', true);
//			}
//		}
//		$this->help = 'search_indices_add';
//	}

/**
 * [ADMIN] 検索インデックス削除　(ajax)
 *
 * @param	int		$id
 * @return	void
 * @access 	public
 */
	public function admin_ajax_delete($id = null) {
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		/* 削除処理 */
		if ($this->SearchIndex->delete($id)) {
			$message = '検索インデックスより NO.' . $id . ' を削除しました。';
			$this->SearchIndex->saveDbLog($message);
			exit(true);
		}
		exit();
	}

/**
 * [ADMIN] 検索インデックス一括削除
 *
 * @param	int		$id
 * @return	void
 * @access 	public
 */
	protected function _batch_del($ids) {
		if ($ids) {

			foreach ($ids as $id) {

				/* 削除処理 */
				if ($this->SearchIndex->delete($id)) {
					$message = '検索インデックスより NO.' . $id . ' を削除しました。';
					$this->SearchIndex->saveDbLog($message);
				}
			}
		}
		return true;
	}

/**
 * [AJAX] 優先順位を変更する
 * 
 * @return boolean
 */
	public function admin_ajax_change_priority() {
		if ($this->request->data) {
			$this->SearchIndex->set($this->request->data);
			if ($this->SearchIndex->save()) {
				echo true;
			}
		}
		exit();
	}

/**
 * 管理画面ページ一覧の検索条件を取得する
 *
 * @param	array		$data
 * @return	string
 * @access	protected
 */
	protected function _createAdminIndexConditions($data) {
		if (empty($data['SearchIndex'])) {
			return array();
		}
		/* 条件を生成 */
		$conditions = array();

		$type = $status = $keyword = $folderId = $siteId = null;
		if(isset($data['SearchIndex']['type'])) {
			$type = $data['SearchIndex']['type'];	
		}
		if(isset($data['SearchIndex']['status'])) {
			$status = $data['SearchIndex']['status'];
		}
		if(isset($data['SearchIndex']['keyword'])) {
			$keyword = $data['SearchIndex']['keyword'];
		}
		if(isset($data['SearchIndex']['folder_id'])) {
			$folderId = $data['SearchIndex']['folder_id'];
		}
		if(isset($data['SearchIndex']['site_id'])) {
			$siteId = $data['SearchIndex']['site_id'];
		}

		unset($data['SearchIndex']['type']);
		unset($data['SearchIndex']['status']);
		unset($data['SearchIndex']['keyword']);
		unset($data['SearchIndex']['folder_id']);
		unset($data['SearchIndex']['site_id']);
		unset($data['SearchIndex']['site_id']);
		unset($data['SearchIndex']['open']);
		unset($data['ListTool']);
		if (empty($data['SearchIndex']['priority'])) {
			unset($data['SearchIndex']['priority']);
		}
		foreach ($data['SearchIndex'] as $key => $value) {
			if (preg_match('/priority_[0-9]+$/', $key)) {
				unset($data['SearchIndex'][$key]);
			}
		}
		if ($data['SearchIndex']) {
			$conditions = $this->postConditions($data);
		}
		if ($type) {
			$conditions['SearchIndex.type'] = $type;
		}
		if($siteId) {
			$conditions['SearchIndex.site_id'] = $siteId;
		} else {
			$conditions['SearchIndex.site_id'] = 0;
		}
		if ($folderId) {
			$content = $this->Content->find('first', ['fields' => ['lft', 'rght'], 'conditions' => ['Content.id' => $folderId], 'recursive' => -1]);
			$conditions['SearchIndex.rght <'] = $content['Content']['rght'];
			$conditions['SearchIndex.lft >'] = $content['Content']['lft'];
		}
		if ($status != '') {
			$conditions['SearchIndex.status'] = $status;
		}
		if ($keyword) {
			$conditions['and']['or'] = array(
				'SearchIndex.title LIKE' => '%' . $keyword . '%',
				'SearchIndex.detail LIKE' => '%' . $keyword . '%'
			);
		}

		return $conditions;
	}

/**
 * 検索インデックスを再構築する 
 */
	public function admin_reconstruct() {
		set_time_limit(0);
		$contents = $this->Content->find('all', ['order' => 'lft', 'recursive' => -1]);
		$models = [];
		$db = $this->SearchIndex->getDataSource();
		$this->SearchIndex->begin();
		$db->truncate('search_indices');
		$result = true;
		if($contents) {
			foreach($contents as $content) {
				if(isset($models[$content['Content']['type']])) {
					$Model = $models[$content['Content']['type']];
				} else {
					if(ClassRegistry::isKeySet($content['Content']['type'])) {
						$models[$content['Content']['type']] = $Model = ClassRegistry::getObject($content['Content']['type']);
					} else {
						if($content['Content']['plugin'] == 'Core') {
							$modelName = $content['Content']['type'];
						} else {
							$modelName = $content['Content']['plugin'] . '.' . $content['Content']['type'];
						}
						$models[$content['Content']['type']] = $Model = ClassRegistry::init($modelName);
					}
				}
				$entity = $Model->find('first', ['conditions' => [$Model->name . '.id' => $content['Content']['entity_id']], 'recursive' => 0]);
				if(!$Model->save($entity, false)) {
					$result = false;
				}
			}
		}
		if($result) {
			$this->SearchIndex->commit();
			$this->setMessage('検索インデックスの再構築に成功しました。', false, true);
		} else {
			$this->SearchIndex->roleback();
			$this->setMessage('検索インデックスの再構築に失敗しました。', true, true);
		}
		$this->redirect(['action' => 'index']);
	}
}
