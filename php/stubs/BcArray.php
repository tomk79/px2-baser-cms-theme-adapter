<?php
/**
 * px2-baser-cms-theme-adapter
 */
namespace tomk79\pickles2\baserCmsThemeAdapter;

/**
 * px2-baser-cms-theme-adapter
 */
class stubs_BcArray{
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
 * 配列の最初の要素かどうか調べる
 *
 * @param array $array 配列
 * @param mixed $key 現在のキー
 * @return boolean
 */
	public function first($array, $key) {
		reset($array);
		$first = key($array);
		if ($key === $first) {
			return true;
		} else {
			return false;
		}
	}

/**
 * 配列の最後の要素かどうか調べる
 *
 * @param array $array 配列
 * @param mixed $key 現在のキー
 * @return boolean
 */
	public function last($array, $key) {
		end($array);
		$end = key($array);
		if ($key === $end) {
			return true;
		} else {
			return false;
		}
	}

/**
 * 配列にテキストを追加する
 *
 * @param	array	$array
 * @param	string	$prefix
 * @param	string	$suffix
 * @return	array
 * @access	public
 */
	public function addText($array, $prefix = '', $suffix = '') {
		if ($prefix || $suffix) {
			array_walk($array, array($this, '__addText'), $prefix . ',' . $suffix);
		}
		return $array;
	}

/**
 * addTextToArrayのコールバックメソッド
 *
 * @param	string	$value
 * @param	string	$key
 * @param	string	$add
 * @return	string
 * @access	private
 */
	private function __addText(&$value, $key, $add) {
		if ($add) {
			list($prefix, $suffix) = explode(',', $add);
		}
		$value = $prefix . $value . $suffix;
	}

}
