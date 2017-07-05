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
 * ブログコメントコントローラー
 *
 * @package Blog.Controller
 * @property BcAuthComponent $BcAuth
 * @property CookieComponent $Cookie
 * @property BcAuthConfigureComponent $BcAuthConfigure
 * @property BcContentsComponent $BcContents
 */
class BlogCommentsController extends BlogAppController {

/**
 * モデル
 *
 * @var array
 */
	public $uses = array('Blog.BlogCategory', 'Blog.BlogComment', 'Blog.BlogPost');

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'RequestHandler', 'BcEmail', 'Security', 'BcCaptcha', 'BcContents' => ['type' => 'Blog.BlogContent']];

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->BcAuth->allow('add', 'captcha', 'smartphone_add', 'smartphone_captcha', 'get_token');

		if (BcUtil::isAdminSystem()) {
			$this->subMenuElements = array('blog_posts');
			$this->request->params['Content'] = $this->BcContents->getContent($this->request->params['pass'][0])['Content'];
			$this->Security->enabled = true;
			$this->Security->requireAuth('add');
		}
		
		$crumbs = array();
		
		if (!empty($this->params['pass'][1])) {
			$dbDatas = $this->BlogPost->find('first', ['conditions' => ['BlogPost.id' => $this->params['pass'][1]]]);
			if (!$dbDatas) {
				$this->notFound();
			}
			$this->blogPost = array('BlogPost' => $dbDatas['BlogPost']);
			$this->blogContent = array('BlogContent' => $dbDatas['BlogContent']);
			if (BcUtil::isAdminSystem()) {
				$crumbs[] = array('name' => $this->request->params['Content']['title'] . '設定', 'url' => array('controller' => 'blog_posts', 'action' => 'index', $this->blogContent['BlogContent']['id']));
				$crumbs[] = array('name' => $this->blogPost['BlogPost']['name'], 'url' => array('controller' => 'blog_posts', 'action' => 'edit', $this->blogContent['BlogContent']['id'], $this->blogPost['BlogPost']['id']));
			}	
		} elseif (!empty($this->params['pass'][0])) {
			if(!in_array($this->request->action, ['captcha', 'smartphone_captcha', 'get_token'])) {
				$dbDatas = $this->BlogPost->BlogContent->find('first', ['conditions' => ['BlogContent.id' => $this->params['pass'][0]]]);
				$this->blogContent = array('BlogContent' => $dbDatas['BlogContent']);
				if (BcUtil::isAdminSystem()) {
					$crumbs[] = array('name' => $this->request->params['Content']['title'] . '設定', 'url' => array('controller' => 'blog_posts', 'action' => 'index', $this->blogContent['BlogContent']['id']));
				}	
			}
		}

		$this->crumbs = array_merge($this->crumbs, $crumbs);
		
	}

/**
 * beforeRender
 *
 * @return void
 */
	public function beforeRender() {
		parent::beforeRender();
		if (!empty($this->blogContent)) {
			$this->set('blogContent', $this->blogContent);
		}
	}

/**
 * [ADMIN] ブログを一覧表示する
 *
 * @return void
 */
	public function admin_index($blogContentId, $blogPostId = null) {
		if (!$blogContentId || empty($this->blogContent['BlogContent'])) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect('/admin');
		}

		/* 検索条件 */
		if ($blogPostId) {
			$conditions['BlogComment.blog_post_id'] = $blogPostId;
			$this->pageTitle = '[' . $this->blogPost['BlogPost']['name'] . '] コメント一覧';
		} else {
			$conditions['BlogComment.blog_content_id'] = $blogContentId;
			$this->pageTitle = '[' . $this->request->params['Content']['title'] . '] コメント一覧';
		}

		/* 画面情報設定 */
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('BlogPost', array('group' => $blogContentId, 'default' => $default));

		// データを取得
		$this->paginate = array('conditions' => $conditions,
			'fields' => array(),
			'order' => 'BlogComment.created DESC',
			'limit' => $this->passedArgs['num']
		);

		$dbDatas = $this->paginate('BlogComment');
		$this->set('dbDatas', $dbDatas);
		$this->help = 'blog_comments_index';
	}

/**
 * [ADMIN] 一括削除
 *
 * @param int $blogContentId
 * @param int $blogPostId
 * @param int $id
 * @return void
 */
	protected function _batch_del($ids) {
		if ($ids) {
			foreach ($ids as $id) {
				$this->_del($id);
			}
		}
		return true;
	}

/**
 * [ADMIN] 削除処理　(ajax)
 *
 * @param int $blogContentId
 * @param int $blogPostId
 * @param int $id
 * @return void
 */
	public function admin_ajax_delete($blogContentId, $blogPostId, $id = null) {
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		if ($this->_del($id)) {
			exit(true);
		} else {
			exit();
		}
	}

/**
 * 削除処理
 *
 * @param int $blogContentId
 * @param int $blogPostId
 * @param int $id
 * @return void
 */
	protected function _del($id = null) {
		/* 削除処理 */
		if ($this->BlogComment->delete($id)) {
			if (isset($this->blogPost['BlogPost']['name'])) {
				$message = '記事「' . $this->blogPost['BlogPost']['name'] . '」へのコメントを削除しました。';
			} else {
				$message = '記事「' . $this->request->params['Content']['title'] . '」へのコメントを削除しました。';
			}
			$this->BlogComment->saveDbLog($message);
			return true;
		} else {
			return false;
		}
	}

/**
 * [ADMIN] 無効状態にする（AJAX）
 * 
 * @param string $blogContentId
 * @param string $blogPostId beforeFilterで利用
 * @param string $blogCommentId
 * @return void
 */
	public function admin_ajax_unpublish($blogContentId, $blogPostId, $id) {
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, false)) {
			clearViewCache();
			exit(true);
		} else {
			$this->ajaxError(500, $this->BlogComment->validationErrors);
		}
		exit();
	}

/**
 * [ADMIN] 有効状態にする（AJAX）
 * 
 * @param string $blogContentId
 * @param string $blogPostId beforeFilterで利用
 * @param string $blogCommentId
 * @return void
 */
	public function admin_ajax_publish($blogContentId, $blogPostId, $id) {
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, true)) {
			clearViewCache();
			exit(true);
		} else {
			$this->ajaxError(500, $this->BlogComment->validationErrors);
		}
		exit();
	}

/**
 * 一括公開
 * 
 * @param array $ids
 * @return boolean
 * @access protected 
 */
	protected function _batch_publish($ids) {
		if ($ids) {
			foreach ($ids as $id) {
				$this->_changeStatus($id, true);
			}
		}
		clearViewCache();
		return true;
	}

/**
 * 一括非公開
 * 
 * @param array $ids
 * @return boolean
 * @access protected 
 */
	protected function _batch_unpublish($ids) {
		if ($ids) {
			foreach ($ids as $id) {
				$this->_changeStatus($id, false);
			}
		}
		clearViewCache();
		return true;
	}

/**
 * ステータスを変更する
 * 
 * @param int $id
 * @param boolean $status
 * @return boolean 
 */
	protected function _changeStatus($id, $status) {
		$statusTexts = array(0 => '非公開状態', 1 => '公開状態');
		$data = $this->BlogComment->find('first', array('conditions' => array('BlogComment.id' => $id), 'recursive' => -1));
		$data['BlogComment']['status'] = $status;
		$this->BlogComment->set($data);

		if ($this->BlogComment->save()) {
			$statusText = $statusTexts[$status];
			if (isset($this->blogPost['BlogPost']['name'])) {
				$message = '記事「' . $this->blogPost['BlogPost']['name'] . '」へのコメントを' . $statusText . 'に設定しました。';
			} else {
				$message = '記事「' . $this->request->params['Content']['title'] . '」へのコメントを' . $statusText . 'に設定しました。';
			}
			$this->BlogComment->saveDbLog($message);
			return true;
		} else {
			return false;
		}
	}

/**
 * [AJAX] ブログコメントを登録する
 * 
 * @param string $blogContentId
 * @param string $blogPostId
 * @return boolean
 */
	public function add($blogContentId, $blogPostId) {
		Configure::write('debug', 0);

		if (!$this->request->data || !$blogContentId || !$blogPostId || empty($this->blogContent) || !$this->blogContent['BlogContent']['comment_use']) {
			$this->notFound();
		} else {

			// 画像認証を行う
			$captchaResult = true;
			if ($this->blogContent['BlogContent']['auth_captcha']) {
				$captchaResult = $this->BcCaptcha->check($this->request->data['BlogComment']['auth_captcha'], @$this->request->data['BlogComment']['captcha_id']);
				if (!$captchaResult) {
					$this->set('dbData', false);
					return false;
				} else {
					unset($this->request->data['BlogComment']['auth_captcha']);
				}
			}

			$result = $this->BlogComment->add($this->request->data, $blogContentId, $blogPostId, $this->blogContent['BlogContent']['comment_approve']);
			if ($result && $captchaResult) {
				$content = $this->BlogPost->BlogContent->Content->findByType('Blog.BlogContent', $this->blogContent['BlogContent']['id']);
				$this->request->data['Content'] = $content['Content'];
				$this->_sendCommentAdmin($blogPostId, $this->request->data);
				// コメント承認機能を利用していない場合は、公開されているコメント投稿者にアラートを送信
				if (!$this->blogContent['BlogContent']['comment_approve']) {
					$this->_sendCommentContributor($blogPostId, $this->request->data);
				}
				$this->set('dbData', $result['BlogComment']);
			} else {
				$this->set('dbData', false);
			}
		}
	}

/**
 * [AJAX] ブログコメントを登録する
 * 
 * @param string $blogContentId
 * @param string $blogPostId
 * @return boolean
 */
	public function smartphone_add($blogContentId, $blogPostId) {
		$this->setAction('add', $blogContentId, $blogPostId);
	}

/**
 * 認証用のキャプチャ画像を表示する
 * 
 * @return void
 */
	public function captcha($token = null) {
		$this->BcCaptcha->render($token);
		exit();
	}

/**
 * [SMARTPHONE] 認証用のキャプチャ画像を表示する
 * 
 * @return void
 */
	public function smartphone_captcha($token = null) {
		$this->BcCaptcha->render($token);
		exit();
	}

/**
 * コメント送信用にAjax経由でトークンを取得するアクション
 */
	public function get_token() {
		$this->_checkReferer();
		$this->autoRender = false;
		return $this->getToken();
	}

}
