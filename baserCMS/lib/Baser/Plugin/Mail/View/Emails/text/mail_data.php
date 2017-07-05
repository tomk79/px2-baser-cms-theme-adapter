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
 * [EMAIL] メール送信データ
 */
$group_field = null;
foreach ($mailFields as $field) {
	$field = $field['MailField'];
	if ($field['use_field'] && $field['type'] != 'file' && isset($message[$field['field_name']]) && ($group_field != $field['group_field'] || (!$group_field && !$field['group_field']))) {
?>


◇◆ <?php echo $field['head']; ?> 
----------------------------------------
<?php
	}
	if ($field['type'] != 'file' && !empty($field['before_attachment']) && isset($message[$field['field_name']])) {
		echo " " . $field['before_attachment'];
	}
	if ($field['type'] != 'file' && isset($message[$field['field_name']]) && !$field['no_send'] && $field['use_field']) {
		echo $this->Maildata->control($field['type'], $message[$field['field_name']], $this->Mailfield->getOptions($field));
	}
	if ($field['type'] != 'file' && !empty($field['after_attachment']) && isset($message[$field['field_name']])) {
		echo " " . $field['after_attachment'];
	}
	$group_field = $field['group_field'];
}
?>