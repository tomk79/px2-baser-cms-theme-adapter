<?php
/**
 * px2-baser-cms-theme-adapter
 */
namespace tomk79\pickles2\baserCmsThemeAdapter;

/**
 * px2-baser-cms-theme-adapter
 */
class stubs_BcBaser{
	/** Pickles 2 Object */
	private $px = '';

/**
 * サイト基本設定データ
 *
 * @var array
 */
	public $siteConfig = array();

/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = array('BcHtml', 'Js', 'Session', 'BcXml', 'BcArray', 'BcPage', 'Flash', 'BcContents');

/**
 * ページモデル
 *
 * 一度初期化した後に再利用し、処理速度を向上する為にコンストラクタでセットする。
 *
 * @var Page
 */
	protected $_Page = null;

/**
 * アクセス制限設定モデル
 *
 * 一度初期化した後に再利用し、処理速度を向上する為にコンストラクタでセットする。
 *
 * @var Permission
 */
	protected $_Permission = null;

/**
 * カテゴリタイトル設定
 *
 * パンくず用の配列を取得する際、カテゴリのタイトルを取得するかどうかの判定を保持
 *
 * @var mixed boolean or null
 */
	protected $_categoryTitleOn = true;

/**
 * カテゴリタイトル
 *
 * @var mixed boolean or string
 */
	protected $_categoryTitle = true;

/**
 * BcBaserHelper を拡張するプラグインのヘルパ
 *
 * BcBaserHelper::_initPluginBasers() で自動的に初期化される。
 *
 * @var array
 */
	protected $_pluginBasers = array();

	/**
	 * constructor
	 *
	 * @param object $px Pickles 2 Object
	 */
	public function __construct( $px ){
		$this->px = $px;
		$this->BcHtml = new stubs_BcHtml($px);
	} // __construct()


/**
 * タイトルを設定する
 *
 * @param string $title タイトル
 * @param mixed $categoryTitleOn カテゴリのタイトルを含むかどうか
 * @return void
 */
	public function setTitle($title, $categoryTitleOn = null) {
		if (!is_null($categoryTitleOn)) {
			$this->_categoryTitleOn = $categoryTitleOn;
		}
		$this->_View->pageTitle = $title;
	}

/**
 * meta タグのキーワードを設定する
 *
 * @param string $keywords キーワード（複数の場合はカンマで区切る）
 * @return void
 */
	public function setKeywords($keywords) {
		$this->_View->set('keywords', $keywords);
	}

/**
 * meta タグの説明文を設定する
 *
 * @param string $description 説明文
 * @return void
 */
	public function setDescription($description) {
		$this->_View->set('description', $description);
	}

/**
 * レイアウトで利用する為の変数を設定する
 *
 * View::set() のラッパー
 *
 * @param string $key 変数名
 * @param mixed $value 値
 * @return void
 */
	public function set($key, $value) {
		$this->_View->set($key, $value);
	}

/**
 * タイトルへのカテゴリタイトルの出力有無を設定する
 *
 * コンテンツごとに個別設定をする為に利用する。
 * パンくずにも影響する。
 *
 * @param bool|string|array $on true を指定した場合は、コントローラーで指定した crumbs を参照し、
 *		文字列を指定した場合には、その文字列をカテゴリとして利用する。
 *		パンくずにリンクをつける場合には、配列で指定する。
 *		（例） array('name' => '会社案内', 'url' => '/company/index')
 * @return void
 */
	public function setCategoryTitle($on = true) {
		$this->_categoryTitle = $on;
	}

/**
 * meta タグ用のキーワードを取得する
 *
 * @return string meta タグ用のキーワード
 */
	public function getKeywords() {
		$keywords = $this->_View->get('keywords');

		if (!empty($keywords)) {
			return $keywords;
		}

		if(!empty($this->siteConfig['keyword'])) {
			return $this->siteConfig['keyword'];
		}

		return '';
	}

/**
 * meta タグ用のページ説明文を取得する
 *
 * @return string meta タグ用の説明文
 */
	public function getDescription() {
		$description = $this->_View->get('description');

		if (!empty($description)) {
			return $description;
		}

		if(!empty($this->siteConfig['description'])) {
			return $this->siteConfig['description'];
		}

		return '';
	}

	/**
	 * タイトルタグを取得する
	 *
	 * ページタイトルと直属のカテゴリ名が同じ場合は、ページ名を省略する
	 * version 3.0.10 より第2引数 $categoryTitleOn は、 $options にまとめられました。
	 * 後方互換のために第2引数に配列型以外を指定された場合は、 $categoryTitleOn として取り扱います。
	 *
	 * @param string $separator 区切り文字
	 * @param array $options
	 *  `categoryTitleOn` カテゴリタイトルを表示するかどうか boolean で指定 (初期値 : null)
	 *  `tag` (boolean) false でタグを削除するかどうか (初期値 : true)
	 *  `allowableTags` tagが falseの場合、削除しないタグを指定できる。詳しくは、php strip_tags のドキュメントを参考してください。 (初期値 : '')
	 * @return string メタタグ用のタイトルを返す
	 */
	public function getTitle($separator = '｜', $options = array()) {
		$rtn = $this->px->site()->get_current_page_info('title_full');
		return $rtn;
	}

	/**
	 * パンくず用の配列を取得する
	 *
	 * 基本的には、コントローラーの crumbs プロパティで設定した値を取得する仕様だが
	 * 事前に setCategoryTitle メソッドで出力内容をカスタマイズする事ができる
	 *
	 * @param mixed $categoryTitleOn 親カテゴリの階層を表示するかどうか
	 * @return array パンくず用の配列
	 * @todo
	 * HTMLレンダリングも含めた状態で取得できる、HtmlHelper::getCrumbs() とメソッド名が
	 * 同じで、 処理内容がわかりにくいので変数名のリファクタリング要。
	 * ただし、BcBaserHelper::getCrumbs() は、テーマで利用されている可能性が高いので、
	 * 後方互換を考慮する必要がある。
	 */
	public function getCrumbs($categoryTitleOn = null) {
		$ary_breadcrumb = $this->px->site()->get_breadcrumb_array();
		return $ary_breadcrumb;
	}

	/**
	 * コンテンツタイトルを取得する
	 *
	 * @return string コンテンツタイトル
	 */
	public function getContentsTitle() {
		$rtn = $this->px->site()->get_current_page_info('title');
		return $rtn;
	}

	/**
	 * コンテンツのタイトルを出力する
	 *
	 * @return void
	 */
	public function contentsTitle() {
		echo $this->getContentsTitle();
	}

/**
 * タイトルタグを出力する
 *
 * @param string $separator 区切り文字
 * @param string $categoryTitleOn カテゴリを表示するかどうか boolean で指定
 * @return void
 */
	public function title($separator = '｜', $categoryTitleOn = null) {
		echo '<title>' . strip_tags($this->getTitle($separator, $categoryTitleOn)) . "</title>\n";
	}

/**
 * キーワード用のメタタグを出力する
 *
 * @return void
 */
	public function metaKeywords() {
		echo $this->BcHtml->meta('keywords', $this->getkeywords()) . "\n";
	}

/**
 * ページ説明文用のメタタグを出力する
 *
 * @return void
 */
	public function metaDescription() {
		echo $this->BcHtml->meta('description', strip_tags($this->getDescription())) . "\n";
	}

/**
 * RSSフィードのリンクタグを出力する
 *
 * @param string $title RSSのタイトル
 * @param string $link RSSのURL
 * @return void
 */
	public function rss($title, $link) {
		echo $this->BcHtml->meta($title, $link, array('type' => 'rss')) . "\n";
	}

/**
 * 現在のページがトップページかどうかを判定する
 *
 * MEMO: BcRequest.(agent).aliasは廃止
 *
 * @return bool
 */
	public function isHome() {
		if(empty($this->request->params['Site'])) {
			return false;
		}
		$site = BcSite::findCurrent(true);
		if (!$site->alias || $site->sameMainUrl || $site->useSubDomain) {
			return (
				$this->request->url == false ||
				$this->request->url == 'index'
			);
		} else {
			return (
				$this->request->url == $site->alias . '/' ||
				$this->request->url == $site->alias . '/index'
			);
		}
	}

/**
 * baserCMSが設置されているパスを出力する
 *
 * BcBaserHelper::getRoot() をラッピングして出力するだけの処理
 *
 * @return void
 */
	public function root() {
		echo $this->getRoot();
	}

/**
 * baserCMSが設置されているパスを取得する
 *
 * 画像タグやリンクタグを出力する際に、baserCMSの設置フォルダに
 * 依存せずパスを出力する為に利用する。
 *
 * 《利用例》
 * <img src="<?php echo $this->BcBaser->root() ?>img/test.png" />
 *
 * 《basercmsというフォルダに設置している場合の取得例》
 * /basercms/
 *
 * 《basercmsというフォルダに設置し、スマートURLオフの場合の取得例》
 * /basercms/index.php/
 *
 * @return string
 */
	public function getRoot() {
		return $this->request->base . '/';
	}

/**
 * baserCMSの設置フォルダを考慮したURLを出力する
 *
 * 《利用例》
 * <a href="<?php $this->BcBaser->getUrl('/about') ?>">会社概要</a>
 *
 * @param mixed $url baserCMS設置フォルダからの絶対URL、もしくは配列形式のURL情報
 *		省略した場合には、PC用のトップページのURLを出力する
 * @param bool $full httpから始まるURLを取得するかどうか
 * @param bool $sessionId セションIDを付加するかどうか
 * @return void
 */
	public function url($url = null, $full = false, $sessionId = true) {
		echo $this->getUrl($url, $full, $sessionId);
	}

/**
 * baserCMSの設置フォルダを考慮したURLを取得する
 *
 * 《利用例》
 * <a href="<?php echo $this->BcBaser->getUrl('/about') ?>">会社概要</a>
 *
 * @param mixed $url baserCMS設置フォルダからの絶対URL、もしくは配列形式のURL情報
 *		省略した場合には、PC用のトップページのURLを取得する
 * @param bool $full httpから始まるURLを取得するかどうか
 * @param bool $sessionId セションIDを付加するかどうか
 * @return string URL
 */
	public function getUrl($url = null, $full = false, $sessionId = true) {
		return parent::url($url, $full, $sessionId);
	}

	/**
	 * エレメントテンプレートのレンダリング結果を取得する
	 *
	 * @param string $name エレメント名
	 * @param array $data エレメントで参照するデータ
	 * @param array $options オプションのパラメータ
	 *  `subDir` (boolean) エレメントのパスについてプレフィックスによるサブディレクトリを追加するかどうか
	 * ※ その他のパラメータについては、View::element() を参照
	 * @return string エレメントのレンダリング結果
	 */
	public function getElement($name, $data = array(), $options = array()) {
		// TODO: エレメントテンプレートとは何？
		return '';
	}

/**
 * エレメントテンプレートを出力する
 *
 * @param string $name エレメント名
 * @param array $data エレメントで参照するデータ
 * @param array $options オプションのパラメータ
 *  `subDir` (boolean) エレメントのパスについてプレフィックスによるサブディレクトリを追加するかどうか
 * ※ その他のパラメータについては、View::element() を参照
 * @return void
 */
	public function element($name, $data = array(), $options = array()) {
		$options = array_merge(array(
			'subDir' => true
		), $options);
		echo $this->getElement($name, $data, $options);
	}

/**
 * ヘッダーテンプレートを出力する
 *
 * @param array $data エレメントで参照するデータ
 * @param array $options オプションのパラメータ
 *  `subDir` (boolean) エレメントのパスについてプレフィックスによるサブディレクトリを追加するかどうか
 * ※ その他のパラメータについては、View::element() を参照
 * @return void
 */
	public function header($data = array(), $options = array()) {
		$options = array_merge(array(
			'subDir' => true
		), $options);

		$out = $this->getElement('header', $data, $options);

		/*** header ***/
		$event = $this->dispatchEvent('header', array(
			'out' => $out
			), array('layer' => 'View', 'class' => '', 'plugin' => ''));
		if ($event !== false) {
			$out = ($event->result === null || $event->result === true) ? $event->data['out'] : $event->result;
		}

		/*** Controller.header ***/
		$event = $this->dispatchEvent('header', array(
			'out' => $out
			), array('layer' => 'View', 'class' => $this->_View->name));
		if ($event !== false) {
			$out = ($event->result === null || $event->result === true) ? $event->data['out'] : $event->result;
		}
		echo $out;
	}

/**
 * フッターテンプレートを出力する
 *
 * @param array $data エレメントで参照するデータ
 * @param array $options オプションのパラメータ
 *  `subDir` (boolean) エレメントのパスについてプレフィックスによるサブディレクトリを追加するかどうか
 * ※ その他のパラメータについては、View::element() を参照
 * @return void
 */
	public function footer($data = array(), $options = array()) {
		$options = array_merge(array(
			'subDir' => true
		), $options);

		$out = $this->getElement('footer', $data, $options);

		/*** footer ***/
		$event = $this->dispatchEvent('footer', array(
			'out' => $out
			), array('layer' => 'View', 'class' => '', 'plugin' => ''));
		if ($event) {
			$out = ($event->result === null || $event->result === true) ? $event->data['out'] : $event->result;
		}

		/*** Controller.footer ***/
		$event = $this->dispatchEvent('footer', array(
			'out' => $out
			), array('layer' => 'View', 'class' => $this->_View->name));
		if ($event) {
			$out = ($event->result === null || $event->result === true) ? $event->data['out'] : $event->result;
		}
		echo $out;
	}

/**
 * ページネーションを出力する
 *
 * @param string $name
 * @param array $data ページネーションで参照するデータ
 * @param array $options オプションのパラメータ
 *  `subDir` (boolean) エレメントのパスについてプレフィックスによるサブディレクトリを追加するかどうか
 * ※ その他のパラメータについては、View::element() を参照
 * @return void
 */
	public function pagination($name = 'default', $data = array(), $options = array()) {
		$options = array_merge(array(
			'subDir' => true
		), $options);

		if (!$name) {
			$name = 'default';
		}

		$file = 'paginations' . DS . $name;

		echo $this->getElement($file, $data, $options);
	}

/**
 * コンテンツ本体を出力する
 *
 * レイアウトテンプレートで利用する
 *
 * @return void
 */
	public function content() {
		/*** contentHeader ***/
		$this->dispatchEvent('contentHeader', null, array('layer' => 'View', 'class' => '', 'plugin' => ''));

		/*** Controller.contentHeader ***/
		$this->dispatchEvent('contentHeader', null, array('layer' => 'View', 'class' => $this->_View->name));

		echo $this->_View->fetch('content');

		/*** contentFooter ***/
		$event = $this->dispatchEvent('contentFooter', null, array('layer' => 'View', 'class' => '', 'plugin' => ''));

		/*** Controller.contentFooter ***/
		$event = $this->dispatchEvent('contentFooter', null, array('layer' => 'View', 'class' => $this->_View->name));
	}

/**
 * セッションに保存したメッセージを出力する
 *
 * メールフォームのエラーメッセージ等を出力します。
 *
 * @param string $key 出力するメッセージのキー（初期状態では省略してよいです）
 * @return void
 */
	public function flash($key = 'flash') {
		if ($this->Session->check('Message.' . $key)) {
			echo '<div id="MessageBox">';
			echo $this->Flash->render($key, ['escape' => false]);
			echo '</div>';
		}
	}

	/**
	 * コンテンツ内で設定した CSS や javascript をレイアウトテンプレートに出力し、ログイン中の場合、ツールバー用のCSSも出力する
	 * また、テーマ用のCSSが存在する場合には出力する
	 *
	 * 利用する際は、</head>タグの直前あたりに記述する。
	 * コンテンツ内で、レイアウトテンプレートへの出力を設定する場合には、inline オプションを false にする
	 *
	 * 《利用例》
	 * $this->BcBaser->css('admin/layout', array('inline' => false));
	 * $this->BcBaser->js('admin/startup', false);
	 *
	 * @return void
	 */
	public function scripts() {
		$scripts = $this->px->bowl()->get_clean('foot');
		echo $scripts;
	}

/**
 * ツールバーエレメントや CakePHP のデバッグ出力を表示
 *
 * 利用する際は、</body> タグの直前あたりに記述する。
 *
 * @return void
 */
	public function func() {

		$currentPrefix = $this->_View->get('currentPrefix');
		$authPrefix = Configure::read('BcAuthPrefix.' . $currentPrefix);
		$toolbar = true;
		if ($authPrefix && isset($authPrefix['toolbar'])) {
			$toolbar = $authPrefix['toolbar'];
		}

		// ### ツールバーエレメント出力
		// 《表示条件》
		// - プレビューでない
		// - auth prefix の設定で、利用するように定義されている
		// - モバイルでない
		// - Query String で、toolbar=false に定義されていない
		// - 管理画面でない
		// - ログインしている
		if (empty($this->_View->viewVars['preview']) && $toolbar && !@$this->request->params['Site']['device']) {
			if (!isset($this->request->query['toolbar']) || ($this->request->query['toolbar'] !== false && $this->request->query['toolbar'] !== 'false')) {
				if (empty($this->request->params['admin']) && !empty($this->_View->viewVars['user'])) {
					$this->element('admin/toolbar', array(), array('subDir' => false));
				}
			}
		}

		// デバッグ
		if (Configure::read('debug') >= 2) {
			$this->element('template_dump', array(), array('subDir' => false));
			$this->element('sql_dump', array(), array('subDir' => false));
		}
	}

/**
 * サブメニューを設定する（管理画面用）
 *
 * @param array $submenus サブメニューエレメント名を配列で指定
 * @return void
 */
	public function setSubMenus($submenus) {
		$this->_View->set('subMenuElements', $submenus);
	}

/**
 * XMLヘッダタグを出力する
 *
 * @param array $attrib 属性
 * @return void
 */
	public function xmlHeader($attrib = array()) {
		if (empty($attrib['encoding']) && @$this->request->params['Site']['device'] == 'mobile') {
			$attrib['encoding'] = 'Shift-JIS';
		}
		echo $this->BcXml->header($attrib) . "\n";
	}

/**
 * アイコン（favicon）タグを出力する
 *
 * @return void
 */
	public function icon() {
		echo $this->BcHtml->meta('icon') . "\n";
	}

/**
 * ドキュメントタイプを指定するタグを出力する
 *
 * @param string $type 出力ドキュメントタイプの文字列（初期値 : 'xhtml-trans'）
 * @return void
 */
	public function docType($type = 'xhtml-trans') {
		echo $this->BcHtml->docType($type) . "\n";
	}

/**
 * CSS タグを出力する
 *
 * 《利用例》
 * $this->BcBaser->css('admin/import')
 *
 * @param string $path CSSファイルのパス（css フォルダからの相対パス）拡張子は省略可
 * @param mixed $options オプション
 *	（配列の場合）
 *	- `rel` : rel属性（初期値 : 'stylesheet'）
 *	- `inline` : コンテンツ内にCSSを出力するかどうか（初期値 : true）
 *  ※ その他のパラメータについては、HtmlHelper::css() を参照。
 *	※ false を指定した場合、inline が false となる。
 * @return void
 */
	public function css($path, $options = array()) {
		if ($options === false) {
			$options['inline'] = false;
		}
		$options = array_merge(array(
			'rel' => 'stylesheet',
			'inline' => true
		), $options);
		$rel = $options['rel'];
		unset($options['rel']);
		$result = $this->BcHtml->css($path, $rel, $options);
		if ($options['inline']) {
			echo $result;
		}
	}

/**
 * Javascript タグを出力する
 *
 * @param string|array $url Javascriptのパス（js フォルダからの相対パス）拡張子は省略可
 * @param bool $inline コンテンツ内に Javascript を出力するかどうか（初期値 : true）
 * @return void
 */
	public function js($url, $inline = true, $options = []) {
		$options = array_merge(['inline' => $inline], $options);
		$result = $this->BcHtml->script($url, $options);
		if ($inline) {
			echo $result;
		}
	}

/**
 * 画像タグを出力する
 *
 * @param array $path 画像のパス（img フォルダからの相対パス）
 * @param array $options オプション（主にHTML属性）
 *	※ パラメータについては、HtmlHelper::image() を参照。
 * @return void
 */
	public function img($path, $options = array()) {
		echo $this->getImg($path, $options);
	}

/**
 * 画像タグを取得する
 *
 * @param string $path 画像のパス（img フォルダからの相対パス）
 * @param array $options オプション（主にHTML属性）
 * ※ パラメータについては、HtmlHelper::image() を参照。
 * @return string 画像タグ
 */
	public function getImg($path, $options = array()) {
		return $this->BcHtml->image($path, $options);
	}

/**
 * アンカータグを出力する
 *
 * @param string $title タイトル
 * @param mixed $url オプション（初期値 : null）
 * @param array $htmlAttributes オプション（初期値 : array()）
 *	- `escape` : タイトルをエスケープするかどうか（初期値 : false）
 *  - `prefix` : URLにプレフィックスをつけるかどうか（初期値 : false）
 *	- `forceTitle` : 許可されていないURLの際にタイトルを強制的に出力するかどうか（初期値 : false）
 *	- `ssl` : SSL用のURLをして出力するかどうか（初期値 : false）
 *	 ※ その他のパラメータについては、HtmlHelper::link() を参照。
 * @param bool $confirmMessage 確認メッセージ（初期値 : false）
 *	リンクをクリックした際に確認メッセージが表示され、はいをクリックした場合のみ遷移する
 * @return void
 */
	public function link($title, $url = null, $htmlAttributes = array(), $confirmMessage = false) {
		echo $this->getLink($title, $url, $htmlAttributes, $confirmMessage);
	}

/**
 * アンカータグを取得する
 *
 * @param string $title タイトル
 * @param mixed $url オプション（初期値 : null）
 * @param array $options オプション（初期値 : array()）
 *	- `escape` : タイトルをエスケープするかどうか（初期値 : false）
 *  - `prefix` : URLにプレフィックスをつけるかどうか（初期値 : false）
 *	- `forceTitle` : 許可されていないURLの際にタイトルを強制的に出力するかどうか（初期値 : false）
 *	- `ssl` : SSL用のURLをして出力するかどうか（初期値 : false）
 *	 ※ その他のパラメータについては、HtmlHelper::image() を参照。
 * @param bool $confirmMessage 確認メッセージ（初期値 : false）
 *	リンクをクリックした際に確認メッセージが表示され、はいをクリックした場合のみ遷移する
 * @return string
 */
	public function getLink($title, $url = null, $options = array(), $confirmMessage = false) {
		$adminAlias = Configure::read('BcAuthPrefix.admin.alias');

		if (!is_array($options)) {
			$options = array($options);
		}

		$options = array_merge(array(
			'escape' => false,
			'prefix' => false,
			'forceTitle' => false,
			'ssl' => false
			), $options);

		/*** beforeGetLink ***/
		$event = $this->dispatchEvent('beforeGetLink', array(
			'title' => $title,
			'url' => $url,
			'options' => $options,
			'confirmMessage' => $confirmMessage
			), array('class' => 'Html', 'plugin' => ''));
		if ($event !== false) {
			$options = ($event->result === null || $event->result === true) ? $event->data['options'] : $event->result;
		}

		if ($options['prefix']) {
			if (!empty($this->request->params['prefix']) && is_array($url)) {
				$url[$this->request->params['prefix']] = true;
			}
		}
		$forceTitle = $options['forceTitle'];
		$ssl = $options['ssl'];

		unset($options['prefix']);
		unset($options['forceTitle']);
		unset($options['ssl']);

		// 管理システムメニュー対策
		// プレフィックスが変更された場合も正常動作させる為
		// TODO メニューが廃止になったら削除
		if (!is_array($url)) {
			$prefixes = Configure::read('Routing.prefixes');
			$url = preg_replace('/^\/' . $adminAlias . '\//', '/' . $prefixes[0] . '/', $url);
		}

		$_url = $this->getUrl($url);
		$_url = preg_replace('/^' . preg_quote($this->request->base, '/') . '\//', '/', $_url);
		$enabled = true;

		if ($options == false) {
			$enabled = false;
		}

		// 認証チェック
		if (isset($this->_Permission) && !empty($this->_View->viewVars['user']['user_group_id'])) {
			$userGroupId = $this->_View->viewVars['user']['user_group_id'];
			if (!$this->_Permission->check($_url, $userGroupId)) {
				$enabled = false;
			}
		}

		// コンテンツ公開チェック
		// TODO 統合コンテンツ管理のチェックに変更する
//		if (isset($this->_Page) && empty($this->request->params['admin'])) {
//			$adminPrefix = Configure::read('Routing.prefixes.0');
//			if (isset($this->_Page) && !preg_match('/^\/' . $adminPrefix . '/', $_url)) {
//				if ($this->_Page->isPageUrl($_url) && !$this->_Page->checkPublish($_url)) {
//					$enabled = false;
//				}
//			}
//		}

		if (!$enabled) {
			if ($forceTitle) {
				return "<span>$title</span>";
			} else {
				return '';
			}
		}

		// 現在SSLのURLの場合、プロトコル指定(フルパス)で取得以外
		// //(スラッシュスラッシュ)から始まるSSL、非SSL共有URLも除外する
		if (($this->isSSL() || $ssl)
			&& !(preg_match('/^(javascript|https?|ftp|tel):/', $_url))
			&& !(strpos($_url, '//') === 0)) {

			$_url = preg_replace("/^\//", "", $_url);
			if (preg_match('/^' . $adminAlias . '\//', $_url)) {
				$admin = true;
			} else {
				$admin = false;
			}
			if (Configure::read('App.baseUrl')) {
				$_url = 'index.php/' . $_url;
			}
			if (!$ssl && !$admin) {
				$url = Configure::read('BcEnv.siteUrl') . $_url;
			} else {
				$sslUrl = Configure::read('BcEnv.sslUrl');
				if($sslUrl) {
					$url = $sslUrl . $_url;
				} else {
					$url = '/' . $_url;
				}
			}
		}

		if (!$options) {
			$options = array();
		}

		$out = $this->BcHtml->link($title, $url, $options, $confirmMessage);

		/*** afterGetLink ***/
		$event = $this->dispatchEvent('afterGetLink', array(
			'url' => $url,
			'out' => $out
			), array('class' => 'Html', 'plugin' => ''));
		if ($event !== false) {
			$out = ($event->result === null || $event->result === true) ? $event->data['out'] : $event->result;
		}

		return $out;
	}

/**
 * SSL通信かどうか判定する
 *
 * @return bool
 */
	public function isSSL() {
		return $this->request->is('ssl');
	}

/**
 * charset メタタグを出力する
 *
 * モバイルの場合は、強制的に文字コードを Shift-JIS に設定
 *
 * @param string $charset 文字コード（初期値 : null）
 * @return void
 */
	public function charset($charset = null) {
		if (!$charset && @$this->request->params['Site']['device'] == 'mobile') {
			$charset = 'Shift-JIS';
		}
		echo $this->BcHtml->charset($charset);
	}

/**
 * コピーライト用の年を出力する
 *
 * 《利用例》
 * $this->BcBaser->copyYear(2012)
 *
 * 《出力例》
 * 2012 - 2014
 *
 * @param int $begin 開始年
 * @return void
 */
	public function copyYear($begin) {
		$year = date('Y');
		if ($begin == $year || !is_numeric($begin)) {
			echo $year;
			return;
		}
		echo $begin . ' - ' . $year;
	}

/**
 * 編集画面へのリンクを設定する
 *
 * @param string $id 固定ページID
 * @return void
 */
	public function setPageEditLink($id) {
		if (empty($this->request->params['admin']) && !empty($this->_View->viewVars['user'])) {
			$this->_View->viewVars['editLink'] = array('admin' => true, 'controller' => 'pages', 'action' => 'edit', $id);
		}
	}

/**
 * 編集画面へのリンクを出力する
 *
 * @return void
 */
	public function editLink() {
		if ($this->existsEditLink()) {
			$this->link('編集する', $this->_View->viewVars['editLink'], array('class' => 'tool-menu'));
		}
	}

/**
 * 編集画面へのリンクが存在するかチェックする
 *
 * @return bool 存在する場合は true を返す
 */
	public function existsEditLink() {
		if (empty($this->_View->viewVars['currentUserAuthPrefixes'])) return false;
		if (empty($this->_View->viewVars['editLink'])) return false;
		foreach($this->_View->viewVars['currentUserAuthPrefixes'] as $currentPrefix) {
			if (Configure::read('Routing.prefixes.0') == $currentPrefix) return true;
			if (Configure::read('Routing.prefixes.0') == Configure::read('BcAuthPrefix.' . $currentPrefix . '.alias')) return true;
		}
		return false;
	}

/**
 * 公開ページへのリンクを出力する
 *
 * 管理システムで利用する
 *
 * @return void
 */
	public function publishLink() {
		if ($this->existsPublishLink()) {
			$site = BcSite::findByUrl($this->_View->viewVars['publishLink']);
			$useSubdomain = $fullUrl = false;
			if($site && $site->name) {
				$useSubdomain = $site->useSubDomain;
				$fullUrl = true;
			}
			$url = $this->BcContents->getUrl($this->_View->viewVars['publishLink'], $fullUrl, $useSubdomain);
			$this->link('公開ページ', $url, array('class' => 'tool-menu'));
		}
	}

/**
 * 公開ページへのリンクが存在するかチェックする
 *
 * @return bool リンクが存在する場合は true を返す
 */
	public function existsPublishLink() {
		if (empty($this->_View->viewVars['currentUserAuthPrefixes'])) return false;
		if (empty($this->_View->viewVars['publishLink'])) return false;
		foreach($this->_View->viewVars['currentUserAuthPrefixes'] as $currentPrefix) {
			if (Configure::read('Routing.prefixes.0') == $currentPrefix) return true;
			if (Configure::read('Routing.prefixes.0') == Configure::read('BcAuthPrefix.' . $currentPrefix . '.alias')) return true;
		}
		return false;
	}

/**
 * アップデート処理が必要かチェックする
 *
 * @return bool アップデートが必要な場合は true を返す
 * @todo 別のヘルパに移動する
 */
	public function checkUpdate() {
		$baserVerpoint = verpoint($this->_View->get('baserVersion'));
		if ($baserVerpoint === false) {
			return false;
		}

		if (!isset($this->siteConfig['version'])) {
			return $baserVerpoint > 0;
		}

		$siteVerpoint = verpoint($this->siteConfig['version']);

		return $siteVerpoint !== false && $baserVerpoint > $siteVerpoint;
	}

/**
 * コンテンツを特定する文字列を出力する
 *
 * URL を元に、第一階層までの文字列をキャメルケースで取得する
 * ※ 利用例、出力例については BcBaserHelper::getContentsName() を参照
 *
 * @param bool $detail 詳細モード true にした場合は、ページごとに一意となる文字列をキャメルケースで出力する（初期値 : false）
 * @param array $options オプション（初期値 : array()）
 *	※ オプションの詳細については、BcBaserHelper::getContentsName() を参照
 * @return void
 */
	public function contentsName($detail = false, $options = array()) {
		echo $this->getContentsName($detail, $options);
	}

/**
 * コンテンツを特定する文字列を取得する
 *
 * URL を元に、第一階層までの文字列をキャメルケースで取得する
 *
 * 《利用例》
 * $this->BcBaser->contentsName()
 *
 * 《出力例》
 * - トップページの場合 : Home
 * - about ページの場合 : About
 *
 * @param bool $detail 詳細モード true にした場合は、ページごとに一意となる文字列をキャメルケースで取得する（初期値 : false）
 * @param array $options オプション（初期値 : array()）
 *	- `home` : トップページの場合に出力する文字列（初期値 : Home）
 *	- `default` : ルート直下の下層ページの場合に出力する文字列（初期値 : Default）
 *	- `error` : エラーページの場合に出力する文字列（初期値 : Error）
 *  - `underscore` : キャメルケースではなく、アンダースコア区切りで出力する（初期値 : false）
 * @return string
 */
	public function getContentsName($detail = false, $options = array()) {
		$rtn = $this->px->site()->get_current_page_info('id');
		return $rtn;
	}

	public function getSitePrefix() {
		$site = null;
		if($this->request->params['Site']) {
			$site = $this->request->params['Site'];
		}
		$Site = ClassRegistry::init('Site');
		return $Site->getPrefix($site);
	}

/**
 * パンくずリストを出力する
 *
 * 事前に BcBaserHelper::addCrumb() にて、パンくず情報を追加しておく必要がある。
 * また、アクセス制限がかかっているリンクはテキストのみ表示する
 *
 * @param string $separator パンくずの区切り文字（初期値 : &raquo;）
 * @param string|bool $startText トップページを先頭に追加する場合にはトップページのテキストを指定する（初期値 : false）
 * @return void
 */
	public function crumbs($separator = '&raquo;', $startText = false, $onSchema = false) {
		$crumbs = $this->BcHtml->getStripCrumbs();
		if (empty($crumbs)) {
			return;
		}
		if ($startText) {
			$homeUrl = '/';
			if(!empty($this->request->params['Site']['alias'])) {
				$homeUrl = '/' . $this->request->params['Site']['alias'] . '/';
			} elseif(!empty($this->request->params['Site']['name'])) {
				$homeUrl = '/' . $this->request->params['Site']['name'] . '/';
			}
			array_unshift($crumbs, [
				0 => $startText,
				1 => $homeUrl
			]);
		}

		$out = array();
		if(!$onSchema) {
			foreach ($crumbs as $crumb) {
				if (!empty($crumb[1])) {
					$out[] = $this->getLink($crumb[0], $crumb[1], @$crumb[2]);
				} else {
					$out[] = $crumb[0];
				}
			}
			$out = implode($separator, $out);
		} else {
			$counter = 1;
			foreach ($crumbs as $crumb) {
				$options = ['itemprop' => 'item'];
				if(!empty($crumb[2])) {
					$options = array_merge($options, $crumb[2]);
				}
				if (!empty($crumb[1])) {
					$crumb = $this->getLink('<span itemprop="name">' . $crumb[0] . '</span>', $crumb[1], $options) . '<span class="separator">' . $separator . '</span>';
				} else {
					$crumb = '<span itemprop="name">' . $crumb[0] . '</span>';
				}
				$out[] = <<< EOD
<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
	{$crumb}
	<meta itemprop="position" content="{$counter}" />
</li>
EOD;
				$counter++;
			}
			$out = implode("\n", $out);
		}
		echo $out;
	}

/**
 * パンくずリストの要素を追加する
 *
 * デフォルトでアクセス制限がかかっているリンクの場合でもタイトルを表示する
 * $options の forceTitle キー に false を指定する事で表示しない設定も可能
 *
 * @param string $name パンくず用のテキスト
 * @param string $link パンくず用のリンク（初期値 : null）※ 指定しない場合はリンクは設定しない
 * @param mixed $options リンクタグ用の属性（初期値 : array()）
 * ※ パラメータについては、HtmlHelper::link() を参照。
 * @return void
 */
	public function addCrumb($name, $link = null, $options = array()) {
		$options = array_merge(array(
			'forceTitle' => true
		), $options);
		$this->BcHtml->addCrumb($name, $link, $options);
	}

/**
 * ブラウザにキャッシュさせる為のヘッダーを出力する
 *
 * @param string|int|float $expire キャッシュの有効期間（初期値 : null） ※ 指定しない場合は、baserCMSコアのキャッシュ設定値
 * @param string $type どのタイプ(拡張子)に対してのキャッシュか（初期値 : 'html'）
 * @return void
 */
	public function cacheHeader($expire = null, $type = 'html') {
		$contentType = array(
			'html' => 'text/html',
			'js' => 'text/javascript', 'css' => 'text/css',
			'gif' => 'image/gif', 'jpg' => 'image/jpeg', 'png' => 'image/png'
		);
		$fileModified = filemtime(WWW_ROOT . 'index.php');

		if (!$expire) {
			$expire = Configure::read('BcCache.duration');
		}
		if (!is_numeric($expire)) {
			$expire = strtotime($expire);
		}
		header("Date: " . date("D, j M Y G:i:s ", $fileModified) . 'GMT');
		header("Last-Modified: " . gmdate("D, d M Y H:i:s", $fileModified) . " GMT");
		header('Content-type: ' . $contentType[$type]);
		header("Expires: " . gmdate("D, j M Y H:i:s", time() + $expire) . " GMT");
		header('Cache-Control: max-age=' . $expire);
		// Firefoxの場合は不要↓
		//header("Cache-Control: cache");
		header("Pragma: cache");
	}

/**
 * プロトコルから始まるURLを取得する
 *
 * 《利用例》
 * $this->BcBaser->getUri('/about')
 *
 * 《出力例》
 * http://localhost/about
 *
 * @param mixed $url 文字列のURL、または、配列形式のURL
 * @param bool $sessionId セッションIDを付加するかどうか（初期値 : true）
 * @return string プロトコルから始まるURL
 */
	public function getUri($url, $sessionId = true) {
		if (is_string($url) && preg_match('/^http/is', $url)) {
			return $url;
		}
		if (empty($_SERVER['HTTPS'])) {
			$protocol = 'http';
		} else {
			$protocol = 'https';
		}
		return $protocol . '://' . Configure::read('BcEnv.host') . $this->getUrl($url, false, $sessionId);
	}

/**
 * PluginBaserHelper を初期化する
 *
 * BcBaserHelperに定義されていないメソッドをプラグイン内のヘルパに定義する事で
 * BcBaserHelperから呼び出せるようになる仕組みを提供する。
 * プラグインのヘルパメソッドを BcBaserHelper 経由で直接呼び出せる為、
 * コア側のコントローラーでいちいちヘルパの定義をしなくてよくなり、
 * プラグインを導入するだけでテンプレート上でプラグインのメソッドが呼び出せるようになる。
 * 例えば固定ページ機能のWYSIWYG内にプラグインのメソッドを書き込む事ができる。
 *
 * 《PluginBaserHelper の命名規則》
 * {プラグイン名}BaserHelper
 *
 * 《利用例》
 * - Feedプラグインに FeedBaserHelper::feed() が定義されている場合
 *		$this->BcBaser->feed(1);
 *
 * @return void
 */
	protected function _initPluginBasers() {
		$plugins = Configure::read('BcStatus.enablePlugins');
		if (!$plugins) {
			return;
		}
		foreach ($plugins as $plugin) {
			$pluginName = Inflector::camelize($plugin);
			if (App::import('Helper', $pluginName . '.' . $pluginName . 'Baser')) {
				$pluginBaser = $pluginName . 'BaserHelper';
				$this->_pluginBasers[$pluginName] = new $pluginBaser($this->_View);
			}
		}
	}

/**
 * PluginBaserHelper 用マジックメソッド
 *
 * BcBaserHelper に存在しないメソッドが呼ばれた際、プラグインで定義された PluginBaserHelper のメソッドを呼び出す
 * call__ から __call へメソット名を変更、Helper の __call をオーバーライド
 *
 * @param string $method メソッド名
 * @param array $params 引数
 * @return mixed PluginBaserHelper の戻り値
 */
	public function __call($method, $params) {
		foreach ($this->_pluginBasers as $pluginBaser) {
			if (method_exists($pluginBaser, $method)) {
				return call_user_func_array(array($pluginBaser, $method), $params);
			}
		}
		return null;
	}

/**
 * 文字列を検索しマークとしてタグをつける
 *
 * 《利用例》
 * $this->BcBaser->mark('強調', '強調します強調します強調します')
 *
 * 《取得例》
 * <strong>強調</strong>します<strong>強調</strong>します<strong>強調</strong>します
 *
 * @param string $search 検索文字列
 * @param string $text 検索対象文字列
 * @param string $name マーク用タグ（初期値 : strong）
 * @param array $attributes タグの属性（初期値 : array()）
 * @param bool $escape エスケープ有無（初期値 : false）
 * @return string $text 変換後文字列
 * @todo TextHelperに移行を検討
 */
	public function mark($search, $text, $name = 'strong', $attributes = array(), $escape = false) {
		if (!is_array($search)) {
			$search = array($search);
		}

		$options = array(
			'escape' => $escape
		);

		if (!empty($attributes)) {
			$options = array_merge($options, $attributes);
		}


		foreach ($search as $value) {
			$text = str_replace($value, $this->BcHtml->tag($name, $value, $options), $text);
		}
		return $text;
	}

/**
 * コンテンツメニューを出力する
 *
 * ログインしていない場合はキャッシュする
 * contents_menu エレメントで、HTMLカスタマイズ可能
 *
 * @param mixed $id コンテンツID（初期値：null）
 * @param int $level 階層（初期値：null）※ null の場合は階層指定なし
 * @param string $currentId 現在のページのコンテンツID（初期値：null）
 * @return string コンテンツメニュー
 */
	public function contentsMenu($id = null, $level = null, $currentId = null) {
		echo $this->getContentsMenu($id, $level, $currentId);
	}

/**
 * メニューを出力する
 *
 * ログインしていない場合はキャッシュする
 * contents_menu エレメントで、HTMLカスタマイズ可能
 *
 * @param mixed $id コンテンツID（初期値：null）
 * @param int $level 階層（初期値：null）※ null の場合は階層指定なし
 * @param string $currentId 現在のページのコンテンツID（初期値：null）
 * @return string コンテンツメニュー
 */
	public function getContentsMenu($id = null, $level = null, $currentId = null) {
		if(!$id) {
			$Content = ClassRegistry::init('Content');
			$siteRoot = $Content->getSiteRoot($this->request->params['Content']['site_id']);
			$id = $siteRoot['Content']['id'];
		}
		$params = [
			'tree' => $this->BcContents->getTree($id, $level),
			'currentId' => $currentId
		];
		$params['tree'] = $this->_unsetIndexInContentsMenu($params['tree']);
		if (empty($_SESSION['Auth'][Configure::read('BcAuthPrefix.admin.sessionKey')])) {
			$params = array_merge($params, [
					'cache' => [
						'time' => Configure::read('BcCache.duration'),
						'key' => $id]]
			);
		}
		return $this->getElement('contents_menu', $params);
	}

/**
 * コンテンツメニューにおいてフォルダ内の index ページを除外する
 *
 * @param array $contents コンテンツデータ
 * @param bool $children 子かどうか
 * @return mixed コンテンツデータ
 */
	public function _unsetIndexInContentsMenu($contents, $children = false) {
		if($contents) {
			foreach($contents as $key => $content) {
				if($children && $content['Content']['type'] != 'ContentFolder' && $content['Content']['name'] == 'index') {
					unset($contents[$key]);
				}
				if($content['children']) {
					$contents[$key]['children'] = $this->_unsetIndexInContentsMenu($content['children'], true);
				}
			}
		}
		return $contents;
	}

/**
 * グローバルメニューを出力する
 *
 * @param array $level 取得する階層（初期値 : 1）
 * @param array $options オプション（初期値 : array()）
 *	※ その他のパラメータについては、View::element() を参照
 * @return void
 */
	public function globalMenu($level = 1, $options = array()) {
		echo $this->getGlobalMenu($level, $options);
	}

/**
 * グローバルメニューを取得する
 *
 * @param array $level 取得する階層（初期値 : 1）
 * @param array $options オプション（初期値 : array()）
 *	※ その他のパラメータについては、View::element() を参照
 * @return string
 */
	public function getGlobalMenu($level = 1, $options = array()) {
		$Content = ClassRegistry::init('Content');
		$siteId = 0;
		if(!empty($this->request->params['Content']['site_id'])) {
			$siteId = $this->request->params['Content']['site_id'];
		}
		$siteRoot = $Content->getSiteRoot($siteId);
		$id = $siteRoot['Content']['id'];
		$currentId = null;
		if(!empty($this->request->params['Content']['id'])) {
			$currentId = $this->request->params['Content']['id'];
		}
		$options = array_merge([
			'tree' => $this->BcContents->getTree($id, $level),
			'currentId' => $currentId,
			'data' => []
		], $options);
		if (empty($_SESSION['Auth'][Configure::read('BcAuthPrefix.admin.sessionKey')])) {
			$options = array_merge($options, [
					'cache' => [
						'time' => Configure::read('BcCache.duration'),
						'key' => $id]]
			);
		}
		$data = array_merge([
			'tree' => $options['tree'],
			'currentId' => $options['currentId']
		], $options['data']);
		unset($options['tree'], $options['currentId'], $options['data']);
		return $this->getElement('global_menu', $data, $options);
	}
/**
 * サイトマップを出力する
 *
 * ログインしていない場合はキャッシュする
 *
 * @param int $siteId サイトID
 */
	public function sitemap($siteId = 0) {
		echo $this->getSitemap($siteId);
	}

/**
 * サイトマップを取得する
 *
 * ログインしていない場合はキャッシュする
 *
 * @param int $siteId サイトID
 * @return string サイトマップ
 */
	public function getSitemap($siteId = 0) {
		$Site = ClassRegistry::init('Site');
		$contentId = $Site->getRootContentId($siteId);
		return $this->getContentsMenu($contentId);
	}

/**
 * Flashを表示する
 *
 * @param string $path Flashのパス
 * @param string $id 任意のID（divにも埋め込まれる）
 * @param int $width 横幅
 * @param int $height 高さ
 * @param array $options オプション（初期値 : array()）
 *	- `version` : Flashのバージョン（初期値 : 7）
 *	- `script` : Flashを読み込むJavascriptのパス（初期値 : admin/swfobject-2.2）
 *	- `noflash` : Flashがインストールされてない場合に表示する文字列
 * @return string Flash表示タグ
 */
	public function swf($path, $id, $width, $height, $options = array()) {
		$options = array_merge(array(
			'version' => 7,
			'script' => 'admin/swfobject-2.2',
			'noflash' => '&nbsp;'
			), $options);

		$version = $options['version'];
		$script = $options['script'];
		$noflash = $options['noflash'];

		if (!preg_match('/\.swf$/', $path)) {
			$path .= '.swf';
		}

		if (is_array($path)) {
			$path = $this->getUrl($path);
		} elseif (strpos($path, '://') === false) {
			if ($path[0] !== '/') {
				$path = Configure::read('App.imageBaseUrl') . $path;
			}
			$path = $this->webroot($path);
		}
		$out = $this->js($script, true) . "\n";
		$out = <<< END_FLASH
<div id="{$id}">{$noflash}</div>
<script type="text/javascript">
	swfobject.embedSWF("{$path}", "{$id}", "{$width}", "{$height}", "{$version}");
</script>
END_FLASH;

		echo $out;
	}

/**
 * 管理者グループかどうかチェックする
 *
 * @param int $userGroupId ユーザーグループID（初期値 : null）※ 指定しない場合は、現在のログインユーザーについてチェックする
 * @return bool 管理者グループの場合は true を返す
 */
	public function isAdminUser($userGroupId = null) {
		if (!$userGroupId) {
			return BcUtil::isAdminUser();
		}
		if ($userGroupId == Configure::read('BcApp.adminGroupId')) {
			return true;
		} else {
			return false;
		}
	}

/**
 * 現在のページが固定ページかどうかを判定する
 *
 * @return bool 固定ページの場合は true を返す
 */
	public function isPage() {
		return ($this->request->params['controller'] == 'pages' && $this->request->params['action'] == 'display');
	}

/**
 * 現在のページの純粋なURLを取得する
 *
 * スマートURL、サブフォルダかどうかに依存しない、スラッシュから始まるURLを取得
 *
 * @return string URL
 */
	public function getHere() {
		return '/' . preg_replace('/^\//', '', $this->request->url);
	}

/**
 * 現在のページがページカテゴリのトップかどうかを判定する
 * 判定は、URLからのみで行う
 *
 * @return bool カテゴリトップの場合は、 true を返す
 */
	public function isCategoryTop() {
		$url = $this->getHere();
		$url = preg_replace('/^\//', '', $url);
		if (preg_match('/\/$/', $url)) {
			$url .= 'index';
		}
		if (preg_match('/\/index$/', $url)) {
			$param = explode('/', $url);
			if (count($param) >= 2) {
				return true;
			}
		}
		return false;
	}

/**
 * 固定ページをエレメントとして読み込む
 *
 * ※ レイアウトは読み込まずコンテンツ本体のみを読み込む
 *
 * @param string $url 固定ページのURL
 * @param array $params 固定ページに引き継ぐパラメータ（初期値 : array()）
 * @param array $options オプション（初期値 : array()）
 *	- `loadHelpers` : ヘルパーを読み込むかどうか（初期値 : false）
 * todo loadHelpersが利用されていないのをなんとかする
 *	- `subDir` : テンプレートの配置場所についてプレフィックスに応じたサブフォルダを利用するかどうか（初期値 : true）
 *	- `recursive` : 固定ページ読み込みを再帰的に読み込むかどうか（初期値 : true）
 * @return void
 */
	public function page($url, $params = array(), $options = array()) {
		if (isset($this->_View->viewVars['pageRecursive']) && !$this->_View->viewVars['pageRecursive']) {
			return;
		}

		$options = array_merge(array(
			'loadHelpers' => false,
			'subDir' => true,
			'recursive' => true
		), $options);

		$subDir = $options['subDir'];
		$recursive = $options['recursive'];

		$this->_View->viewVars['pageRecursive'] = $recursive;

		// 現在のページの情報を退避
		$editLink = null;
		$description = $this->getDescription();
		$title = $this->getContentsTitle();
		if (!empty($this->_View->viewVars['editLink'])) {
			$editLink = $this->_View->viewVars['editLink'];
		}

		// urlを取得
		if (empty($this->_View->subDir)) {
			$url = '/../Pages' . $url;
		} else {
			$dirArr = explode('/', $this->_View->subDir);
			$url = str_repeat('/..', count($dirArr)) . '/../Pages' . $url;
		}

		$this->element($url, $params, array('subDir' => $subDir));

		// 現在のページの情報に戻す
		$this->setDescription($description);
		$this->setTitle($title);
		if ($editLink) {
			$this->_View->viewVars['editLink'] = $editLink;
		}
	}

/**
 * ウィジェットエリアを出力する
 *
 * @param int $no ウィジェットエリアNO（初期値 : null）※ 省略した場合は、コンテンツごとに管理システムにて設定されているウィジェットエリアを出力する
 * @param array $options オプション（初期値 : array()）
 *	- `loadHelpers` : ヘルパーを読み込むかどうか（初期値 : false）
 * todo loadHelpersが利用されていないのをなんとかする
 *	- `subDir` : テンプレートの配置場所についてプレフィックスに応じたサブフォルダを利用するかどうか（初期値 : true）
 * @return void
 */
	public function widgetArea($no = null, $options = array()) {
		echo $this->getWidgetArea($no, $options);
	}

/**
 * ウィジェットエリアを取得する
 *
 * @param int $no ウィジェットエリアNO（初期値 : null）※ 省略した場合は、コンテンツごとに管理システムにて設定されているウィジェットエリアを出力する
 * @param array $options オプション（初期値 : array()）
 *	- `loadHelpers` : ヘルパーを読み込むかどうか（初期値 : false）
 * todo loadHelpersが利用されていないのをなんとかする
 *	- `subDir` : テンプレートの配置場所についてプレフィックスに応じたサブフォルダを利用するかどうか（初期値 : true）
 * @return string
 */
	public function getWidgetArea($no = null, $options = array()) {
		$options = array_merge(array(
			'loadHelpers'	=> false,
			'subDir'		=> true,
		), $options);

		$subDir = $options['subDir'];

		if (!$no && isset($this->_View->viewVars['widgetArea'])) {
			$no = $this->_View->viewVars['widgetArea'];
		}
		if ($no) {
			return $this->getElement('widget_area', array('no' => $no, 'subDir' => $subDir), array('subDir' => $subDir));
		}
		return '';
	}

/**
 * 指定したURLが現在のURLと同じかどうか判定する
 *
 * 《比較例》
 * /news/ | /news/ ・・・○
 * /news | /news/ ・・・×
 * /news/ | /news/index ・・・○
 *
 * @param string $url 比較対象URL
 * @return bool 同じ場合には true を返す
 */
	public function isCurrentUrl($url) {
		$pattern = '/\/$/';
		$shortenedUrl = preg_replace($pattern, '/index', $this->getUrl($url));
		$shortenedHere = preg_replace($pattern, '/index', $this->request->here);
		return ($shortenedUrl === $shortenedHere);
	}

/**
 * ユーザー名を整形して取得する
 *
 * 姓と名を結合して取得
 * ニックネームがある場合にはニックネームを優先する
 *
 * @param array $user ユーザーデータ
 * @return string $userName ユーザー名
 */
	public function getUserName($user) {
		if (isset($user['User'])) {
			$user = $user['User'];
		}

		if (!empty($user['nickname'])) {
			return $user['nickname'];
		}

		$userName = array();
		if (!empty($user['real_name_1'])) {
			$userName[] = $user['real_name_1'];
		}
		if (!empty($user['real_name_2'])) {
			$userName[] = $user['real_name_2'];
		}
		$userName = implode(' ', $userName);

		return $userName;
	}

/**
 * baserCMSのコアテンプレートを読み込む
 *
 * コントローラー名より指定が必要
 *
 * 《利用例》
 * $this->BcBaser->includeCore('Users/admin/form')
 * $this->BcBaser->includeCore('Mail.MailFields/admin/form')
 *
 * @param string $name テンプレート名
 * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
 * @param array $options オプション（初期値 : array()）
 *	- `subDir` : テンプレートの配置場所についてプレフィックスに応じたサブフォルダを利用するかどうか（初期値 : true）
 *	※ その他のパラメータについては、View::element() を参照
 * @return void
 */
	public function includeCore($name, $data = array(), $options = array()) {
		$options = array_merge($options, array(
			'subDir' => false
		));
		$plugin = '';
		if (strpos($name, '.') !== false) {
			list($plugin, $name) = explode('.', $name);
			$plugin = Inflector::camelize($plugin);
			$name = '../../../lib/Baser/Plugin/' . $plugin . '/View/' . $name;
		} else {
			$name = '../../../lib/Baser/View/' . $name;
		}

		$this->element($name, $data, $options);
	}

/**
 * ロゴを出力する
 *
 * @param array $options オプション（初期値 : array()）
 *	※ パラメーターは、 BcBaserHelper->_getThemeImage() を参照
 * @return void
 */
	public function logo($options = array()) {
		echo $this->_getThemeImage('logo', $options);
	}

/**
 * メインイメージを出力する
 *
 * メインイメージは管理画面のテーマ設定にて指定
 *
 * @param array $options オプション
 *	- `all`: 全ての画像を出力する。
 *	- `num`: 指定した番号の画像を出力する。all を true とした場合は、出力する枚数となる。
 *	- `id` : all を true とした場合、UL タグの id 属性を指定できる。
 *	- `class` : all を true とした場合、UL タグの class 属性を指定できる。
 *	※ その他の、パラメーターは、 BcBaserHelper->_getThemeImage() を参照
 * @return void
 */
	public function mainImage($options = array()) {
		$options = array_merge(array(
			'num' => 1,
			'all' => false,
			'id' => 'MainImage',
			'class' => false
			), $options);
		if ($options['all']) {
			$id = $options['id'];
			$class = $options['class'];
			$num = $options['num'];
			unset($options['all']);
			unset($options['id']);
			unset($options['class']);
			$tag = '';
			for ($i = 1; $i <= $num; $i++) {
				$options['num'] = $i;
				$themeImage = $this->_getThemeImage('main_image', $options);
				if ($themeImage) {
					$tag .= '<li>' . $themeImage . '</li>' . "\n";
				}
			}
			$ulAttr = '';
			if ($id !== false) {
				$ulAttr .= ' id="' . $id . '"';
			}
			if ($class !== false) {
				$ulAttr .= ' class="' . $class . '"';
			}
			echo '<ul' . $ulAttr . '>' . "\n" . $tag . "\n" . '</ul>';
		} else {
			echo $this->_getThemeImage('main_image', $options);
		}
	}

/**
 * テーマ画像を取得する
 *
 * @param string $name テーマ画像名（ log or main_image ）
 * @param array $options オプション（初期値 :array()）
 *	- `num` : main_imageの場合の番号指定（初期値 : ''）
 *	- `thumb`: サムネイルを取得する（初期値 : false）
 *	- `class`: 画像に設定する class 属性（初期値 : ''）
 *	- `popup`: ポップアップリンクを指定（初期値 : false）
 *	- `alt`	: 画像に設定する alt 属性。リンクの title 属性にも設定される。（初期値 : テーマ設定で設定された値）
 *	- `link`	: リンク先URL。popup を true とした場合、オリジナルの画像へのリンクとなる。（初期値 : テーマ設定で設定された値）
 *	- `maxWidth : 最大横幅（初期値 : ''）
 *	- `maxHeight: 最大高さ（初期値 : ''）
 *	- `width : 最大横幅（初期値 : ''）
 *	- `height: 最大高さ（初期値 : ''）
 * @return string $tag テーマ画像のHTMLタグ
 */
	protected function _getThemeImage($name, $options = array()) {
		$ThemeConfig = ClassRegistry::init('ThemeConfig');
		$data = $ThemeConfig->findExpanded();

		$url = $imgPath = $uploadUrl = $uploadThumbUrl = $originUrl = '';
		$thumbSuffix = '_thumb';
		$dir = WWW_ROOT . 'files' . DS . 'theme_configs' . DS;
		$themeDir = $path = getViewPath() . 'img' . DS;
		$num = '';
		if (!empty($options['num'])) {
			$num = '_' . $options['num'];
		}
		$options = array_merge(array(
			'thumb' => false,
			'class' => '',
			'popup' => false,
			'alt' => $data[$name . '_alt' . $num],
			'link' => $data[$name . '_link' . $num],
			'maxWidth' => '',
			'maxHeight' => '',
			'width' => '',
			'height' => ''
			), $options);
		$name = $name . $num;

		if ($data[$name]) {
			$pathinfo = pathinfo($data[$name]);
			$uploadPath = $dir . $data[$name];
			$uploadThumbPath = $dir . $pathinfo['filename'] . $thumbSuffix . '.' . $pathinfo['extension'];
			$uploadUrl = '/files/theme_configs/' . $data[$name];
			$uploadThumbUrl = '/files/theme_configs/' . $pathinfo['filename'] . $thumbSuffix . '.' . $pathinfo['extension'];
		}

		if ($data[$name]) {
			if (!$options['thumb']) {
				if (file_exists($uploadPath)) {
					$imgPath = $uploadPath;
					$url = $uploadUrl;
				}
			} else {
				if (file_exists($uploadThumbPath)) {
					$imgPath = $uploadThumbPath;
					$url = $uploadThumbUrl;
				}
			}
			$originUrl = $uploadUrl;
		}

		if (!$url) {
			$exts = array('png', 'jpg', 'gif');
			foreach ($exts as $ext) {
				if (file_exists($themeDir . $name . '.' . $ext)) {
					$url = '/theme/' . $this->siteConfig['theme'] . '/img/' . $name . '.' . $ext;
					$imgPath = $themeDir . $name . '.' . $ext;
					$originUrl = $url;
				}
			}
		}

		if (!$url) {
			return '';
		}

		$imgOptions = array();
		if ($options['class']) {
			$imgOptions['class'] = $options['class'];
		}
		if ($options['alt']) {
			$imgOptions['alt'] = $options['alt'];
		}
		if ($options['maxWidth'] || $options['maxHeight']) {
			$imginfo = getimagesize($imgPath);
			$widthRate = $heightRate = 0;
			if ($options['maxWidth']) {
				$widthRate = $imginfo[0] / $options['maxWidth'];
			}
			if ($options['maxHeight']) {
				$heightRate = $imginfo[1] / $options['maxHeight'];
			}
			if ($widthRate > $heightRate) {
				if ($options['maxWidth'] && $imginfo[0] > $options['maxWidth']) {
					$imgOptions['width'] = $options['maxWidth'];
				}
			} else {
				if ($options['maxHeight'] && ($imginfo[1] > $options['maxHeight'])) {
					$imgOptions['height'] = $options['maxHeight'];
				}
			}
		}
		if ($options['width']) {
			$imgOptions['width'] = $options['width'];
		}
		if ($options['height']) {
			$imgOptions['height'] = $options['height'];
		}

		$tag = $this->getImg($url, $imgOptions);
		if ($options['link'] || $options['popup']) {
			$linkOptions = array();
			if ($options['popup']) {
				$linkOptions['rel'] = 'colorbox';
				$link = $originUrl;
			} elseif ($options['link']) {
				$link = $options['link'];
			}
			if ($options['alt']) {
				$linkOptions['title'] = $options['alt'];
			}
			$tag = $this->getLink($tag, $link, $linkOptions);
		}
		return $tag;
	}

/**
 * 現在のテーマのURLを取得する
 *
 * @return string テーマのURL
 */
	public function getThemeUrl() {
		return $this->request->webroot . 'theme' . '/' . $this->siteConfig['theme'] . '/';
	}

/**
 * 現在のテーマのURLを出力する
 *
 * @return void
 */
	public function themeUrl() {
		echo $this->getThemeUrl();
	}

/**
 * ベースとなるURLを取得する
 *
 * サブフォルダやスマートURLについて考慮されている事が前提
 *
 * @return string ベースURL
 */
	public function getBaseUrl() {
		return $this->request->base . '/';
	}

/**
 * ベースとなるURLを出力する
 *
 * サブフォルダやスマートURLについて考慮されている事が前提
 *
 * @return void
 */
	public function baseUrl() {
		echo $this->getBaseUrl();
	}

/**
 * サブメニューを出力する
 *
 * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
 * @param array $options オプション（初期値 : array()）
 *	※ その他のパラメータについては、View::element() を参照
 * @return void
 */
	public function subMenu($data = array(), $options = array()) {
		echo $this->getSubMenu($data, $options);
	}

/**
 * サブメニューを取得する
 *
 * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
 * @param array $options オプション（初期値 : array()）
 *	※ その他のパラメータについては、View::element() を参照
 * @return string
 */
	public function getSubMenu($data = array(), $options = array()) {
		return $this->getElement('sub_menu', $data, $options);
	}

/**
 * コンテンツナビを出力する
 *
 * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
 * @param array $options オプション（初期値 : array()）
 *	※ その他のパラメータについては、View::element() を参照
 * @return void
 */
	public function contentsNavi($data = array(), $options = array()) {
		$this->element('contents_navi', $data, $options);
	}

/**
 * パンくずリストを出力する
 *
 * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
 * @param array $options オプション（初期値 : array()）
 *	※ その他のパラメータについては、View::element() を参照
 * @return void
 */
	public function crumbsList($data = array(), $options = array()) {
		$data = array_merge([
			'onSchema' => false
		], $data);
		$this->element('crumbs', $data, $options);
	}

/**
 * Google Analytics のトラッキングコードを出力する
 *
 * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
 * @param array $options オプション（初期値 : array()）
 *	※ その他のパラメータについては、View::element() を参照
 * @return void
 */
	public function googleAnalytics($data = [], $options = []) {
		$data = array_merge([
			'useUniversalAnalytics' => (bool) @$this->siteConfig['use_universal_analytics']
		], $data);
		$this->element('google_analytics', $data, $options);
	}

/**
 * Google Maps を出力する
 *
 * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
 * @param array $options オプション（初期値 : array()）
 *	※ その他のパラメータについては、View::element() を参照
 * @return void
 */
	public function googleMaps($data = [], $options = []) {
		echo $this->getGoogleMaps($data, $options);
	}

/**
 * Google Maps を取得する
 *
 * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
 * @param array $options オプション（初期値 : array()）
 *	※ その他のパラメータについては、View::element() を参照
 * @return void
 */
	public function getGoogleMaps($data = [], $options = []) {
		return $this->getElement('google_maps', $data, $options);
	}

/**
 * 表示件数設定機能を出力する
 *
 * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
 * @param array $options オプション（初期値 : array()）
 *	※ その他のパラメータについては、View::element() を参照
 * @return void
 */
	public function listNum($data = array(), $options = array()) {
		$this->element('list_num', $data, $options);
	}

/**
 * サイト内検索フォームを出力
 *
 * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
 * @param array $options オプション（初期値 : array()）
 *	※ その他のパラメータについては、View::element() を参照
 * @return void
 */
	public function siteSearchForm($data = array(), $options = array()) {
		echo $this->getSiteSearchForm($data, $options);
	}

/**
 * サイト内検索フォームを取得
 *
 * @param array $data 読み込むテンプレートに引き継ぐパラメータ（初期値 : array()）
 * @param array $options オプション（初期値 : array()）
 *	※ その他のパラメータについては、View::element() を参照
 * @return string
 */
	public function getSiteSearchForm($data = array(), $options = array()) {
		return $this->getElement('site_search_form', $data, $options);
	}

/**
 * WEBサイト名を出力する
 *
 * @return void
 */
	public function siteName() {
		echo $this->getSiteName();
	}

/**
 * WEBサイト名を取得する
 *
 * @return string サイト基本設定のWEBサイト名
 */
	public function getSiteName() {
		if (!empty($this->_View->viewVars['siteConfig']['formal_name'])) {
			return $this->_View->viewVars['siteConfig']['formal_name'];
		}

		if(!empty($this->siteConfig['formal_name'])) {
			return $this->siteConfig['formal_name'];
		}

		return '';
	}

/**
 * WEBサイトURLを出力する
 *
 * @param boolean ssl （初期値 : false）
 * @return void
 */
	public function siteUrl($ssl = false) {
		echo $this->getSiteUrl($ssl);
	}

/**
 * WEBサイトURLを取得する
 *
 * @param boolean ssl （初期値 : false）
 * @return string サイト基本設定のWEBサイト名
 */
	public function getSiteUrl($ssl = false) {
		if ($ssl) {
			return Configure::read('BcEnv.sslUrl');
		} else {
			return Configure::read('BcEnv.siteUrl');
		}
	}

/**
 * URLのパラメータ情報を返す
 * 主なreturnデータは
 * http://basercms.net/news/index/example/test?name=value の場合
 * 'plugin' => blog (利用しているプラグイン)
 * 'pass' => [0] => 'example'
 *           [1] => 'test'
 * 'isAjax' => (boolean)false
 * 'query' => 'name' => 'value'
 * 'url' => 'news/index/fuga/hoge'
 * 'here' => '/news/index/fuga/hoge'
 *
 * @return array URLのパラメータ情報の配列
 */
	public function getParams() {
		$params = $this->request->params ;
		$params['query'] = $this->request->query;
		$params['url'] = $this->request->url;
		$params['here'] = $this->request->here;
		unset($params['named']);
		unset($params['controller']);
		unset($params['action']);
		unset($params['models']);
		unset($params['_Token']);
		unset($params['paging']);
		return $params;
	}

/**
 * 現在のコンテンツ情報を取得する
 *
 * @return mixed|null
 */
	public function getCurrentContent() {
		if(!empty($this->request->params['Content'])) {
			return $this->request->params['Content'];
		}
		return null;
	}

/**
 * 現在のサイトプレフィックスを取得する
 *
 * @return string
 */
	public function getCurrentPrefix() {
		if(empty($this->request->params['Site'])) {
			return '';
		}
		$Site = ClassRegistry::init('Site');
		return $Site->getPrefix($this->request->params['Site']);
	}

/**
 * コンテンツ作成日を取得
 * @return null|string
 */
	public function getContentCreatedDate($format = 'Y/m/d H:i') {
		$content = $this->getCurrentContent();
		if($content['created_date']) {
			return date($format, strtotime($content['created_date']));
		} else {
			return '';
		}
	}

/**
 * コンテンツ更新日を取得
 *
 * @param string $format
 * @return null|string
 */
	public function getContentModifiedDate($format = 'Y/m/d H:i') {
		$content = $this->getCurrentContent();
		if($content['modified_date']) {
			return date($format, strtotime($content['modified_date']));
		} else {
			return '';
		}
	}

/**
 * 更新情報を出力する
 */
	public function updateInfo() {
		echo $this->getUpdateInfo();
	}

/**
 * 更新情報を取得する
 */
	public function getUpdateInfo() {
		return $this->getElement('update_info', [
			'createdDate' => $this->getContentCreatedDate(),
			'modifiedDate' => $this->getContentModifiedDate()
		]);
	}

/**
 * 関連サイトのリンク一覧を取得
 *
 * @param int $id コンテンツID
 */
	public function getRelatedSiteLinks($id = null, $excludeIds = []) {
		$options = [];
		if($excludeIds) {
			$options['excludeIds'] = $excludeIds;
		}
		$links = $this->BcContents->getRelatedSiteLinks($id, $options);
		return $this->getElement('related_site_links', ['links' => $links]);
	}

/**
 * 関連サイトのリンク一覧を表示
 *
 * @param int $id コンテンツID
 */
	public function relatedSiteLinks($id = null, $excludeIds = []) {
		echo $this->getRelatedSiteLinks($id, $excludeIds);
	}

/**
 * After Render
 *
 * @param string $viewFile
 */
	public function afterRender($viewFile) {
		parent::afterRender($viewFile);
		if(BcUtil::isAdminSystem()) {
			return;
		}
		if(empty($this->request->params['Site'])) {
			return;
		}
		if(isset($this->request->params['Site']['name']) && is_null($this->request->params['Site']['name'])) {
			return;
		}
		if(isset($this->request->params['Site']['device']) && $this->request->params['Site']['device'] != '') {
			return;
		}
		// 別URLの場合、alternateを出力（スマートフォンのみ対応）
		$pureUrl = $this->BcContents->getPureUrl($this->request->url, $this->request->params['Site']['id']);
		$agent = BcAgent::find('smartphone');
		$subSite = BcSite::findCurrentSub(false, $agent);
		if(!$subSite) {
			return;
		}
		$url = $subSite->makeUrl(new CakeRequest($pureUrl));
		$this->_View->set('meta',
			$this->BcHtml->meta('canonical',
				$this->BcHtml->url($url, true),
				[
					'rel' => 'canonical',
					'media' => 'only screen and (max-width: 640px)',
					'type' => null,
					'title' => null,
					'inline' => false
				]
			)
		);
	}

/**
 * トップページのタイトルをセットする
 *
 * @param $title
 */
	public function setHomeTitle($title = null) {
		if(!$title) {
			$crumbs = $this->getCrumbs();
			if ($crumbs) {
				$crumbs = array_reverse($crumbs);
				$title = $crumbs[0]['name'];
			}
		}
		$this->_View->set('homeTitle', $title);
	}

/**
 * スマートフォン用のウェブクリップアイコン用のタグを出力する
 *
 * @param string $fileName ファイル名（webroot に配置する事が前提）
 * @param bool $useGloss 光沢有無
 */
	public function webClipIcon($fileName = 'apple-touch-icon-precomposed.png', $useGloss = false) {
		if($useGloss) {
			$rel = 'apple-touch-icon';
		} else {
			$rel = 'apple-touch-icon-precomposed';
		}
		echo '<link rel="' . $rel . '" href="' . Router::url('/' . $fileName, true) . '" />';
	}

/**
 * コンテンツ管理用のURLを取得する
 *
 * @param string $url コンテンツ管理用URLの元データ
 *	省略時は request より現在のデータを取得
 *	request が取得できない場合は、トップページのURLを設定
 * @return string
 */
	public function getContentsUrl($url = null) {
		if(empty($url) && !empty($this->request->params['Content']['url'])) {
			$url = $this->request->params['Content']['url'];
		} else {
			$url = '/';
		}
		$site = BcSite::findCurrent();
		return $this->BcContents->getUrl($url, false, $site->useSubDomain);
	}

/**
 * Plugin 内の Baserヘルパを取得する
 *
 * @param string $name
 * @return bool|mixed Plugin 内の Baserヘルパ
 */
	public function getPluginBaser($name) {
		if(!empty($this->_pluginBasers[$name])) {
			return $this->_pluginBasers[$name];
		}
		return false;
	}


}