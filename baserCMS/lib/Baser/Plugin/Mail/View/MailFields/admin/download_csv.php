<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] CSVダウンロード
 */
$this->BcCsv->encoding = $encoding;
$this->BcCsv->addModelDatas('MailMessage' . $mailContent['MailContent']['id'], $messages);
$this->BcCsv->download($contentName);
