<?php
namespace Blog\Model\DataObjects\Lists;
use \Blog\Model\Abstracts\DataObjectList;
use \Blog\Model\DataObjects\Post;

class PostList extends DataObjectList {

#	@inherited
#	public $objects;
#	public $count;
#
#	private $new;
#	private $empty;

	const OBJECT_CLASS = Post::class;
	const OBJECTS_ALIAS = 'posts';


	const SELECT_QUERY = <<<SQL
SELECT * FROM posts
LEFT JOIN media ON medium_id = post_image_id
ORDER BY post_timestamp DESC
SQL; #---|


	const SELECT_IDS_QUERY = <<<SQL
SELECT * FROM posts
WHERE post_id IN 
SQL; #---|


	const COUNT_QUERY = <<<SQL
SELECT COUNT(*) FROM posts
SQL; #---|

}
?>
