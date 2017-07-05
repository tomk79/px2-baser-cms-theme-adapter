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

App::uses('BcAppHelper', 'View/Helper');

/**
 * アップロードヘルパー
 *
 * @package Baser.View.Helper
 */
class BcUploadHelper extends BcAppHelper {

/**
 * ヘルパ
 * 
 * @var array
 */
	public $helpers = array('Html', 'BcForm');
	
/**
 * ファイルへのリンクを取得する
 *
 * @param string $fieldName
 * @param array $options
 * @return string
 */
	public function fileLink($fieldName, $options = array()) {
		$options = array_merge(array(
			'imgsize' => 'medium', // 画像サイズ
			'rel' => '', // rel属性
			'title' => '', // タイトル属性
			'link' => true, // 大きいサイズの画像へのリンク有無
			'force' => false,
			'width' => '', // 横幅
			'height' => '', // 高さ
			), $options);

		extract($options);

		if(strpos($fieldName, '.') === false) {
			throw new BcException('BcUploadHelper を利用するには、$fieldName に、モデル名とフィールド名をドットで区切って指定する必要があります。');
		}
		$this->setEntity($fieldName);
		$field = $this->field();

		$tmp = false;
		$Model = ClassRegistry::init($this->model());

		try{
			$settings = $this->getBcUploadSetting();
		} catch (BcException $e){
			throw $e ;
		}

		// EVENT BcUpload.beforeFileLInk
		$event = $this->dispatchEvent('beforeFileLink', [
			'formId' => $this->__id,
			'settings' => $settings,
			'fieldName' => $fieldName,
			'options' => $options
		], ['class' => 'BcUpload', 'plugin' => '']);
		if ($event !== false) {
			$options = ($event->result === null || $event->result === true) ? $event->data['options'] : $event->result;
			$settings = $event->data['settings'];
		}

		$this->setBcUploadSetting($settings);

		$basePath = '/files/' . str_replace(DS, '/', $settings['saveDir']) . '/';

		if (empty($options['value'])) {
			$value = $this->value($fieldName);
		} else {
			$value = $options['value'];
		}

		if (is_array($value)) {
			if (empty($value['session_key']) && empty($value['name'])) {
				$data = $Model->findById($Model->id);
				if (!empty($data[$Model->alias][$field])) {
					$value = $data[$Model->alias][$field];
				} else {
					$value = '';
				}
			} else {
				if (isset($value['session_key'])) {
					$tmp = true;
					$value = str_replace('/', '_', $value['session_key']);
					$basePath = '/uploads/tmp/';
				} else {
					return false;
				}
			}
		}

		/* ファイルのパスを取得 */
		/* 画像の場合はサイズを指定する */
		if (isset($settings['saveDir'])) {
			if ($value && !is_array($value)) {
				$uploadSettings = $settings['fields'][$field];
				$ext = decodeContent('', $value);
				if ($uploadSettings['type'] == 'image' || in_array($ext, $Model->Behaviors->BcUpload->imgExts)) {
					$options = array(
						'imgsize' => $imgsize, 
						'rel' => $rel, 
						'title' => $title, 
						'link' => $link, 
						'force' => $force,
						'width' => $width, // 横幅
						'height' => $height // 高さ
					);
					if ($tmp) {
						$options['tmp'] = true;
					}
					$out = $this->uploadImage($fieldName, $value, $options) . '<br /><span class="file-name">' . mb_basename($value) . '</span>';
				} else {
					$filePath = $basePath . $value;
					$out = $this->Html->link('ダウンロード ≫', $filePath, array('target' => '_blank')) . '<br /><span class="file-name">' . mb_basename($value) . '</span>';
				}
			} else {
				$out = $value;
			}
		} else {
			$out = false;
		}

		// EVENT BcUpload.afterFileLink
		$event = $this->dispatchEvent('afterFileLink', [
			'data' => $this->request->data,
			'fieldName' => $fieldName,
			'out' => $out
		], ['class' => 'BcUpload', 'plugin' => '']);
		if ($event !== false) {
			$out = ($event->result === null || $event->result === true) ? $event->data['out'] : $event->result;
		}

		return $out;
	}

/**
 * アップロードした画像のタグをリンク付きで出力する
 * Uploadビヘイビアの設定による
 * 上から順に大きい画像を並べている事が前提で
 * 指定したサイズ内で最大の画像を出力
 * リンク先は存在する最大の画像へのリンクとなる
 *
 * @param string $fieldName
 * @param string $fileName
 * @param array $options
 * @return string
 */
	public function uploadImage($fieldName, $fileName, $options = array()) {
		$options = array_merge([
			'imgsize' => 'medium', // 画像サイズ
			'link' => true, // 大きいサイズの画像へのリンク有無
			'escape' => false, // エスケープ
			'mobile' => false, // モバイル
			'alt' => '', // alt属性
			'width' => '', // 横幅
			'height' => '', // 高さ
			'noimage' => '', // 画像がなかった場合に表示する画像
			'tmp' => false,
			'force' => false,
			'output' => '', // 出力タイプ tag ,url を指定、未指定(or false)の場合は、tagで出力(互換性のため)
			'limited' => false // 公開制限フォルダを利用する場合にフォルダ名を設定する
		], $options);

		$this->setEntity($fieldName);
		$field = $this->field();
		
		try{
			$settings = $this->getBcUploadSetting();
		} catch (BcException $e){
			throw $e ;
		}

		// EVENT BcUpload.beforeUploadImage
		$event = $this->dispatchEvent('beforeUploadImage', [
			'formId' => $this->__id,
			'settings' => $settings,
			'fieldName' => $fieldName,
			'options' => $options
		], ['class' => 'BcUpload', 'plugin' => '']);
		if ($event !== false) {
			$options = ($event->result === null || $event->result === true) ? $event->data['options'] : $event->result;
			$settings = $event->data['settings'];
		}

		$this->setBcUploadSetting($settings);
		
		$imgOptions = [
			'alt' => $options['alt'],
			'width' => $options['width'],
			'height' => $options['height']
		];
		if ($imgOptions['width'] === '') {
			unset($imgOptions['width']);
		}
		if ($imgOptions['height'] === '') {
			unset($imgOptions['height']);
		}
		$linkOptions = [
			'rel' => 'colorbox',
			'escape' => $options['escape']
		];

		if (is_array($fileName)) {
			if (isset($fileName['session_key'])) {
				$fileName = $fileName['session_key'];
				$options['tmp'] = true;
			} else {
				return '';
			}
		}

		if ($options['noimage']) {
			if (!$fileName) {
				$fileName = $options['noimage'];
			}
		} else {
			if (!$fileName) {
				return '';
			}
		}

		if (strpos($fieldName, '.') === false) {
			trigger_error('フィールド名は、 ModelName.field_name で指定してください。', E_USER_WARNING);
			return false;
		}

		$fileUrl = $this->getBasePath($settings);
		$Model = $this->getUploadModel();
		$saveDir = $Model->getSaveDir(false, $options['limited']);
		$saveDirInTheme = $Model->getSaveDir(true, $options['limited']);

		if (isset($settings['fields'][$field]['imagecopy'])) {
			$copySettings = $settings['fields'][$field]['imagecopy'];
		} else {
			$copySettings = "";
		}

		if ($options['tmp']) {
			$options['link'] = false;
			$fileUrl = '/uploads/tmp/';
			if ($options['imgsize']) {
				$fileUrl .= $options['imgsize'] . '/';
			}
		}

		if ($fileName == $options['noimage']) {
			$mostSizeUrl = $fileName;
		} elseif ($options['tmp']) {
			$mostSizeUrl = $fileUrl . str_replace(array('.', '/'), array('_', '_'), $fileName);
		} else {
			$check = false;
			$maxSizeExists = false;
			$mostSizeExists = false;

			if ($copySettings) {

				foreach ($copySettings as $key => $copySetting) {

					if ($key == $options['imgsize']) {
						$check = true;
					}

					if (isset($copySetting['mobile'])) {
						if ($copySetting['mobile'] != $options['mobile']) {
							continue;
						}
					} else {
						if ($options['mobile'] != preg_match('/^mobile_/', $key)) {
							continue;
						}
					}

					$imgPrefix = '';
					$imgSuffix = '';

					if (isset($copySetting['suffix'])) {
						$imgSuffix = $copySetting['suffix'];
					}
					if (isset($copySetting['prefix'])) {
						$imgPrefix = $copySetting['prefix'];
					}
					
					$pathinfo = pathinfo($fileName);
					$ext = $pathinfo['extension'];
					$basename = basename($fileName, '.' . $ext);

					$subdir = str_replace($basename . '.' . $ext, '', $fileName);
					$file = str_replace('/', DS, $subdir) . $imgPrefix . $basename . $imgSuffix . '.' . $ext;
					if ((file_exists($saveDir . $file) || file_exists($saveDirInTheme . $file)) || $options['force']) {
						if ($check && !$mostSizeExists) {
							$mostSizeUrl = $fileUrl . $subdir . $imgPrefix . $basename . $imgSuffix . '.' . $ext . '?' . rand();
							$mostSizeExists = true;
						} elseif (!$mostSizeExists && !$maxSizeExists) {
							$maxSizeUrl = $fileUrl . $subdir . $imgPrefix . $basename . $imgSuffix . '.' . $ext . '?' . rand();
							$maxSizeExists = true;
						}
					}
				}
			}

			if (!isset($mostSizeUrl)) {
				$mostSizeUrl = $fileUrl . $fileName . '?' . rand();
			}
			if (!isset($maxSizeUrl)) {
				$maxSizeUrl = $fileUrl . $fileName . '?' . rand();
			}
		}

		$output = $options['output'];
		$link = $options['link'];
		$noimage = $options['noimage'];
		unset($options['imgsize']);
		unset($options['link']);
		unset($options['escape']);
		unset($options['mobile']);
		unset($options['alt']);
		unset($options['width']);
		unset($options['height']);
		unset($options['noimage']);
		unset($options['tmp']);
		unset($options['force']);
		unset($options['output']);
		
		switch($output){
			case 'url' :
				$out = $mostSizeUrl;
				break;
			case 'tag' :
				$out = $this->Html->image($mostSizeUrl, array_merge($options, $imgOptions));
				break;
			default :
				if ($link && !($noimage == $fileName)) {
					$out = $this->Html->link($this->Html->image($mostSizeUrl, $imgOptions), $maxSizeUrl, array_merge($options, $linkOptions));
				} else {
					$out = $this->Html->image($mostSizeUrl, array_merge($options, $imgOptions));
				}
		}

		// EVENT BcUpload.afterUploadImage
		$event = $this->dispatchEvent('afterUploadImage', [
			'data' => $this->request->data,
			'fieldName' => $fieldName,
			'out' => $out
		], ['class' => 'BcUpload', 'plugin' => '']);
		if ($event !== false) {
			$out = ($event->result === null || $event->result === true) ? $event->data['out'] : $event->result;
		}
		return $out;
	}

/**
 * アップロード先のベースパスを取得
 *
 * @param string $fieldName 格納されているDBのフィールド名、ex) BlogPost.eye_catch
 * @return string パス
 */
	public function getBasePath($settings = null) {
		if(! $settings){
			try{
				$settings = $this->getBcUploadSetting();
			} catch (BcException $e){
				throw $e ;
			}
		}
		return '/files/' . str_replace(DS, '/', $settings['saveDir']) . '/';
	}

/**
 * アップロードの設定を取得する
 *
 * @param string $modelName
 * @return array
 */
	protected function getBcUploadSetting(){
		$Model = $this->getUploadModel();
		return $Model->Behaviors->BcUpload->settings[$Model->name];
	}

	protected function setBcUploadSetting($settings){
		$Model = $this->getUploadModel();
		$Model->Behaviors->BcUpload->settings[$Model->name] = $settings;
	}
	
	protected function getUploadModel() {
		$modelName = $this->model();
		$Model = ClassRegistry::init($modelName);
		if (empty($Model->Behaviors->BcUpload)) {
			throw new BcException('BcUploadHelper を利用するには、モデルで BcUploadBehavior の利用設定が必要です。');
		}
		return $Model;
	}
	
}
