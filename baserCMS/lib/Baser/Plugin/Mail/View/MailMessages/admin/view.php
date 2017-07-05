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
 * [ADMIN] 受信メール詳細
 */
?>


<!-- view -->
<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
	<tr><th>NO</th><td><?php echo $message['MailMessage']['id'] ?></td></tr>
	<tr><th>受信日時</th><td><?php echo $this->BcTime->format('Y/m/d H:i:s', $message['MailMessage']['created']) ?></td></tr>
	<?php
	$groupField = null;
	foreach ($mailFields as $key => $mailField) {
		$field = $mailField['MailField'];
		if ($field['use_field'] && $field['type'] != 'hidden') {
			$nextKey = $key + 1;
			/* 項目名 */
			if ($groupField != $field['group_field'] || (!$groupField && !$field['group_field'])) {
				echo '<tr>';
				echo '<th class="col-head" width="160">' . $field['head'] . '</th>';
				echo '<td class="col-input">';
			}
			if (!empty($message['MailMessage'][$mailField['MailField']['field_name']])) {
				echo $field['before_attachment'];
			}
			if (!$field['no_send']) {
				if($field['type'] == 'file') {
					echo $this->Maildata->control(
						$mailField['MailField']['type'], 
						$message['MailMessage'][$mailField['MailField']['field_name']], 
						$this->Mailfield->getOptions($mailField['MailField'])
					);
				} else {
					echo nl2br($this->BcText->autoLink($this->Maildata->control(
						$mailField['MailField']['type'], 
						$message['MailMessage'][$mailField['MailField']['field_name']], 
						$this->Mailfield->getOptions($mailField['MailField'])
					)));
				}
			}
			if (!empty($message['MailMessage'][$mailField['MailField']['field_name']])) {
				echo $field['after_attachment'];
			}
			echo '&nbsp;';
			if (($this->BcArray->last($mailFields, $key)) ||
				($field['group_field'] != $mailFields[$nextKey]['MailField']['group_field']) ||
				(!$field['group_field'] && !$mailFields[$nextKey]['MailField']['group_field']) ||
				($field['group_field'] != $mailFields[$nextKey]['MailField']['group_field'] && $this->BcArray->first($mailFields, $key))) {
				echo '</td></tr>';
			}
			$groupField = $field['group_field'];
		}
	}
	?>
</table>

<!-- button -->
<p class="submit">
<?php $this->BcBaser->link('削除', array('action' => 'delete', $mailContent['MailContent']['id'], $message['MailMessage']['id']), array('class' => 'submit-token btn-gray button'), sprintf('受信メール NO「%s」を削除してもいいですか？', $message['MailMessage']['id']), false); ?>
</p>
