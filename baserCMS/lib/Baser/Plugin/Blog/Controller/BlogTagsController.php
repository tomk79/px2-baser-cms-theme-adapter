<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * ブログタグコントローラー
 *
 * @package Blog.Controller
 * @property BcAuthComponent $BcAuth
 * @property CookieComponent $Cookie
 * @property BcAuthConfigureComponent $BcAuthConfigure
 * @property BcContentsComponent $BcContents
 */
class BlogTagsController extends BlogAppController {

/**
 * クラス名
 *
 * @var array
 */
	public $name = 'BlogTags';

/**
 * モデル
 *
 * @var array
 */
	public $uses = array('Blog.BlogCategory', 'Blog.BlogTag');

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure', 'BcContents');
	
/**
 * [ADMIN] タグ一覧
 *
 * @return void
 */
	public function admin_index() {
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num'], 'sort' => 'id', 'direction' => 'asc'));
		$this->setViewConditions('BlogTag', array('default' => $default));

		$this->paginate = array(
			'order' => 'BlogTag.id',
			'limit' => $this->passedArgs['num'],
			'recursive' => 0
		);
		$this->set('datas', $this->paginate('BlogTag'));

		$this->pageTitle = 'タグ一覧';
	}

/**
 * [ADMIN] タグ登録
 *
 * @return void
 */
	public function admin_add() {
		if (!empty($this->request->data)) {

			$this->BlogTag->create($this->request->data);
			if ($this->BlogTag->save()) {
				$this->setMessage('タグ「' . $this->request->data['BlogTag']['name'] . '」を追加しました。', false, true);
				$this->redirect(array('action' => 'index'));
			} else {
				$this->setMessage('エラーが発生しました。内容を確認してください。', true);
			}
		}

		$this->pageTitle = '新規タグ登録';
		$this->render('form');
	}

/**
 * [ADMIN] タグ編集
 *
 * @param int $id タグID
 * @return void
 */
	public function admin_edit($id) {
		if (!$id) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('action' => 'index'));
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->BlogTag->read(null, $id);
		} else {

			$this->BlogTag->set($this->request->data);
			if ($this->BlogTag->save()) {
				$this->setMessage('タグ「' . $this->request->data['BlogTag']['name'] . '」を更新しました。', false, true);
				$this->redirect(array('action' => 'index'));
			} else {
				$this->setMessage('エラーが発生しました。内容を確認してください。', true);
			}
		}

		$this->pageTitle = 'タグ編集： ' . $this->request->data['BlogTag']['name'];
		$this->render('form');
	}

/**
 * [ADMIN] 削除処理
 *
 * @param int $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->_checkSubmitToken();
		if (!$id) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('action' => 'index'));
		}

		$data = $this->BlogTag->read(null, $id);

		if ($this->BlogTag->delete($id)) {
			$this->setMessage('タグ「' . $this->BlogTag->data['BlogTag']['name'] . '」を削除しました。', false, true);
		} else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * [ADMIN] 削除処理　(ajax)
 *
 * @param int $id
 * @return void
 */
	public function admin_ajax_delete($id = null) {
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		$data = $this->BlogTag->read(null, $id);
		if ($this->BlogTag->delete($id)) {
			$message = 'タグ「' . $this->BlogTag->data['BlogTag']['name'] . '」を削除しました。';
			$this->BlogTag->saveDbLog($message);
			exit(true);
		}
		exit();
	}

/**
 * [ADMIN] 一括削除
 *
 * @param int $id
 * @return void
 */
	protected function _batch_del($ids) {
		if ($ids) {
			foreach ($ids as $id) {
				$data = $this->BlogTag->read(null, $id);
				if ($this->BlogTag->delete($id)) {
					$message = 'タグ「' . $this->BlogTag->data['BlogTag']['name'] . '」を削除しました。';
					$this->BlogTag->saveDbLog($message);
				}
			}
		}
		return true;
	}

/**
 * [ADMIN] AJAXタグ登録
 *
 * @return void
 */
	public function admin_ajax_add() {
		if (!empty($this->request->data)) {
			$this->BlogTag->create($this->request->data);
			if ($data = $this->BlogTag->save()) {
				$result = array($this->BlogTag->id => $data['BlogTag']['name']);
				$this->set('result', $result);
			} else {
				$this->ajaxError(500, $this->BlogTag->validationErrors);
			}
		} else {
			$this->ajaxError(500, '無効な処理です。');
		}
	}

}
