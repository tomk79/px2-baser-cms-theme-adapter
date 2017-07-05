<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] 検索インデックス登録フォーム
 */
?>


<?php echo $this->BcForm->create('SearchIndex') ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SearchIndex.title', 'タイトル') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SearchIndex.title', array('type' => 'text', 'size' => 60, 'maxlength' => 255)) ?>
				<?php echo $this->BcForm->error('SearchIndex.title') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('SearchIndex.url', 'URL') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('SearchIndex.url', array('type' => 'text', 'size' => 60, 'maxlength' => 255, 'autofocus' => true)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpUrl', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('SearchIndex.url') ?>
				<div id="helptextUrl" class="helptext">
					<ul>
						<li>サイト内で検索インデックスとして登録したいURLを指定します。</li>
						<li>baserCMSの設置URL部分は省略する事ができます。<br />
							http://{baserCMS設置URL}/company/index<br />
							→ /company/index<br />
							<small>※ 省略時、スマートURLオフの場合、URL上の「/index.php」 は含めないようにします。</small>
						</li>
					</ul>
				</div>
			</td>
		</tr>
	</table>
</div>
<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'btnSave')) ?>
</div>

<?php echo $this->BcForm->end() ?>
