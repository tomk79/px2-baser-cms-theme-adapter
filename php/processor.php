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

	/** $BcBaser Stub */
	protected $BcBaser;

	/** 出力コード */
	private $finalized_html_code = '';

	/**
	 * constructor
	 * @param object $px Pickles 2 Object
	 * @param string $path_theme_layout_file テーマレイアウトファイルのパス
	 */
	public function __construct( $px, $path_theme_layout_file ){
		$this->px = $px;

		$this->BcBaser = new stubs_BcBaser( $this->px );

		ob_start();
		include( $path_theme_layout_file );
		$this->finalized_html_code = ob_get_clean();
	} // __construct()

	/**
	 * 完成したHTMLコードを取得する
	 * @return string 完成したHTMLコード
	 */
	public function get_finalized_html_code(){
		return $this->finalized_html_code;
	}

}
