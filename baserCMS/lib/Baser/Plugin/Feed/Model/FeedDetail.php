<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('FeedAppModel', 'Feed.Model');

/**
 * feed_detail
 *
 * @package Feed.Model
 */
class FeedDetail extends FeedAppModel {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'FeedDetail';

/**
 * ビヘイビア
 * 
 * @var array
 */
	public $actsAs = array('BcCache');

/**
 * belongsTo
 * 
 * @var array
 */
	public $belongsTo = array('FeedConfig' => array('className' => 'Feed.FeedConfig',
			'conditions' => '',
			'order' => '',
			'foreignKey' => 'feed_config_id'
	));

/**
 * validate
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			array('rule' => array('notBlank'),
				'message' => "フィード詳細名を入力してください。",
				'required' => true),
			array('rule' => array('maxLength', 50),
				'message' => 'フィード詳細名は50文字以内で入力してください。')
		),
		'url' => array(
			array('rule' => array('notBlank'),
				'message' => "フィードURLを入力してください。",
				'required' => true),
			array('rule' => array('maxLength', 255),
				'message' => 'フィードURLは255文字以内で入力してください。')
		),
		'category_filter' => array(
			array('rule' => array('maxLength', 255),
				'message' => 'カテゴリフィルタは255文字以内で入力してください。')
		)
	);

/**
 * コントロールソースを取得する
 *
 * @param string $field フィールド名
 * @return array コントロールソース
 * @access	public
 */
	public function getControlSource($field = null) {
		$controlSources['cache_time'] = array('+1 minute' => '1分',
			'+30 minutes' => '30分',
			'+1 hour' => '1時間',
			'+6 hours' => '6時間',
			'+24 hours' => '1日');
		return $controlSources[$field];
	}

/**
 * 初期値を取得する
 * 
 * @param string $feedDetailId
 * @retun array $data
 */
	public function getDefaultValue($feedConfigId) {
		$feedConfig = $this->FeedConfig->find('first', array('conditions' => array('FeedConfig.id' => $feedConfigId)));
		$data[$this->name]['feed_config_id'] = $feedConfigId;
		$data[$this->name]['name'] = $feedConfig['FeedConfig']['name'];
		$data[$this->name]['cache_time'] = '+30 minutes';
		return $data;
	}

}
