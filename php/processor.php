<?php
/**
 * px2-baser-cms-theme-adapter
 */
namespace tomk79\pickles2\baserCmsThemeAdapter;

/**
 * px2-baser-cms-theme-adapter
 */
class processor{
	/** Pickles 2 Object */
	private $px;

	/** Config Object */
	private $conf;

	/** Stubs */
	private $BcBaser;
	private $BcHtml;
	private $BcPage;
	private $BcArray;
	private $BcXml;
	private $BcContents;
	private $BcForm;

	/** Theme ID */
	private $theme_id;

	/** テーマフォルダ */
	private $path_theme_dir;

	/** ????? */
	private $name;
	private $viewPath;

	/**
	 * constructor
	 * @param object $px Pickles 2 Object
	 */
	public function __construct( $px, $conf, $theme_id, $path_theme_dir ){
		$this->px = $px;
		$this->conf = $conf;
		$this->path_theme_dir = $path_theme_dir;
		$this->theme_id = $theme_id;
	} // __construct()


	/**
	 * `$conf` を取得する
	 * @return object `$conf`
	 */
	public function get_conf(){
		return $this->conf;
	}

	/**
	 * テーマディレクトリのパスを取得する
	 * @return string テーマディレクトリのパス
	 */
	public function get_path_theme_dir(){
		return $this->path_theme_dir;
	}

	/**
	 * テーマIDを取得する
	 * @return string テーマID
	 */
	public function get_theme_id(){
		return $this->theme_id;
	}

	/**
	 * 初期データCSVを読み込む
	 * @param  string $name ファイル名
	 * @return array 読み込んだCSVデータ
	 */
	public function read_default_csv($name){
		$data = array();
		$path_theme_configs_csv = $this->get_path_theme_dir().'Config/data/default/'.$name;
		if(is_file($path_theme_configs_csv)){
			$theme_configs = $this->px->fs()->read_csv($path_theme_configs_csv);
			$theme_configs_definition = null;
			foreach($theme_configs as $theme_configs_row){
				if( !is_array($theme_configs_definition) ){
					$theme_configs_definition = array();
					foreach($theme_configs_row as $key=>$val){
						$theme_configs_definition[$val] = $key;
					}
					unset($key, $val);
					continue;
				}
				$data[$theme_configs_row[$theme_configs_definition['name']]] = $theme_configs_row[$theme_configs_definition['value']];
				// var_dump($theme_configs_row);
			}
			unset($theme_configs, $theme_configs_definition, $theme_configs_row);
		}
		return $data;
	}

	/**
	 * 完成したHTMLコードを取得する
	 * @param string $path_theme_layout_file テーマレイアウトファイルのパス
	 * @param array $data エレメントで参照するデータ
	 * @param array $options オプションのパラメータ
	 *  `subDir` (boolean) エレメントのパスについてプレフィックスによるサブディレクトリを追加するかどうか
	 * ※ その他のパラメータについては、View::element() を参照
	 * @return string 完成したHTMLコード
	 */
	public function bind_template( $path_theme_layout_file, $data = array(), $options = array() ){
		$this->BcBaser = new stubs_BcBaser( $this->px, $this );
		$this->BcHtml = new stubs_BcHtml($this->px, $this);
		$this->BcPage = new stubs_BcPage($this->px, $this);
		$this->BcArray = new stubs_BcArray($this->px, $this);
		$this->BcXml = new stubs_BcXml($this->px, $this);
		$this->BcContents = new stubs_BcContents($this->px, $this);
		$this->BcForm = new stubs_BcForm($this->px, $this);

		$options = array_merge(array(
			'subDir' => true
		), $options);

		if (isset($options['plugin']) && !$options['plugin']) {
			unset($options['plugin']);
		}

		$file = $plugin = null;

		if (!isset($options['callbacks'])) {
			$options['callbacks'] = false;
		}

		extract($data);

		$realpath_layout_file = $this->path_theme_dir.$path_theme_layout_file;
		if( !is_file($realpath_layout_file) ){
			$realpath_layout_file = __DIR__.'/../baserCMS/lib/Baser/View/'.$path_theme_layout_file;
		}
		if( !is_file($realpath_layout_file) ){
			$realpath_layout_file = __DIR__.'/../baserCMS/lib/Cake/View/'.$path_theme_layout_file;
		}
		if( !is_file($realpath_layout_file) ){
			return '';
		}

		ob_start();
		include( $realpath_layout_file );
		$finalized_html_code = ob_get_clean();

		return $finalized_html_code;
	}

}
