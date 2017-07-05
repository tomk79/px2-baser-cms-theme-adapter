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
 * [PUBLISH] ページネーション標準
 * 
 * $this->BcBaser->pagination() で呼び出す
 */
?>


<div class="pagination">
	<div class="pagination-result">
		<?php echo $this->Paginator->counter(array('format' => '結果：　%start%～%end% 件 ／ 総件数：　%count% 件')) ?>
	</div>
	<div class="pagination-numbers">
		<?php echo $this->Paginator->first('|<') ?>　
		<?php echo $this->Paginator->prev('<<', null, null, array('class' => array('disabled', 'number'), 'tag' => 'span')) ?>　
		<?php echo $this->Paginator->numbers() ?>　
		<?php echo $this->Paginator->next('>>', null, null, array('class' => 'disabled', 'tag' => 'span')) ?>　
		<?php echo $this->Paginator->last('>|') ?>
	</div>
</div>