<?php

/* BlogPosts schema generated on: 2011-08-20 02:08:54 : 1313774094 */

class BlogPostsSchema extends CakeSchema {

	public $name = 'BlogPosts';

	public $file = 'blog_posts.php';

	public $connection = 'default';

	public function before($event = array()) {
		return true;
	}

	public function after($event = [])
	{
		$db = ConnectionManager::getDataSource($this->connection);
		if( get_class($db) !== 'BcMysql'){
			return true ;
		}

		if (isset($event['create'])) {
			switch ($event['create']) {
				case 'blogposts':
					$tableName = $db->config['prefix'] . 'blog_posts';
					$db->query("ALTER TABLE {$tableName} CHANGE content content LONGTEXT");
					$db->query("ALTER TABLE {$tableName} CHANGE content_draft content_draft LONGTEXT");
					$db->query("ALTER TABLE {$tableName} CHANGE detail detail LONGTEXT");
					$db->query("ALTER TABLE {$tableName} CHANGE detail_draft detail_draft LONGTEXT");
					break;
			}
		}
	}

	public $blog_posts = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'),
		'blog_content_id' => array('type' => 'integer', 'null' => true, 'length' => 8),
		'no' => array('type' => 'integer', 'null' => true),
		'name' => array('type' => 'string', 'null' => true, 'default' => null),
		'content' => array('type' => 'text', 'null' => true, 'default' => null),
		'detail' => array('type' => 'text', 'null' => true, 'default' => null),
		'blog_category_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 8),
		'status' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'posts_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'content_draft' => array('type' => 'text', 'null' => true, 'default' => null),
		'detail_draft' => array('type' => 'text', 'null' => true, 'default' => null),
		'publish_begin' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'publish_end' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'exclude_search' => array('type' => 'boolean', 'null' => true, 'default' => null),
		'eye_catch' => array('type' => 'text', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci')
	);

}
