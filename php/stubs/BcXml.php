<?php
/**
 * px2-baser-cms-theme-adapter
 */
namespace tomk79\pickles2\baserCmsThemeAdapter;

/**
 * px2-baser-cms-theme-adapter
 */
class stubs_BcXml{
	/** Pickles 2 Object */
	private $px = '';

/**
 * XML document version
 *
 * @var string
 */
	private $version = '1.0';

/**
 * XML document encoding
 *
 * @var string
 */
	private $encoding = 'UTF-8';


	/**
	 * constructor
	 *
	 * @param object $px Pickles 2 Object
	 */
	public function __construct( $px ){
		$this->px = $px;
	} // __construct()

/**
 * XML宣言を生成
 * IE6以外の場合のみ生成する
 *
 * @param array $attrib
 * @return string XML宣言
 */
	public function header($attrib = array()) {

		if (is_array($attrib)) {
			$attrib = array_merge(array('encoding' => $this->encoding), $attrib);
		}
		if (is_string($attrib) && strpos($attrib, 'xml') !== 0) {
			$attrib = 'xml ' . $attrib;
		}

		$header = 'xml';
		if (is_string($attrib)) {
			$header = $attrib;
		} else {

			$attrib = array_merge(array('version' => $this->version, 'encoding' => $this->encoding), $attrib);
			foreach ($attrib as $key => $val) {
				$header .= ' ' . $key . '="' . $val . '"';
			}
		}
		$out = '<' . '?' . $header . ' ?' . '>';

		return $out;
	}

}
