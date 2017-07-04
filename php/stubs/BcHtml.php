<?php
/**
 * px2-baser-cms-theme-adapter
 */
namespace tomk79\pickles2\baserCmsThemeAdapter;

/**
 * px2-baser-cms-theme-adapter
 */
class stubs_BcHtml{
	/** Pickles 2 Object */
	private $px = '';

	/**
	 * constructor
	 *
	 * @param object $px Pickles 2 Object
	 */
	public function __construct( $px ){
		$this->px = $px;
	} // __construct()

	/**
	 * ドキュメントタイプを指定するタグを出力する
	 *
	 * @param string $type 出力ドキュメントタイプの文字列（初期値 : 'xhtml-trans'）
	 * @return void
	 */
	public function docType($type = 'xhtml-trans') {
		echo '<!DOCTYPE html>'."\n";
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
		if (!@strlen($charset) ) {
			$charset = 'UTF-8';
		}
		echo '<meta charset="'.htmlspecialchars($charset).'" />'."\n";
	}

	/**
	 * CSS を生成する
	 */
	public function css() {
		return '';
	}

	/**
	 * script を生成する
	 */
	public function script() {
		return '';
	}

}
