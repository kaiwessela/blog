<?php
namespace Blog\Model\DataObjects;
use \Blog\Model\Abstracts\DataObject;
use \Blog\Model\Abstracts\DataObjects\Media\Application;
use \Blog\Model\Abstracts\DataObjects\Media\Audio;
use \Blog\Model\Abstracts\DataObjects\Media\Image;
use \Blog\Model\Abstracts\DataObjects\Media\Video;
use \Blog\Config\Config;
use \Blog\Config\MediaConfig;
use Exception;

abstract class Medium extends DataObject {
#	public string $class;
	public string $type;
	public string $extension;
	public ?string $title;
	public ?string $description;
	public ?string $copyright;
	public ?string $alternative;
	public ?array $variants;

#	@inherited
#	public string $id;
#	public string $longid;
#
#	private bool $new;
#	private bool $empty;
#	private bool $disabled;
#
#	const PAGINATABLE = false;

	protected ?array $files;

	const PROPERTIES = [
		'title' => '.{0,140}',
		'description' => '.{0,250}',
		'copyright' => '.{0,250}',
		'alternative' => '.{0,250}',
		'file' => 'custom',
		'type' => 'custom',
		'extension' => 'custom',
		'variants' => 'custom',
		'rewrite' => 'custom'
	];


	public static function create_and_load(array $data, bool $norecursion = false) : Medium {
		$class = $data['medium_class'] ?? $data[0]['medium_class'] ?? null;

		$object = match($class){
			'application' => new Application(),
			'audio' => new Audio(),
			'image' => new Image(),
			'video' => new Video(),
			default => throw new Exception('invalid class.')
		};

		$object->load($data, $norecursion);
		return $object;
	}


	public function load(array $data, bool $norecursion = false) : void {
		$this->require_empty();

		if(is_array($data[0])){
			$row = $data[0];
		} else {
			$row = $data;
		}

		if($row['medium_class'] != $this::$class){
			throw new Exception('class mismatch.');
		}

		$this->id = $row['medium_id'];
		$this->longid = $row['medium_longid'];
		$this->type = $row['medium_type'];
		$this->extension = $row['medium_extension'];
		$this->title = $row['medium_title'];
		$this->description = $row['medium_description'];
		$this->copyright = $row['medium_copyright'];
		$this->alternative = $row['medium_alternative'];
		$this->variants = empty($row['medium_variants'])
			? null : json_decode($row['medium_variants'], true, 512, \JSON_THROW_ON_ERROR);

		$this->set_not_new();
		$this->set_not_empty();
	}


	protected function check_class(string $class) : bool {
		return ($class == $this->class);
	}


	public function push() : void {
		$push_and_write = $this->is_new();

		parent::push();

		if($push_and_write){
			$this->write_file();
			$this->push_file();
		}
	}


	public function delete() : void {
		parent::delete();

		$this->erase_file();
	}


	protected function db_export() : array {
		$values = [
			'id' => $this->id,
			'title' => $this->title,
			'description' => $this->description,
			'copyright' => $this->copyright,
			'alternative' => $this->alternative,
			'variants' => json_encode($this->variants, \JSON_THROW_ON_ERROR)
		];

		if($this->is_new()){
			$values['longid'] = $this->longid;
			$values['class'] = static::$class;
			$values['type'] = $this->type;
			$values['extension'] = $this->extension;
		}

		return $values;
	}


	public function src(string $variant = 'original') : ?string {
		if(!in_array($variant, $this->variants)){
			return null;
		}

		$id_and_variant = $this->id . (($variant == 'original') ? '' : '_'.$variant);
		$longid_and_variant = $this->longid . (($variant == 'original') ? '' : '_'.$variant);

		return Config::SERVER_URL . DIRECTORY_SEPARATOR
			. MediaConfig::DIRECTORIES[$this::$class][0] . DIRECTORY_SEPARATOR
			. str_replace(
				['$ID&VARIANT', '$LONGID&VARIANT', '$ID', '$LONGID', '$EXTENSION'],
				[$id_and_variant, $longid_and_variant, $this->id, $this->longid, $this->extension],
				MediaConfig::DIRECTORIES[$this::$class][1]
			);
	}


	const INSERT_QUERY = <<<SQL
INSERT INTO media
(medium_id, medium_longid, medium_class, medium_type, medium_extension, medium_title, medium_description, medium_copyright,
medium_alternative, medium_variants)
VALUES (:id, :longid, :class, :type, :extension, :title, :description, :copyright, :alternative, :variants)
SQL; #---|

	const UPDATE_QUERY = <<<SQL
UPDATE media SET
	medium_title = :title,
	medium_description = :description,
	medium_copyright = :copyright,
	medium_alternative = :alternative,
	medium_variants = :variants
WHERE medium_id = :id
SQL; #---|

	const DELETE_QUERY = <<<SQL
DELETE FROM media
WHERE medium_id = :id
SQL; #---|

}
?>
