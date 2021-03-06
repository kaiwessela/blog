<?php
namespace Blog\Model\DataObjects;
use \Blog\Model\Abstracts\DataObject;
use \Blog\Model\DataTypes\Timestamp;
use \Blog\Model\DataTypes\MarkdownContent;

class Event extends DataObject {
	public string 			$title;
	public string 			$organisation;
	public Timestamp 		$timestamp;
	public ?string 			$location;
	public ?MarkdownContent $description;
	public ?bool 			$cancelled;

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
		'title' => '.{1,100}',
		'organisation' => '.{1,60}',
		'timestamp' => Timestamp::class,
		'location' => '.{0,100}',
		'description' => MarkdownContent::class,
		'cancelled' => null
	];


	public function load(array $data, bool $norecursion = false) : void {
		$this->require_empty();

		if(is_array($data[0])){
			$row = $data[0];
		} else {
			$row = $data;
		}

		$this->id = $row['event_id'];
		$this->longid = $row['event_longid'];
		$this->title = $row['event_title'];
		$this->organisation = $row['event_organisation'];
		$this->timestamp = new Timestamp($row['event_timestamp']);
		$this->location = $row['event_location'];
		$this->cancelled = (bool) $row['event_cancelled'];

		$this->description = empty($row['event_description'])
		? null : new MarkdownContent($row['event_description']);

		$this->set_not_new();
		$this->set_not_empty();
	}


	protected function db_export() : array {
		$values = [
			'id' => $this->id,
			'title' => $this->title,
			'organisation' => $this->organisation,
			'timestamp' => (string) $this->timestamp,
			'location' => $this->location,
			'description' => $this->description,
			'cancelled' => (int) $this->cancelled
		];

		if($this->is_new()){
			$values['longid'] = $this->longid;
		}

		return $values;
	}


	const PULL_QUERY = <<<SQL
SELECT * FROM events
WHERE event_id = :id OR event_longid = :id
SQL; #---|


	const INSERT_QUERY = <<<SQL
INSERT INTO events (
	event_id,
	event_longid,
	event_title,
	event_organisation,
	event_timestamp,
	event_location,
	event_description,
	event_cancelled
) VALUES (
	:id,
	:longid,
	:title,
	:organisation,
	:timestamp,
	:location,
	:description,
	:cancelled
)
SQL; #---|


	const UPDATE_QUERY = <<<SQL
UPDATE events SET
	event_title = :title,
	event_organisation = :organisation,
	event_timestamp = :timestamp,
	event_location = :location,
	event_description = :description,
	event_cancelled = :cancelled
WHERE event_id = :id
SQL; #---|


	const DELETE_QUERY = <<<SQL
DELETE FROM events
WHERE event_id = :id
SQL; #---|

}
?>
