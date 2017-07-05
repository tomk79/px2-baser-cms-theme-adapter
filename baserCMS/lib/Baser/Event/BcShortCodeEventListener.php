<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Event
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * baserCMS Short Code Event Listener
 *
 * @package Baser.Event
 */
class BcShortCodeEventListener extends Object implements CakeEventListener {

/**
 * Implemented Events
 *
 * @return array
 */
	public function implementedEvents() {
		return [
			'View.afterRender' => ['callable' => 'afterRender']
		];
	}

/**
 * After Render
 * 
 * @param CakeEvent $event
 */
	public function afterRender(CakeEvent $event) {
		if(BcUtil::isAdminSystem()) {
			return;
		}
		$View = $event->subject();
		$this->_execShortCode($View);
	}

/**
 * ショートコードを実行する
 * 
 * @param View $View
 */
	protected function _execShortCode($View) {
		$shortCodes = Configure::read('BcShortCode');
		if(!$shortCodes) {
			return;
		}
		$output = $View->output;
		if(!is_array($shortCodes)) {
			$shortCodes = [$shortCodes];
		}
		foreach($shortCodes as $plugin => $values) {
			foreach ($values as $shortCode) {
				$func = explode('.', $shortCode);
				if (empty($func[0]) || empty($func[1])) {
					continue;
				}
				$regex = '/(\[' . preg_quote($shortCode, '/') . '(|\s(.*?))\])/';
				if (preg_match($regex, $output, $matches)) {
					$target = $matches[1];
					$args = [];
					if (!empty($matches[3])) {
						$args = explode(',', $matches[3]);
						foreach ($args as $key => $value) {
							if (strpos($value, '|') !== false) {
								$args[$key] = call_user_func_array('aa', explode("|", $value));
							}
						}
					}
					if (isset($View->{$func[0]})) {
						$Helper = $View->{$func[0]};
					} else {
						if($plugin == 'Core') {
							$plugin = '';
						} else {
							$plugin .= '.';
						}
						$className = $func[0] . 'Helper';
						App::uses($className, $plugin . 'View/Helper');
						$Helper = new $className($View);
					}
					$result = call_user_func_array(array($Helper, $func[1]), $args);
					$output = str_replace($target, $result, $output);
				}
			}
		}	
		$View->output = $output;
	}

}
