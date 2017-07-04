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

	/** Stubs */
	private $BcBaser;
	private $BcHtml;
	private $BcPage;

	/** テーマフォルダ */
	private $path_theme_dir;

	/** ????? */
	private $name;

	/**
	 * constructor
	 * @param object $px Pickles 2 Object
	 */
	public function __construct( $px, $path_theme_dir ){
		$this->px = $px;
		$this->path_theme_dir = $path_theme_dir;
	} // __construct()


	/**
	 * 完成したHTMLコードを取得する
	 * @param string $path_theme_layout_file テーマレイアウトファイルのパス
	 * @return string 完成したHTMLコード
	 */
	public function bind_template( $path_theme_layout_file ){
		$this->BcBaser = new stubs_BcBaser( $this->px, $this );
		$this->BcHtml = new stubs_BcHtml($this->px);
		$this->BcPage = new stubs_BcPage($this->px);

		ob_start();
		include( $this->path_theme_dir.$path_theme_layout_file );
		$finalized_html_code = ob_get_clean();

		return $finalized_html_code;
	}

}
