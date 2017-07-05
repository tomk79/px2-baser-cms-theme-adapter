<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * メールコンテンツモデル
 *
 * @package Mail.Model
 *
 */
class MailContent extends MailAppModel {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'MailContent';

/**
 * behaviors
 *
 * @var array
 */
	public $actsAs = array('BcSearchIndexManager', 'BcCache', 'BcContents');

/**
 * hasMany
 *
 * @var array
 */
	public $hasMany = array('MailField' =>
		array('className' => 'Mail.MailField',
			'order' => 'sort',
			'limit' => 100,
			'foreignKey' => 'mail_content_id',
			'dependent' => true,
			'exclusive' => false,
			'finderQuery' => ''));

/**
 * validate
 *
 * @var array
 */
	public $validate = array(
		'sender_name' => array(
			array('rule' => array('notBlank'),
				'message' => "送信先名を入力してください。"),
			array('rule' => array('maxLength', 50),
				'message' => '送信先名は50文字以内で入力してください。')
		),
		'subject_user' => array(
			array('rule' => array('notBlank'),
				'message' => "自動返信メール件名[ユーザー宛]を入力してください。"),
			array('rule' => array('maxLength', 50),
				'message' => '自動返信メール件名[ユーザー宛]は50文字以内で入力してください。')
		),
		'subject_admin' => array(
			array('rule' => array('notBlank'),
				'message' => "自動送信メール件名[管理者宛]を入力してください。"),
			array('rule' => array('maxLength', 50),
				'message' => '自動返信メール件名[管理者宛]は50文字以内で入力してください。')
		),
		'form_template' => array(
			array('rule' => array('halfText'),
				'message' => "メールフォームテンプレート名は半角のみで入力してください。",
				'allowEmpty' => false),
			array('rule' => array('maxLength', 20),
				'message' => 'フォームテンプレート名は20文字以内で入力してください。')
		),
		'mail_template' => array(
			array('rule' => array('halfText'),
				'message' => "送信メールテンプレートは半角のみで入力してください。",
				'allowEmpty' => false),
			array('rule' => array('maxLength', 20),
				'message' => 'メールテンプレート名は20文字以内で入力してください。')
		),
		'redirect_url' => array(
			array('rule' => array('maxLength', 255),
				'message' => 'リダイレクトURLは255文字以内で入力してください。')
		),
		'sender_1' => array(
			array('rule' => array('emails'),
				'allowEmpty' => true,
				'message' => '送信先メールアドレスの形式が不正です。'),
			array('rule' => array('maxLength', 255),
				'message' => '送信先メールアドレスは255文字以内で入力してください。')
		),
		'sender_2' => array(
			array('rule' => array('emails'),
				'allowEmpty' => true,
				'message' => '送信先メールアドレスの形式が不正です。'),
			array('rule' => array('maxLength', 255),
				'message' => 'CC用送信先メールアドレスは255文字以内で入力してください。')
		),
		'ssl_on' => array(
			'rule' => 'checkSslUrl',
			"message" => 'SSL通信を利用するには、システム設定で、事前にSSL通信用のWebサイトURLを指定してください。'
		)
	);

/**
 * SSL用のURLが設定されているかチェックする
 * 
 * @param string $check チェック対象文字列
 * @return boolean
 */
	public function checkSslUrl($check) {
		if ($check[key($check)]) {
			$sslUrl = Configure::read('BcEnv.sslUrl');
			if (empty($sslUrl)) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}

/**
 * 英数チェック
 *
 * @param string $check チェック対象文字列
 * @return boolean
 */
	public function alphaNumeric($check) {
		if (preg_match("/^[a-z0-9]+$/", $check[key($check)])) {
			return true;
		} else {
			return false;
		}
	}

/**
 * フォームの初期値を取得する
 *
 * @return string
 */
	public function getDefaultValue() {
		$data['MailContent']['sender_name'] = '送信先名を入力してください';
		$data['MailContent']['subject_user'] = 'お問い合わせ頂きありがとうございます';
		$data['MailContent']['subject_admin'] = 'お問い合わせを頂きました';
		$data['MailContent']['layout_template'] = 'default';
		$data['MailContent']['form_template'] = 'default';
		$data['MailContent']['mail_template'] = 'mail_default';
		$data['MailContent']['use_description'] = true;
		$data['MailContent']['auth_captcha'] = false;
		$data['MailContent']['ssl_on'] = false;
		$data['MailContent']['save_info'] = true;
		return $data;
	}

/**
 * afterSave
 *
 * @return boolean
 */
	public function afterSave($created, $options = array()) {
		// 検索用テーブルへの登録・削除
		if (!$this->data['Content']['exclude_search'] && $this->data['Content']['status']) {
			$this->saveSearchIndex($this->createSearchIndex($this->data));
		} else {
			$this->deleteSearchIndex($this->data['MailContent']['id']);
		}
	}

/**
 * beforeDelete
 *
 * @return	boolean
 * @access	public
 */
	public function beforeDelete($cascade = true) {
		return $this->deleteSearchIndex($this->id);
	}

/**
 * 検索用データを生成する
 *
 * @param array $data
 * @return array
 */
	public function createSearchIndex($data) {
		if (!isset($data['MailContent']) || !isset($data['Content'])) {
			return false;
		}
		$mailContent = $data['MailContent'];
		$content = $data['Content'];
		return ['SearchIndex' => [
			'type'		=> 'メール',
			'model_id'	=> (!empty($mailContent['id'])) ? $mailContent['id'] : $this->id,
			'content_id'=> $content['id'],
			'site_id'	=> $content['site_id'],
			'title'		=> $content['title'],
			'detail'	=> $mailContent['description'],
			'url'		=> $content['url'],
			'status'	=> $content['status']
		]];
	}

/**
 * メールコンテンツデータをコピーする
 *
 * @param int $id ページID
 * @param int $newParentId 新しい親コンテンツID
 * @param string $newTitle 新しいタイトル
 * @param int $newAuthorId 新しいユーザーID
 * @param int $newSiteId 新しいサイトID
 * @return mixed mailContent|false
 */
	public function copy($id, $newParentId, $newTitle, $newAuthorId, $newSiteId = null) {

		$data = $this->find('first', ['conditions' => ['MailContent.id' => $id], 'recursive' => 0]);
		$url = $data['Content']['url'];
		$siteId = $data['Content']['site_id'];
		$name = $data['Content']['name'];
		unset($data['MailContent']['id']);
		unset($data['MailContent']['created']);
		unset($data['MailContent']['modified']);
		unset($data['Content']);
		$data['Content'] = [
			'name'		=> $name,
			'parent_id'	=> $newParentId,
			'title'		=> $newTitle,
			'author_id' => $newAuthorId,
			'site_id' 	=> $newSiteId
		];
		if(!is_null($newSiteId) && $siteId != $newSiteId) {
			$data['Content']['site_id'] = $newSiteId;
			$data['Content']['parent_id'] = $this->Content->copyContentFolderPath($url, $newSiteId);
		}
		$this->getDataSource()->begin();
		if ($result = $this->save($data)) {
			$result['MailContent']['id'] = $this->id;
			$mailFields = $this->MailField->find('all', array('conditions' => array('MailField.mail_content_id' => $id), 'order' => 'MailField.sort', 'recursive' => -1));
			foreach ($mailFields as $mailField) {
				$mailField['MailField']['mail_content_id'] = $result['MailContent']['id'];
				$this->MailField->copy(null, $mailField, array('sortUpdateOff' => true));
			}
			App::uses('MailMessage', 'Mail.Model');
			$MailMessage = ClassRegistry::init('Mail.MailMessage');
			$MailMessage->setup($result['MailContent']['id']);
			$MailMessage->_sourceConfigured = true; // 設定しておかないと、下記の処理にて内部的にgetDataSouceが走る際にエラーとなってしまう。
			$MailMessage->construction($result['MailContent']['id']);
			$this->getDataSource()->commit();
			return $result;
		}
		$this->getDataSource()->rollback();
		return false;
	}

/**
 * フォームが公開中かどうかチェックする
 *
 * @param string $publishBegin 公開開始日時
 * @param string $publishEnd 公開終了日時
 * @return	bool
 */
	public function isAccepting($publishBegin, $publishEnd) {
		if ($publishBegin && $publishBegin != '0000-00-00 00:00:00') {
			if ($publishBegin > date('Y-m-d H:i:s')) {
				return false;
			}
		}
		if ($publishEnd && $publishEnd != '0000-00-00 00:00:00') {
			if ($publishEnd < date('Y-m-d H:i:s')) {
				return false;
			}
		}
		return true;
	}

/**
 * 公開済の conditions を取得
 *
 * @return array 公開条件（conditions 形式）
 */
	public function getConditionAllowAccepting() {
		$conditions[] = array('or' => array(array($this->alias . '.publish_begin <=' => date('Y-m-d H:i:s')),
			array($this->alias . '.publish_begin' => null),
			array($this->alias . '.publish_begin' => '0000-00-00 00:00:00')));
		$conditions[] = array('or' => array(array($this->alias . '.publish_end >=' => date('Y-m-d H:i:s')),
			array($this->alias . '.publish_end' => null),
			array($this->alias . '.publish_end' => '0000-00-00 00:00:00')));
		return $conditions;
	}

/**
 * 公開されたコンテンツを取得する
 *
 * @param Model $model
 * @param string $type
 * @param array $query
 * @return array|null
 */
	public function findAccepting($type = 'first', $query = []) {
		$getConditionAllowAccepting = $this->getConditionAllowAccepting();
		if(!empty($query['conditions'])) {
			$query['conditions'] = array_merge(
				$getConditionAllowAccepting,
				$query['conditions']
			);
		} else {
			$query['conditions'] = $getConditionAllowAccepting;
		}
		return $this->find($type, $query);
	}
	
}
