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

App::uses('BlogAppController', 'Blog.Controller');

/**
 * ブログ記事コントローラー
 *
 * @package Blog.Controller
 * @property BlogContent $BlogContent
 * @property BlogCategory $BlogCategory
 * @property BlogPost $BlogPost
 * @property BcAuthComponent $BcAuth
 * @property CookieComponent $Cookie
 * @property BcAuthConfigureComponent $BcAuthConfigure
 * @property BcContentsComponent $BcContents
 * @property Content $Content
 */
class BlogController extends BlogAppController {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'Blog';

/**
 * モデル
 *
 * @var array
 */
	public $uses = array('Blog.BlogCategory', 'Blog.BlogPost', 'Blog.BlogContent');

/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = array('BcText', 'BcTime', 'BcFreeze', 'BcArray', 'Paginator', 'Blog.Blog');

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'RequestHandler', 'BcEmail', 'Security', 'BcContents' => ['type' => 'Blog.BlogContent', 'useViewCache' => false]];

/**
 * ぱんくずナビ
 *
 * @var array
 */
	public $crumbs = array();

/**
 * サブメニューエレメント
 *
 * @var array
 */
	public $subMenuElements = array();

/**
 * ブログデータ
 *
 * @var array
 */
	public $blogContent = array();

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		/* 認証設定 */
		$this->BcAuth->allow(
			'index', 'mobile_index', 'smartphone_index', 'archives', 'mobile_archives', 'smartphone_archives', 'posts', 'mobile_posts', 'smartphone_posts', 'get_calendar', 'get_categories', 'get_posted_months', 'get_posted_years', 'get_recent_entries', 'get_authors'
		);
		$blogContentId = null;
		if(!empty($this->request->params['entityId'])) {
			$blogContentId = $this->request->params['entityId'];	
		} elseif(!empty($this->request->params['pass'])) {
			// 後方互換の為 pass もチェック
			$blogContentId = $this->request->params['pass'];
		}
		$this->BlogContent->recursive = -1;
		if ($this->contentId) {
			$this->blogContent = $this->BlogContent->read(null, $this->contentId);
		} else {
			$this->blogContent = $this->BlogContent->read(null, $blogContentId);
			$this->contentId = $blogContentId;
		}
		
		if(empty($this->request->params['Content'])) {
			// ウィジェット系の際にコンテンツ管理上のURLでないので自動取得できない
			$content = $this->BcContents->getContent($blogContentId);
			$this->request->params['Content'] = $content['Content'];
			$this->request->params['Site'] = $content['Site'];
		}

		$this->BlogPost->setupUpload($this->blogContent['BlogContent']['id']);

		$this->subMenuElements = array('default');

		// ページネーションのリンク対策
		// コンテンツ名を変更している際、以下の設定を行わないとプラグイン名がURLに付加されてしまう
		// Viewで $paginator->options = array('url' => $this->passedArgs) を行う事が前提
		if (!isset($this->request->params['admin'])) {
			$this->passedArgs['controller'] = $this->request->params['Content']['name'];
			$this->passedArgs['plugin'] = $this->request->params['Content']['name'];
			$this->passedArgs['action'] = $this->action;
		}

		// コメント送信用のトークンを出力する為にセキュリティコンポーネントを利用しているが、
		// 表示用のコントローラーなのでポストデータのチェックは必要ない
		if (Configure::read('debug') > 0) {
			$this->Security->validatePost = false;
			$this->Security->csrfCheck = false;
		} else {
			$this->Security->enabled = true;
			$this->Security->validatePost = false;
		}
	}

/**
 * beforeRender
 *
 * @return void
 */
	public function beforeRender() {
		parent::beforeRender();

		$this->set('blogContent', $this->blogContent);

		if ($this->blogContent['BlogContent']['widget_area']) {
			$this->set('widgetArea', $this->blogContent['BlogContent']['widget_area']);
		}
	}

/**
 * [PUBLIC] ブログを一覧表示する
 *
 * @return void
 */
	public function index() {

		if($this->BcContents->preview == 'default' && $this->request->data) {
			$this->blogContent['BlogContent'] = $this->request->data['BlogContent'];
		}
		if ($this->RequestHandler->isRss()) {
			Configure::write('debug', 0);
			$this->set('channel', array(
				'title' => h($this->request->params['Content']['title'] . '｜' . $this->siteConfigs['name']),
				'description' => h(strip_tags($this->blogContent['BlogContent']['description']))
			));
			$this->layout = 'default';
			$template = 'index';
			$listCount = $this->blogContent['BlogContent']['feed_count'];
		} else {
			$template = $this->blogContent['BlogContent']['template'] . DS . 'index';
			$listCount = $this->blogContent['BlogContent']['list_count'];
		}

		$datas = $this->_getBlogPosts(array('listCount' => $listCount));
		$this->set('editLink', array('admin' => true, 'plugin' => 'blog', 'controller' => 'blog_contents', 'action' => 'edit', $this->blogContent['BlogContent']['id']));
		$this->set('posts', $datas);
		$this->set('single', false);
		$this->pageTitle = $this->request->params['Content']['title'];
		$this->render($template);
	}

/**
 * [MOBILE] ブログ記事を一覧表示する
 *
 * @return void
 */
	public function mobile_index() {
		$this->setAction('index');
	}

/**
 * [SMARTPHONE] ブログ記事を一覧表示する
 *
 * @return void
 */
	public function smartphone_index() {
		$this->setAction('index');
	}

/**
 * [PUBLIC] ブログアーカイブを表示する
 *
 * @param mixed	blog_post_id / type
 * @param mixed	blog_post_id / ""
 * @return void
 */
	public function archives() {

		// パラメーター処理
		$pass = $this->request->params['pass'];
		$type = $year = $month = $day = $id = '';
		$crumbs = $posts = array();
		$single = false;
		$posts = array();

		if ($pass[0] == 'category') {
			$type = 'category';
		} elseif ($pass[0] == 'author') {
			$type = 'author';
		} elseif ($pass[0] == 'tag') {
			$type = 'tag';
		} elseif ($pass[0] == 'date') {
			$type = 'date';
		}

		$crumbs[] = array('name' => $this->request->params['Content']['title'], 'url' => $this->request->params['Content']['url']);
		
		switch ($type) {

			/* カテゴリ一覧 */
			case 'category':

				$category = $pass[count($pass) - 1];
				if (empty($category)) {
					//$this->notFound();
				}

				// ナビゲーションを設定
				$categoryId = $this->BlogCategory->field('id', array(
					'BlogCategory.blog_content_id' => $this->contentId,
					'BlogCategory.name' => urlencode($category)
				));

				if (!$categoryId) {
					$this->notFound();
				}

				// 記事を取得
				$posts = $this->_getBlogPosts(array('conditions' => array('category' => urlencode($category))));
				$blogCategories = $this->BlogCategory->getPath($categoryId, array('name', 'title'));
				if (count($blogCategories) > 1) {
					foreach ($blogCategories as $key => $blogCategory) {
						if ($key < count($blogCategories) - 1) {
							$crumbs[] = array('name' => $blogCategory['BlogCategory']['title'], 'url' => $this->request->params['Content']['url'] . '/archives/category/' . $blogCategory['BlogCategory']['name']);
						}
					}
				}
				$this->pageTitle = $blogCategories[count($blogCategories) - 1]['BlogCategory']['title'];
				$template = $this->blogContent['BlogContent']['template'] . DS . 'archives';

				$this->set('blogArchiveType', $type);

				break;

			case 'author':
				$author = h($pass[count($pass) - 1]);
				$posts = $this->_getBlogPosts(array('conditions' => array('author' => $author)));
				$data = $this->BlogPost->User->find('first', array('fields' => array('real_name_1', 'real_name_2', 'nickname'), 'conditions' => array('User.name' => $author)));
				App::uses('BcBaserHelper', 'View/Helper');
				$BcBaser = new BcBaserHelper(new View());
				$userName = $BcBaser->getUserName($data);
				$this->pageTitle = urldecode($userName);
				$template = $this->blogContent['BlogContent']['template'] . DS . 'archives';
				$this->set('blogArchiveType', $type);
				break;

			/* タグ別記事一覧 */
			case 'tag':

				$tag = h($pass[count($pass) - 1]);
				if (empty($this->blogContent['BlogContent']['tag_use']) || empty($tag)) {
					$this->notFound();
				}
				$posts = $this->_getBlogPosts(array('conditions' => array('tag' => $tag)));
				$this->pageTitle = urldecode($tag);
				$template = $this->blogContent['BlogContent']['template'] . DS . 'archives';

				$this->set('blogArchiveType', $type);

				break;

			/* 月別アーカイブ一覧 */
			case 'date':

				$year = h($pass[1]);
				$month = h(@$pass[2]);
				$day = h(@$pass[3]);
				if (!$year && !$month && !$day) {
					$this->notFound();
				}
				$posts = $this->_getBlogPosts(array('conditions' => array('year' => $year, 'month' => $month, 'day' => $day)));
				$this->pageTitle = $year . '年';
				if ($month) {
					$this->pageTitle .= $month . '月';
				}
				if ($day) {
					$this->pageTitle .= $day . '日';
				}
				$template = $this->blogContent['BlogContent']['template'] . DS . 'archives';

				if ($day) {
					$this->set('blogArchiveType', 'daily');
				} elseif ($month) {
					$this->set('blogArchiveType', 'monthly');
				} else {
					$this->set('blogArchiveType', 'yearly');
				}

				break;

			/* 単ページ */
			default:

				if (!empty($pass[0])) {
					$id = $pass[0];
				}
				// プレビュー
				if ($this->BcContents->preview) {
					if (!empty($this->request->data['BlogPost'])) {
						$this->request->data = $this->BlogPost->saveTmpFiles($this->request->data, mt_rand(0, 99999999));
						$post = $this->BlogPost->createPreviewData($this->request->data);

					} else {
						$post = $this->_getBlogPosts(['preview' => true, 'conditions' => ['id' => $id]]);
						if (isset($post[0])) {
							$post = $post[0];
							if ($this->BcContents->preview == 'draft') {
								$post['BlogPost']['detail'] = $post['BlogPost']['detail_draft'];
							}
						}
					}
					
				} else {
					if (empty($pass[0])) {
						$this->notFound();
					}

					// コメント送信
					if (isset($this->request->data['BlogComment'])) {
						$this->add_comment($id);
					}

					$post = $this->_getBlogPosts(['conditions' => ['id' => $id]]);
					if (!empty($post[0])) {
						$post = $post[0];
					} else {
						$this->notFound();
					}
					
					// 一覧系のページの場合、時限公開の記事が存在し、キャッシュがあると反映できないが、
					// 詳細ページの場合は、記事の終了期間の段階でキャッシュが切れる前提となる為、キャッシュを利用する
					// プレビューでは利用しない事。
					// コメント送信時、キャッシュはクリアされるが、モバイルの場合、このメソッドに対してデータを送信する為、
					// キャッシュがあるとデータが処理されないので、キャッシュは全く作らない設定とする
					if(BcSite::findCurrent()->device != 'mobile') {
						$this->BcContents->useViewCache = true;
					}

				}
				
				if (BcUtil::isAdminUser()) {
					$this->set('editLink', array('admin' => true, 'plugin' => 'blog', 'controller' => 'blog_posts', 'action' => 'edit', $post['BlogPost']['blog_content_id'], $post['BlogPost']['id']));
				}

				// ナビゲーションを設定
				if (!empty($post['BlogPost']['blog_category_id'])) {
					$blogCategories = $this->BlogCategory->getPath($post['BlogPost']['blog_category_id'], array('name', 'title'));
					if ($blogCategories) {
						foreach ($blogCategories as $blogCategory) {
							$crumbs[] = array('name' => $blogCategory['BlogCategory']['title'], 'url' => $this->request->params['Content']['url'] . '/archives/category/' . $blogCategory['BlogCategory']['name']);
						}
					}
				}
				$this->pageTitle = $post['BlogPost']['name'];
				$single = true;
				$template = $this->blogContent['BlogContent']['template'] . DS . 'single';
				if ($this->BcContents->preview) {
					$this->blogContent['BlogContent']['comment_use'] = false;
				}
				$this->set('post', $post);
		}

		// 表示設定
		$this->crumbs = array_merge($this->crumbs, $crumbs);
		$this->set('single', $single);
		$this->set('posts', $posts);
		$this->set('year', $year);
		$this->set('month', $month);
		$this->render($template);
	}

/**
 * コメントを送信する
 *
 * @param int $id
 * @return void
 */
	public function add_comment($id) {
		// blog_post_idを取得
		$conditions = array(
			'BlogPost.no' => $id,
			'BlogPost.blog_content_id' => $this->contentId
		);
		$conditions = am($conditions, $this->BlogPost->getConditionAllowPublish());

		// 毎秒抽出条件が違うのでキャッシュしない
		$data = $this->BlogPost->find('first', array(
			'conditions' => $conditions,
			'fields' => array('BlogPost.id'),
			'cache' => false,
			'recursive' => -1
		));

		if (empty($data['BlogPost']['id'])) {
			$this->notFound();
		} else {
			$postId = $data['BlogPost']['id'];
		}

		if ($this->BlogPost->BlogComment->add($this->request->data, $this->contentId, $postId, $this->blogContent['BlogContent']['comment_approve'])) {

			$this->_sendCommentAdmin($postId, $this->request->data);
			// コメント承認機能を利用していない場合は、公開されているコメント投稿者にアラートを送信
			if (!$this->blogContent['BlogContent']['comment_approve']) {
				$this->_sendCommentContributor($postId, $this->request->data);
			}
			if ($this->blogContent['BlogContent']['comment_approve']) {
				$commentMessage = '送信が完了しました。送信された内容は確認後公開させて頂きます。';
			} else {
				$commentMessage = 'コメントの送信が完了しました。';
			}
			$this->request->data = null;
		} else {

			$commentMessage = 'コメントの送信に失敗しました。';
		}
		clearViewCache();
		$this->set('commentMessage', $commentMessage);
	}

/**
 * ブログ記事を取得する
 *
 * @param array $options
 * @return array
 */
	protected function _getBlogPosts($options = array()) {
		// listCountの処理 （num が優先）
		// TODO num に統一する
		if (!empty($options['listCount'])) {
			if (empty($options['num'])) {
				$options['num'] = $options['listCount'];
			}
		}

		// named の 処理
		$named = array();
		if (!empty($this->request->params['named'])) {
			$named = $this->request->params['named'];
		}
		if (!empty($named['direction'])) {
			$options['direction'] = $named['direction'];
			unset($named['direction']);
		}
		if (!empty($named['num'])) {
			$options['num'] = $named['num'];
			unset($named['num']);
		}
		if (!empty($named['page'])) {
			$options['page'] = $named['page'];
			unset($named['page']);
		}
		if (!empty($named['sort'])) {
			$options['sort'] = $named['sort'];
			unset($named['sort']);
		}
		if (!empty($named['contentId'])) {
			$options['contentId'] = $named['contentId'];
			unset($named['contentId']);
		}

		$_conditions = array();
		if (!empty($this->request->params['named'])) {
			if (!empty($options['conditions'])) {
				$_conditions = array_merge($options['conditions'], $this->request->params['named']);
			} else {
				$_conditions = $this->request->params['named'];
			}
		} elseif (!empty($options['conditions'])) {
			$_conditions = $options['conditions'];
		}
		unset($options['conditions']);

		$_conditions = array_merge(array(
			'category' => null,
			'tag' => null,
			'year' => null,
			'month' => null,
			'day' => null,
			'id' => null,
			'keyword' => null,
			'author' => null
			), $_conditions);

		$options = array_merge(array(
			'direction' => $this->blogContent['BlogContent']['list_direction'],
			'num' => $this->blogContent['BlogContent']['list_count'],
			'page' => 1,
			'sort' => 'posts_date'
			), $options);

		extract($options);

		$expects = array('BlogContent', 'BlogCategory', 'User', 'BlogTag');
		$conditions = array();

		if (isset($options['contentId']) && $options['contentId']) {
			$conditions[] = array('BlogPost.blog_content_id' => $options['contentId']);
		} elseif ($this->contentId) {
			$conditions[] = array('BlogPost.blog_content_id' => $this->contentId);
		}
		
		// カテゴリ条件
		if ($_conditions['category']) {
			$category = $_conditions['category'];
			$categoryId = $this->BlogCategory->field('id', array(
				'BlogCategory.blog_content_id' => $this->contentId,
				'BlogCategory.name' => $category
			));

			if ($categoryId === false) {
				$categoryIds = '';
			} else {
				$categoryIds = array(0 => $categoryId);
				// 指定したカテゴリ名にぶら下がる子カテゴリを取得
				$catChildren = $this->BlogCategory->children($categoryId);
				if ($catChildren) {
					$catChildren = Hash::extract($catChildren, '{n}.BlogCategory.id');
					$categoryIds = am($categoryIds, $catChildren);
				}
			}
			$conditions['BlogPost.blog_category_id'] = $categoryIds;
		}

		// タグ条件
		if ($_conditions['tag']) {

			$tag = $_conditions['tag'];
			if (!is_array($tag)) {
				$tag = array($tag);
			}

			foreach ($tag as $key => $value) {
				$tag[$key] = urldecode($value);
			}

			$tags = $this->BlogPost->BlogTag->find('all', array(
				'conditions' => array('BlogTag.name' => $tag),
				'recursive' => 1
			));
			if (isset($tags[0]['BlogPost'][0]['id'])) {
				$ids = Hash::extract($tags, '{n}.BlogPost.{n}.id');
				$conditions['BlogPost.id'] = $ids;
			} else {
				return array();
			}
		}

		// キーワード条件
		if ($_conditions['keyword']) {
			$keyword = $_conditions['keyword'];
			if (preg_match('/\s/', $keyword)) {
				$keywords = explode("\s", $keyword);
			} else {
				$keywords = array($keyword);
			}
			foreach ($keywords as $key => $value) {
				$keywords[$key] = urldecode($value);
				$conditions['or'][]['BlogPost.name LIKE'] = '%' . $value . '%';
				$conditions['or'][]['BlogPost.content LIKE'] = '%' . $value . '%';
				$conditions['or'][]['BlogPost.detail LIKE'] = '%' . $value . '%';
			}
		}

		// 年月日条件
		if ($_conditions['year'] || $_conditions['month'] || $_conditions['day']) {
			$year = $_conditions['year'];
			$month = $_conditions['month'];
			$day = $_conditions['day'];

			$db = ConnectionManager::getDataSource($this->BlogPost->useDbConfig);
			$datasouce = strtolower(preg_replace('/^Database\/Bc/', '', $db->config['datasource']));

			switch ($datasouce) {
				case 'mysql':
				case 'csv':
					if ($year) {
						$conditions["YEAR(BlogPost.posts_date)"] = $year;
					}
					if ($month) {
						$conditions["MONTH(BlogPost.posts_date)"] = $month;
					}
					if ($day) {
						$conditions["DAY(BlogPost.posts_date)"] = $day;
					}
					break;
				case 'postgres':
					if ($year) {
						$conditions["date_part('year',BlogPost.posts_date) = "] = $year;
					}
					if ($month) {
						$conditions["date_part('month',BlogPost.posts_date) = "] = $month;
					}
					if ($day) {
						$conditions["date_part('day',BlogPost.posts_date) = "] = $day;
					}
					break;
				case 'sqlite':
					if ($year) {
						$conditions["strftime('%Y',BlogPost.posts_date)"] = $year;
					}
					if ($month) {
						$conditions["strftime('%m',BlogPost.posts_date)"] = sprintf('%02d', $month);
					}
					if ($day) {
						$conditions["strftime('%d',BlogPost.posts_date)"] = sprintf('%02d', $day);
					}
					break;
			}
		}

		//author条件
		if ($_conditions['author']) {
			$author = $_conditions['author'];
			App::uses('User', 'Model');
			$user = new User();
			$userId = $user->field('id', array(
				'User.name' => $author
			));
			$conditions['BlogPost.user_id'] = $userId;
		}
		
		if ($_conditions['id']) {
			$conditions["BlogPost.no"] = $_conditions['id'];
			$expects[] = 'BlogComment';
			$this->BlogPost->hasMany['BlogComment']['conditions'] = array('BlogComment.status' => true);
			$num = 1;
		}

		unset($_conditions['author']);
		unset($_conditions['category']);
		unset($_conditions['tag']);
		unset($_conditions['keyword']);
		unset($_conditions['year']);
		unset($_conditions['month']);
		unset($_conditions['day']);
		unset($_conditions['id']);
		unset($_conditions['page']);
		unset($_conditions['num']);
		unset($_conditions['sort']);
		unset($_conditions['direction']);
		unset($_conditions['contentId']);

		if ($_conditions) {
			// とりあえず BlogPost のフィールド固定
			$conditions = array_merge($conditions, $this->postConditions(array('BlogPost' => $_conditions)));
		}

		if(empty($options['preview'])) {
			$conditions = array_merge($conditions, $this->BlogPost->getConditionAllowPublish());
		}

		$this->BlogPost->expects($expects, false);

		if (strtoupper($direction) == 'RANDOM') {
			$db = ConnectionManager::getDataSource($this->BlogPost->useDbConfig);
			$datasouce = strtolower(preg_replace('/^Database\/Bc/', '', $db->config['datasource']));
			switch ($datasouce) {
				case 'mysql':
					$order = 'RAND()';
					break;
				case 'postgres':
					$order = 'RANDOM()';
					break;
				case 'sqlite':
					$order = 'RANDOM()';
					break;
			}
		} else {
			$order = "BlogPost.{$sort} {$direction}, BlogPost.id {$direction}";
		}

		// 毎秒抽出条件が違うのでキャッシュしない
		$this->paginate = array(
			'conditions' => $conditions,
			'fields' => array(),
			'order' => $order,
			'limit' => $num,
			'recursive' => 2,
			'cache' => false
		);
		$this->BlogPost->BlogContent->unbindModel([
			'hasMany' => ['BlogPost', 'BlogCategory']
		]);
		$this->BlogPost->BlogCategory->unbindModel([
			'hasMany' => ['BlogPost']
		]);
		$this->BlogPost->User->unbindModel([
			'hasMany' => ['Favorite']
		]);
		return $this->paginate('BlogPost');
	}

/**
 * [MOBILE] ブログアーカイブを表示する
 *
 * @param mixed	blog_post_id / type
 * @param mixed	blog_post_id / ""
 * @return void
 */
	public function mobile_archives() {
		$this->setAction('archives');
	}

/**
 * [SMARTPHONE] ブログアーカイブを表示する
 *
 * @param mixed	blog_post_id / type
 * @param mixed	blog_post_id / ""
 * @return void
 */
	public function smartphone_archives() {
		$this->setAction('archives');
	}

/**
 * ブログカレンダー用のデータを取得する
 *
 * @param int $id
 * @param int $year
 * @param int $month
 * @return array
 */
	public function get_calendar($id, $year = '', $month = '') {
		$year = h($year);
		$month = h($month);
		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null, $id);
		$this->BlogPost->recursive = -1;
		$data['entryDates'] = $this->BlogPost->getEntryDates($id, $year, $month);

		if (!$year) {
			$year = date('Y');
		}
		if (!$month) {
			$month = date('m');
		}

		if ($month == 12) {
			$data['next'] = $this->BlogPost->existsEntry($id, $year + 1, 1);
		} else {
			$data['next'] = $this->BlogPost->existsEntry($id, $year, $month + 1);
		}
		if ($month == 1) {
			$data['prev'] = $this->BlogPost->existsEntry($id, $year - 1, 12);
		} else {
			$data['prev'] = $this->BlogPost->existsEntry($id, $year, $month - 1);
		}

		return $data;
	}

/**
 * カテゴリー一覧用のデータを取得する
 *
 * @param int $id
 * @param mixed $limit Number Or false Or '0'（制限なし）
 * @param mixed $viewCount
 * @param mixed $contentType year Or null
 * @return array
 */
	public function get_categories($id, $limit = false, $viewCount = false, $depth = 1, $contentType = null) {
		if ($limit === '0') {
			$limit = false;
		}
		$data = array();
		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null, $id);
		$data['categories'] = $this->BlogCategory->getCategoryList($id, array(
			'type' => $contentType,
			'limit' => $limit,
			'depth' => $depth,
			'viewCount' => $viewCount
		));
		return $data;
	}

/**
 * 投稿者一覧ウィジェット用のデータを取得する
 * 
 * @param int $blogContentId
 * @param boolean $limit
 * @param int $viewCount 
 */
	public function get_authors($blogContentId, $viewCount = false) {
		$data = array();
		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null, $blogContentId);
		$data['authors'] = $this->BlogPost->getAuthors($blogContentId, array(
			'viewCount' => $viewCount
		));
		return $data;
	}

/**
 * 月別アーカイブ一覧用のデータを取得する
 *
 * @param int $id
 * @return mixed $limit Number Or false Or '0'（制限なし）
 */
	public function get_posted_months($id, $limit = 12, $viewCount = false) {
		if ($limit === '0') {
			$limit = false;
		}
		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null, $id);
		$this->BlogPost->recursive = -1;
		$data['postedDates'] = $this->BlogPost->getPostedDates($id, array(
			'type' => 'month',
			'limit' => $limit,
			'viewCount' => $viewCount
		));
		return $data;
	}

/**
 * 年別アーカイブ一覧用のデータを取得する
 *
 * @param int $id
 * @param boolean $viewCount
 * @return mixed $count
 */
	public function get_posted_years($id, $limit = false, $viewCount = false) {
		if ($limit === '0') {
			$limit = false;
		}
		$this->BlogContent->recursive = -1;
		$data['blogContent'] = $this->BlogContent->read(null, $id);
		$this->BlogPost->recursive = -1;
		$data['postedDates'] = $this->BlogPost->getPostedDates($id, array(
			'type' => 'year',
			'limit' => $limit,
			'viewCount' => $viewCount
		));
		return $data;
	}

/**
 * 最近の投稿用のデータを取得する
 *
 * @param int $id
 * @param mixed $count
 * @return array
 */
	public function get_recent_entries($id, $limit = 5) {
		if ($limit === '0') {
			$limit = false;
		}
		$data['blogContent'] = $this->BlogContent->find('first', ['conditions' => ['BlogContent.id' => $id], 'recursive' => 0]);
		$conditions = array_merge(['BlogPost.blog_content_id' => $id], $this->BlogPost->getConditionAllowPublish());
		$this->BlogPost->unbindModel([
			'belongsTo' => ['BlogCategory', 'User'],
			'hasAndBelongsToMany' => ['BlogTag']
		]);
		$this->BlogPost->BlogContent->unbindModel([
			'hasMany' => ['BlogPost', 'BlogCategory']
		]);
		// 毎秒抽出条件が違うのでキャッシュしない
		$data['recentEntries'] = $this->BlogPost->find('all', [
			'conditions' => $conditions,
			'limit' => $limit,
			'order' => 'posts_date DESC',
			'recursive' => 2,
			'cache' => false
		]);
		return $data;
	}

/**
 * 記事リストを出力
 * requestAction用
 *
 * @param int $blogContentId
 * @param mixed $num
 */
	public function posts($blogContentId, $limit = 5) {
		if (!empty($this->params['named']['template'])) {
			$template = $this->request->params['named']['template'];
		} else {
			$template = 'posts';
		}
		unset($this->request->params['named']['template']);

		$this->layout = null;
		$this->contentId = $blogContentId;

		$datas = $this->_getBlogPosts(array('listCount' => $limit));

		$this->set('posts', $datas);

		$this->render($this->blogContent['BlogContent']['template'] . DS . $template);
	}

/**
 * [MOBILE] 記事リストを出力
 *
 * requestAction用
 *
 * @param int $blogContentId
 * @param mixed $num
 */
	public function mobile_posts($blogContentId, $limit = 5) {
		$this->setAction('posts', $blogContentId, $limit);
	}

/**
 * [SMARTPHONE] 記事リストを出力
 *
 * requestAction用
 *
 * @param int $blogContentId
 * @param mixed $num
 */
	public function smartphone_posts($blogContentId, $limit = 5) {
		$this->setAction('posts', $blogContentId, $limit);
	}

}
