<?php
namespace Blog\Model\DataObjects;
use \Blog\Model\Abstracts\DataObject;
use \Blog\Model\DataObjects\Media\Image;
use \Blog\Model\DataObjects\Lists\ColumnList;
use \Blog\Model\DataObjects\Relations\Lists\PostColumnRelationList;
use \Blog\Model\DataTypes\Timestamp;
use \Blog\Model\DataTypes\MarkdownContent;
use \Blog\Model\Abstracts\DataObjectCollection;

class Post extends DataObject {
	public ?string 					$overline;
	public string 					$headline;
	public ?string 					$subline;
	public ?string 					$teaser;
	public string 					$author;
	public Timestamp 				$timestamp;
	public ?Image 					$image;
	public ?MarkdownContent 		$content;
	public ?PostColumnRelationList 	$columnrelations;
	public ?DataObjectCollection	$collection;

#	@inherited
#	public string $id;
#	public string $longid;
#
#	private bool $new;
#	private bool $empty;
#	private bool $disabled;
#
#	const PAGINATABLE = false;

	const PROPERTIES = [
		'overline' => '.{0,50}',
		'headline' => '.{1,100}',
		'subline' => '.{0,100}',
		'teaser' => null,
		'author' => '.{1,100}',
		'timestamp' => Timestamp::class,
		'image' => Image::class,
		'content' => [
			'type' => MarkdownContent::class,
			'allow_html' => true,
			'collection' => 'collection'
		],
		'columnrelations' => PostColumnRelationList::class,
		'collection' => DataObjectCollection::class
	];

	const PSEUDOLISTS = [
		'columns' => [ColumnList::class, 'columnrelations']
	];


	public function load(array $data, bool $norecursion = false) : void {
		$this->require_empty();

		if(is_array($data[0])){
			$row = $data[0];
		} else {
			$row = $data;
		}

		$this->id = $row['post_id'];
		$this->longid = $row['post_longid'];
		$this->overline = $row['post_overline'];
		$this->headline = $row['post_headline'];
		$this->subline = $row['post_subline'];
		$this->teaser = $row['post_teaser'];
		$this->author = $row['post_author'];

		$this->timestamp = new Timestamp($row['post_timestamp']);

		$this->image = empty($row['medium_id']) ? null : new Image();
		$this->image?->load($data);

		$this->content = empty($row['post_content'])
		? null : new MarkdownContent($row['post_content']);

		$this->columnrelations = ($norecursion || empty($row['postcolumnrelation_id'])) ? null : new PostColumnRelationList();
		$this->columnrelations?->load($data, $this);

		$this->collection = (empty($row['post_collection'])) ? null : new DataObjectCollection(json_decode($row['post_collection'], true));
		$this->collection?->pull(); // TODO prevent pulling on multi

		$this->set_not_new();
		$this->set_not_empty();
	}


	protected function db_export() : array {
		$values = [
			'id' => $this->id,
			'overline' => $this->overline,
			'headline' => $this->headline,
			'subline' => $this->subline,
			'teaser' => $this->teaser,
			'author' => $this->author,
			'timestamp' => (string) $this->timestamp,
			'image_id' => $this->image?->id,
			'content' => (string) $this->content,
			// 'collection' => $this->colletion->db_export()
		];

		if($this->is_new()){
			$values['longid'] = $this->longid;
		}

		return $values;
	}


	const PULL_QUERY = <<<SQL
SELECT * FROM posts
LEFT JOIN media ON medium_id = post_image_id
LEFT JOIN postcolumnrelations ON postcolumnrelation_post_id = post_id
LEFT JOIN columns ON column_id = postcolumnrelation_column_id
WHERE post_id = :id OR post_longid = :id
SQL; #---|

	const INSERT_QUERY = <<<SQL
INSERT INTO posts (
	post_id,
	post_longid,
	post_overline,
	post_headline,
	post_subline,
	post_teaser,
	post_author,
	post_timestamp,
	post_image_id,
	post_content
) VALUES (
	:id,
	:longid,
	:overline,
	:headline,
	:subline,
	:teaser,
	:author,
	:timestamp,
	:image_id,
	:content
)
SQL; #---|

	const UPDATE_QUERY = <<<SQL
UPDATE posts SET
	post_overline = :overline,
	post_headline = :headline,
	post_subline = :subline,
	post_teaser = :teaser,
	post_author = :author,
	post_timestamp = :timestamp,
	post_image_id = :image_id,
	post_content = :content
WHERE post_id = :id
SQL; #---|

	const DELETE_QUERY = <<<SQL
DELETE FROM posts
WHERE post_id = :id
SQL; #---|

}
?>
