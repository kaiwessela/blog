<?php
namespace Blog\Model\DataObjects\Lists;
use \Blog\Model\Abstracts\DataObjectList;
use \Blog\Model\DataObjects\Group;

class GroupList extends DataObjectList {

	#	@inherited
	#	public $objects;
	#	public $count;
	#
	#	private $new;
	#	private $empty;

	const OBJECT_CLASS = Group::class;
	const OBJECTS_ALIAS = 'groups';


	const SELECT_QUERY = <<<SQL
SELECT * FROM groups
ORDER BY group_name
SQL; #---|


	const SELECT_IDS_QUERY = <<<SQL
SELECT * FROM groups
WHERE group_id IN 
SQL; #---|


	const COUNT_QUERY = <<<SQL
SELECT COUNT(*) FROM groups
SQL; #---|
}
?>
