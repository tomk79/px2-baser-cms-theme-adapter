<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('TextHelper', 'View/Helper');
App::uses('BcTimeHelper', 'View/Helper');

/**
 * Textヘルパー拡張
 *
 * @package Baser.View.Helper
 */
class BcTextHelper extends TextHelper {

/**
 * helpers
 *
 * @var array
 */
	// CUSTOMIZE MODIFY 2014/07/03 ryuring
	// >>>
	//public $helpers = array('Html');
	// ---
	public $helpers = array('BcTime', 'BcForm', 'Html');
	// <<<

// CUSTOMIZE ADD 2014/07/03 ryuring
// >>>
/**
 * boolean型を ○ または ― マークで出力
 *
 * @param boolean $value
 * @return string ○ または ― 
 */
	public function booleanMark($value) {
		if ($value) {
			return "○";
		} else {
			return "―";
		}
	}

/**
 * boolean型用のリストを ○ ― マークで出力
 *
 * @return array マークリスト（ - ○ ）
 */
	public function booleanMarkList() {
		return array(0 => "―", 1 => "○");
	}

/**
 * boolean型用のリストを「有」「無」で出力
 *
 * @return array 「有」「無」リスト
 */
	public function booleanExistsList() {
		return array(0 => "無", 1 => "有");
	}

/**
 * boolean型用のリストを可、不可で出力
 *
 * @return array 可/不可リスト
 */
	public function booleanAllowList() {
		return array(0 => "不可", 1 => "可");
	}

/**
 * boolean型用のリストを[〜する/〜しない]形式で出力する
 *
 * @param string $doText Do文字列
 * @return array [〜する/〜しない]形式のリスト
 */
	public function booleanDoList($doText = null) {
		return array(0 => $doText . "しない", 1 => $doText . "する");
	}

/**
 * boolean型のデータを [〜する / 〜しない] 形式で出力する
 *
 * @param boolean $value 値
 * @param string $doText Do文字列
 * @return string
 */
	public function booleanDo($value, $doText = null) {
		$booleanDoList = $this->booleanDoList($doText);
		return $booleanDoList[$value];
	}

/**
 * 都道府県のリストを出力
 *
 * @return array 都道府県リスト
 */
	public function prefList($empty = '都道府県') {
		$pref = array();
		if ($empty) {
			$pref = array("" => $empty);
		} elseif ($pref !== false) {
			$pref = array("" => "");
		}

		$pref = $pref + array(
			1 => "北海道", 2 => "青森県", 3 => "岩手県", 4 => "宮城県", 5 => "秋田県", 6 => "山形県", 7 => "福島県",
			8 => "茨城県", 9 => "栃木県", 10 => "群馬県", 11 => "埼玉県", 12 => "千葉県", 13 => "東京都", 14 => "神奈川県",
			15 => "新潟県", 16 => "富山県", 17 => "石川県", 18 => "福井県", 19 => "山梨県", 20 => "長野県", 21 => "岐阜県",
			22 => "静岡県", 23 => "愛知県", 24 => "三重県", 25 => "滋賀県", 26 => "京都府", 27 => "大阪府", 28 => "兵庫県",
			29 => "奈良県", 30 => "和歌山県", 31 => "鳥取県", 32 => "島根県", 33 => "岡山県", 34 => "広島県", 35 => "山口県",
			36 => "徳島県", 37 => "香川県", 38 => "愛媛県", 39 => "高知県", 40 => "福岡県", 41 => "佐賀県", 42 => "長崎県",
			43 => "熊本県", 44 => "大分県", 45 => "宮崎県", 46 => "鹿児島県", 47 => "沖縄県"
		);
		return $pref;
	}

/**
 * 性別を出力
 * 
 * @param array $value
 * @return string
 */
	public function sex($value = 1) {
		if (preg_match('/[1|2]/', $value)) {
			$sexes = array(1 => '男', 2 => '女');
			return $sexes[$value];
		}
		return '';
	}

/**
 * 郵便番号にハイフンをつけて出力
 *
 * @param string $value 郵便番号
 * @param string $prefix '〒'
 * @return string	〒マーク、ハイフン付きの郵便番号
 * @access	public
 */
	public function zipFormat($value, $prefix = "〒 ") {
		if (preg_match('/-/', $value)) {
			return $prefix . $value;
		}
		$right = substr($value, 0, 3);
		$left = substr($value, 3, 4);

		return $prefix . $right . "-" . $left;
	}

/**
 * 番号を都道府県に変換して出力
 *
 * @param int $value 都道府県番号
 * @param string $noValue 都道府県名
 * @return string 都道府県名
 * @access	public
 */
	public function pref($value, $noValue = '') {
		if (!empty($value) && ($value >= 1 && $value <= 47)) {
			$list = $this->prefList();
			return $list[(int) $value];
		}
		return $noValue;
	}

/**
 * データをチェックして空の場合に指定した値を返す
 *
 * @param mixed $value
 * @param	mixed $noValue データが空の場合に返す値
 * @return mixed そのままのデータ/空の場合のデータ
 */
	public function noValue($value, $noValue) {
		if (!$value) {
			return $noValue;
		} else {
			return $value;
		}
	}

/**
 * boolean型のデータを可、不可で出力
 *
 * 0 or 1 の int も許容する
 * 文字列を与えた場合には、不可を出力
 *
 * @param boolean $value
 * @return	string	可/不可
 * @access	public
 */
	public function booleanAllow($value) {
		$list = $this->booleanAllowList();
		return $list[(int) $value];
	}

/**
 * boolean型用を有無で出力
 *
 * @param boolean $value
 * @return string 有/無
 */
	public function booleanExists($value) {
		$list = $this->booleanExistsList();
		return $list[(int) $value];
	}

/**
 * 配列形式の和暦データを文字列データに変換する
 *
 * FormHelper::dateTime() で取得した配列データを
 * BcTimeHelper::convertToWarekiArray() で配列形式の和暦データに変換したものを利用する
 *
 * @param array $arrDate
 * 	− `wareki`:和暦に変換する場合は、trueを設定、設定しない場合何も返さない
 *	- `year` :和暦のキーを付与した年。
 * 		h: 平成 / s: 昭和 / t: 大正 / m: 明治
 * 		（例）h-27
 * 	- `month` : 月
 * 	- `day` : 日
 * @return string 和暦（例）平成 27年 8月 11日
 */
	public function dateTimeWareki($arrDate) {
		if (!is_array($arrDate)) {
			return;
		}
		if (!$arrDate['wareki'] || !$arrDate['year'] || !$arrDate['month'] || !$arrDate['day']) {
			return;
		}
		list($w, $year) = explode('-', $arrDate['year']);
		$wareki = $this->BcTime->nengo($w);
		return $wareki . " " . $year . "年 " . $arrDate['month'] . "月 " . $arrDate['day'] . '日';
	}

/**
 * 通貨表示
 *
 * @param int $value 通貨となる数値
 * @param string $prefix '¥'
 * @return string
 */
	public function moneyFormat($value, $prefix = '¥') {
		if(!is_numeric($value)) {
			return false;
		}
		if ($value) {
			return $prefix . number_format($value);
		} else {
			return '';
		}
	}

/**
 * 配列形式の日付データを文字列データに変換する
 *
 * 配列形式のデータは、FormHelper::dateTime()で取得できる
 *
 * @param array $arrDate
 * 	- `year` : 年
 * 	- `month` : 月
 * 	- `day` : 日
 * @return string 日付（例）2015/8/11
 */
	public function dateTime($arrDate) {
		if (!isset($arrDate['year']) || !isset($arrDate['month']) || !isset($arrDate['day'])) {
			return;
		}

		return $arrDate['year'] . "/" . $arrDate['month'] . "/" . $arrDate['day'];
	}

/**
 * 文字をフォーマット形式で出力し、値が存在しない場合は初期値を出力する
 *
 * @param string $format フォーマット文字列（sprintfで利用できるもの）
 * @param mixed $value フォーマット対象の値
 * @param	mixed $noValue データがなかった場合の初期値
 * @return	string	変換後の文字列
 * @access	public
 */
	public function format($format, $value, $noValue = '') {
		if ($value === '' || is_null($value)) {
			return $noValue;
		} else {
			return sprintf($format, $value);
		}
	}

/**
 * モデルのコントロールソースより表示用データを取得する
 *
 * @param string $field フィールド名
 * @param mixed $value 値
 * @return string 表示用データ
 */
	public function listValue($field, $value) {
		$list = $this->BcForm->getControlSource($field);
		if ($list && isset($list[$value])) {
			return $list[$value];
		} else {
			return false;
		}
	}

/**
 * 配列とキーを指定して値を取得する
 * 
 * @param int $key 配列のキー
 * @param array $array 配列
 * @param mixed type $noValue 値がない場合に返す値
 * @return mixed
 */
	public function arrayValue($key, $array, $noValue = '') {
		if (is_numeric($key)) {
			$key = (int) $key;
		}
		if (isset($array[$key])) {
			return $array[$key];
		}
		return $noValue;
	}

/**
 * 連想配列とキーのリストより値のリストを取得し文字列で返す
 * 文字列に結合する際、指定した結合文字を指定できる
 *
 * @param string $glue 結合文字
 * @param array $keys 結合対象のキーのリスト
 * @param array $array リスト
 * @return string
 */
	public function arrayValues($glue, $keys, $array) {
		$values = array();
		foreach ($keys as $key) {
			if (isset($array[$key])) {
				$values[] = $array[$key];
			}
		}
		if ($values) {
			return implode($glue, $values);
		} else {
			return '';
		}
	}

/**
 * 日付より年齢を取得する
 *
 * @param string $birthday
 * @param string $suffix
 * @param mixed $noValue
 * @return mixed
 */
	public function age($birthday, $suffix = '歳', $noValue = '不明') {
		if (!$birthday) {
			return $noValue;
		}
		$byear = date('Y', strtotime($birthday));
		$bmonth = date('m', strtotime($birthday));
		$bday = date('d', strtotime($birthday));
		$tyear = date('Y');
		$tmonth = date('m');
		$tday = date('d');
		$age = $tyear - $byear;
		if ($tmonth * 100 + $tday < $bmonth * 100 + $bday) {
			$age--;
		}

		return $age . $suffix;
	}

/**
 * boolean型用のリストを有効、無効で出力
 *
 * @return array 可/不可リスト
 */
	public function booleanStatusList() {
		return array(0 => "無効", 1 => "有効");
	}

/**
 * boolean型用を無効・有効で出力
 *
 * @param boolean
 * @return string 無効/有効
 */
	public function booleanStatus($value) {
		$list = $this->booleanStatusList();
		return $list[(int) $value];
	}
// <<<
}
