<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] 統合コンテンツ一覧
 */
?>


<ul>
	<?php foreach($datas as $data): ?>
		<?php
		$type = $data['Content']['type'];
		$treeItemType = 'default';
		if($type == 'ContentFolder') {
			$treeItemType = 'folder';
		}
		$fullUrl = $this->BcContents->getUrl($data['Content']['url'], true, $data['Site']['use_subdomain']);
		$parentId = $data['Content']['parent_id'];
		$alias = false;
		$open = false;
		if(!empty($this->BcContents->settings[$type]['icon'])) {
			$iconPath = $this->BcContents->settings[$type]['url']['icon'];
		} else {
			$iconPath = $this->BcContents->settings['Default']['url']['icon'];
		}
		if($data['Content']['alias_id']) {
			$alias = true;
		}
		$status = $this->BcContents->isAllowPublish($data, true);
		if($data['Content']['site_root']) {
			$open = true;
		}
		$editDisabled = !$this->BcContents->isActionAvailable($data['Content']['type'], 'edit', $data['Content']['entity_id']);
		$manageDisabled = !$this->BcContents->isActionAvailable($data['Content']['type'], 'manage', $data['Content']['entity_id']);
		$editInIndexDisabled
		?>
<li id="node-<?php echo $data['Content']['id'] ?>" data-jstree='{
	"icon":"<?php echo $iconPath ?>",
	"type":"<?php echo $treeItemType ?>",
	"status":"<?php echo (bool) $status ?>",
	"alias":"<?php echo (bool) $alias ?>",
	"related":"<?php echo (bool) $this->BcContents->isSiteRelated($data) ?>",
	"contentId":"<?php echo $data['Content']['id'] ?>",
	"contentParentId":"<?php echo $parentId ?>",
	"contentEntityId":"<?php echo $data['Content']['entity_id'] ?>",
	"contentSiteId":"<?php echo $data['Content']['site_id'] ?>",
	"contentFullUrl":"<?php echo $fullUrl ?>",
	"contentType":"<?php echo $type ?>",
	"contentAliasId":"<?php echo $data['Content']['alias_id'] ?>",
	"contentPlugin":"<?php echo $data['Content']['plugin'] ?>",
	"contentTitle":"<?php echo addslashes(strip_tags(h($data['Content']['title']))) ?>",
	"contentSiteRoot":"<?php echo (bool) $data['Content']['site_root'] ?>",
	"editDisabled":"<?php echo $editDisabled ?>",
	"manageDisabled":"<?php echo $manageDisabled ?>"
}'<?php if($open): ?> class="jstree-open"<?php endif ?>>
			<span><?php echo strip_tags(h($data['Content']['title'])) ?></span>
			<?php if(!empty($data['children'])): ?>
				<?php $this->BcBaser->element('admin/contents/index_list_tree', array('datas' => $data['children'])) ?>
			<?php endif ?>
		</li>
	<?php endforeach ?>
</ul>

