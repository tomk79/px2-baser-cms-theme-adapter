<?php
/**
 * px2-baser-cms-theme-adapter
 */
namespace tomk79\pickles2\baserCmsThemeAdapter;

/**
 * main.php
 */
class theme{
	/** Picklesオブジェクト */
	private $px;
	/** テーマディレクトリのパス */
	private $path_theme_dir;
	/** カレントページの情報 */
	private $page;
	/** テーマスイッチ名 */
	private $param_theme_switch = 'THEME';
	/** テーマ名を格納するクッキー名 */
	private $cookie_theme_switch = 'THEME';
	/** 選択されるテーマID */
	private $theme_id = 'default';
	/** テーマコレクション */
	private $theme_collection;
	/** テーマのコンフィグオプション */
	private $theme_options;
	/** px2-multithemeの設定情報 */
	private $conf;

	/**
	 * entry method
	 * @param object $px Picklesオブジェクト
	 * @param object $options プラグイン設定
	 */
	public static function exec( $px, $options = null ){
		$theme = new self($px, $options);
		$src = $theme->bind($px);
		$px->bowl()->replace($src, '');
		return true;
	}

	/**
	 * constructor
	 * @param object $px Picklesオブジェクト
	 * @param object $options プラグイン設定
	 */
	public function __construct($px, $options = null){
		$this->px = $px;

		$this->conf = new \stdClass();
		$this->conf->path_theme_collection = $this->px->get_path_homedir().'bc_themes'.DIRECTORY_SEPARATOR;
		if( strlen(@$options->path_theme_collection) ){
			$this->conf->path_theme_collection = $this->px->fs()->get_realpath($options->path_theme_collection.DIRECTORY_SEPARATOR);
		}
		$this->conf->default_theme_id = 'default';

		if( strlen(@$options->default_theme_id) ){
			$this->conf->default_theme_id = $options->default_theme_id;
		}
		$this->conf->attr_bowl_name_by = 'data-contents-area';
		if( strlen(@$options->attr_bowl_name_by) ){
			$this->conf->attr_bowl_name_by = $options->attr_bowl_name_by;
		}
		if( strlen(@$options->param_theme_switch) ){
			$this->param_theme_switch = $options->param_theme_switch;
		}
		if( strlen(@$options->cookie_theme_switch) ){
			$this->cookie_theme_switch = $options->cookie_theme_switch;
		}

		$this->theme_options = (@$options->options ? $options->options : new \stdClass());
		$this->theme_options = json_decode( json_encode($this->theme_options), true );
		// var_dump($this->theme_options);


		// h1 の処理 の設定
		$this->conf->h1 = 'supply';
			// 'supply' -> h1 を付加する
			// 'pass' -> 付加しない
		if( strlen(@$options->h1) ){
			$this->conf->h1 = $options->h1;
		}

		// サイトマップからページ情報を取得
		$this->page = $this->px->site()->get_current_page_info();
		if( @!strlen( $this->page['layout'] ) ){
			$this->page['layout'] = 'default';
		}

		// テーマを選択する
		$this->auto_select_theme();


		// テーマディレクトリを決定する
		$this->path_theme_dir = $this->px->fs()->get_realpath( $this->conf->path_theme_collection.'/'.$this->theme_id.'/' );
		if( !is_dir($this->path_theme_dir) ){
			$this->path_theme_dir = $this->px->fs()->get_realpath( $this->get_composer_root_dir().'/vendor/'.$this->theme_id.'/theme/' );
		}

		// テーマのリソースファイルをキャッシュに複製する
		foreach( array('img','css','js','files') as $resource_dir_name ){
			if( is_dir($this->path_theme_dir.'/'.$resource_dir_name.'/') ){
				$this->px->fs()->copy_r(
					$this->path_theme_dir.'/'.$resource_dir_name.'/' ,
					$this->px->realpath_plugin_files('/'.urlencode($this->theme_id).'/'.$resource_dir_name.'/')
				);
			}
		}
	} // __construct()

	/**
	 * auto select theme
	 */
	private function auto_select_theme(){
		$this->theme_id = @$this->conf->default_theme_id;
		if( !strlen( $this->theme_id ) ){
			$this->theme_id = 'default';
		}

		if( strlen( @$this->px->req()->get_cookie($this->cookie_theme_switch) ) ){
			$this->theme_id = @$this->px->req()->get_cookie($this->cookie_theme_switch);
		}

		$param_theme_id = $this->px->req()->get_param($this->param_theme_switch);
		if( strlen( $param_theme_id ) && $this->is_valid_theme_id( $param_theme_id ) ){
			// GETパラメータに、有効な THEME が入ってたら

			if( $this->theme_id !== $param_theme_id ){
				// 現在選択中のテーマと別のIDだったら

				if( $this->px->fs()->is_dir( $this->conf->path_theme_collection.'/'.$param_theme_id.'/' ) || $this->px->fs()->is_dir( $this->get_composer_root_dir().'/vendor/'.$param_theme_id.'/theme/' ) ){
					// テーマが実在していたら

					$this->theme_id = $param_theme_id;
					$this->px->req()->set_cookie( $this->cookie_theme_switch, $this->theme_id );

					if( $this->theme_id == @$this->conf->default_theme_id ){
						$this->px->req()->delete_cookie( $this->cookie_theme_switch );
					}
				}
			}
		}

		return true;
	}

	/**
	 * bind content to theme
	 *
	 * @param object $px Picklesオブジェクト
	 * @return string テーマを実行した結果のHTMLコード
	 */
	private function bind( $px ){
		$path_theme_layout_file = $this->px->fs()->get_realpath( $this->path_theme_dir.'Layouts/'.$this->page['layout'].'.php' );
		if( !$px->fs()->is_file( $path_theme_layout_file ) ){
			$this->page['layout'] = 'default';
			$path_theme_layout_file = $this->px->fs()->get_realpath( $this->path_theme_dir.'Layouts/'.$this->page['layout'].'.php' );
		}
		if( !$px->fs()->is_file( $path_theme_layout_file ) ){
			$this->path_theme_dir = __DIR__.'/default/';
			$this->page['layout'] = 'default';
			$path_theme_layout_file = $this->px->fs()->get_realpath( $this->path_theme_dir.'Layouts/default.php' );
		}

		$processor = new processor( $this->px, $this->conf, $this->theme_id, $this->path_theme_dir);
		return $processor->bind_template('Layouts/'.$this->page['layout'].'.php');
	}

	/**
	 * テーマごとのオプションを取得する
	 *
	 * コンフィグオプションに指定されたテーマ別設定の値を取り出します。
	 *
	 * @param string $key 取り出したいオプションのキー
	 */
	public function get_option($key){
		return @$this->theme_options[$this->theme_id][$key];
	}


	/**
	 * composer のルートディレクトリのパスを取得する
	 *
	 * @return string vendorディレクトリの絶対パス
	 */
	private function get_composer_root_dir(){
		$tmp_composer_root_dir = $this->px->fs()->get_realpath( '.' );
		// var_dump($tmp_composer_root_dir);
		while(1){
			if( $this->px->fs()->is_dir( $tmp_composer_root_dir.'/vendor/' ) && $this->px->fs()->is_file( $tmp_composer_root_dir.'/composer.json' ) ){
				break;
			}
			if( realpath($tmp_composer_root_dir) == realpath( dirname($tmp_composer_root_dir) ) ){
				$tmp_composer_root_dir = false;
				break;
			}
			$tmp_composer_root_dir = dirname($tmp_composer_root_dir);
			continue;
		}
		// var_dump( $tmp_composer_root_dir );
		return $tmp_composer_root_dir;
	}

	/**
	 * テーマコレクションを作成する
	 *
	 * テーマコレクションディレクトリおよびvendorディレクトリを検索し、
	 * 選択可能なテーマの一覧を生成します。
	 *
	 * @return array テーマコレクション
	 */
	public function mk_theme_collection(){
		$collection = array();

		// テーマコレクションを作成
		foreach( $this->px->fs()->ls( $this->conf->path_theme_collection ) as $theme_id ){
			$collection[$theme_id] = [
				'id'=>$theme_id,
				'path'=>$this->px->fs()->get_realpath( $this->conf->path_theme_collection.'/'.$theme_id.'/' ),
				'type'=>'collection'
			];
		}

		// vendorディレクトリ内から検索
		$tmp_composer_root_dir = $this->get_composer_root_dir();
		// var_dump( $tmp_composer_root_dir );

		if( $this->px->fs()->is_dir( $tmp_composer_root_dir.'/vendor/' ) ){
			foreach( $this->px->fs()->ls( $tmp_composer_root_dir.'/vendor/' ) as $vendor_id ){
				if( !$this->px->fs()->is_dir( $tmp_composer_root_dir.'/vendor/'.$vendor_id ) ){ continue; }
				foreach( $this->px->fs()->ls( $tmp_composer_root_dir.'/vendor/'.$vendor_id ) as $package_id ){
					if( $this->px->fs()->is_dir( $tmp_composer_root_dir.'/vendor/'.$vendor_id.'/'.$package_id.'/theme/' ) ){
						$collection[$vendor_id.'/'.$package_id] = [
							'id'=>$vendor_id.'/'.$package_id,
							'path'=>$px->fs()->get_realpath( $tmp_composer_root_dir.'/vendor/'.$vendor_id.'/'.$package_id.'/theme/' ),
							'type'=>'vendor'
						];
					}
				}
			}
		}
		// var_dump($collection);

		return $collection;
	}

	/**
	 * テーマIDとして有効な文字列か検証する
	 *
	 * @param string $theme_id 検証対象のテーマID
	 * @return bool 有効なら true, 無効なら false
	 */
	public function is_valid_theme_id( $theme_id ){
		if( preg_match('/[^a-zA-Z0-9\/\.\-\_]/', $theme_id) ){ return false; }
		if( preg_match('/(?:^|\/)[\.]{1,2}(?:$|\/)/', $theme_id) ){ return false; }
		if( preg_match('/^\//', $theme_id) ){ return false; }
		if( preg_match('/\/$/', $theme_id) ){ return false; }
		if( preg_match('/\/\//', $theme_id) ){ return false; }

		return true;
	}

	/**
	 * 選択されたレイアウト名を取得する
	 *
	 * レイアウトは、Pickles 2 のサイトマップCSVの layout 列に指定すると選択できます。
	 *
	 * layout列には、拡張子を含まない値を指定してください。
	 * レイアウト `hoge.html` を選択したい場合、 layout列には `hoge` と入力します。
	 *
	 * layout列が空白の場合、 `default.html` が選択されます。
	 */
	public function get_layout(){
		return @$this->page['layout'];
	}

	/**
	 * $conf->attr_bowl_name_by 設定の値を受け取る
	 *
	 * このメソッドが返す値は、 テーマのコンテンツエリアを囲うラッパー要素にセットされるべき、bowl名を格納するための属性名です。
	 * デフォルトは `data-contents-area` ですが、コンフィグオプションで変更することができます。
	 *
	 * bowl `main` は次のように実装します。
	 * ```
	 * <div class="contents" <?= htmlspecialchars($theme->get_attr_bowl_name_by())?>="main">
	 * 	 <?= $px->bowl()->pull() ?>
	 * </div>
	 * ```
	 *
	 * 独自の名前 `hoge` という bowl を作るには、次のように実装します。
	 * ```
	 * <div class="contents" <?= htmlspecialchars($theme->get_attr_bowl_name_by())?>="hoge">
	 * 	 <?= $px->bowl()->pull('hoge') ?>
	 * </div>
	 * ```
	 *
	 * この値は、 Pickles 2 Desktop Tool のGUI編集機能が、テーマの画面から編集可能領域を探しだすために利用します。
	 *
	 * @return string bowl名を格納するための属性名
	 */
	public function get_attr_bowl_name_by(){
		return $this->conf->attr_bowl_name_by;
	}

}
