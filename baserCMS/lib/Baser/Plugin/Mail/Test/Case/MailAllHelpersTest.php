<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Test.Case
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

class MailAllHelpersTest extends CakeTestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return CakeTestSuite
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Helper tests');
		$suite->addTestDirectory(dirname(__FILE__) . DS . 'View' . DS . 'Helper' . DS);
		return $suite;
	}

}
