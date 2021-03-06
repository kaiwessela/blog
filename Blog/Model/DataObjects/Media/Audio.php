<?php
namespace Blog\Model\DataObjects\Media;
use \Blog\Model\DataObjects\Medium;
use \Blog\Model\FileManager;
use \Blog\Model\Exceptions\IllegalValueException;
use \Blog\Config\MediaConfig;

class Audio extends Medium {
	public static string $class = 'audio';


	protected function import_custom(string $property, array $data) : void {
		if($property != 'file' || !$this->is_new()){
			return;
		}

		$file = FileManager::receive('file');
		$variants = [];

		if(!in_array($file->type, MediaConfig::AUDIO_TYPES)){
			throw new IllegalValueException('file', '', 'audio');
		}

		$this->type = $file->type;
		$this->extension = $file->extension;
		$this->files['original'] => $file;
		$this->variants = ['original'];
	}


	protected function write_file() : void {
		FileManager::write($this->files['original'], $this);
	}

	protected function push_file() : void {
		FileManager::push($this->files['original'], $this);
	}

	protected function erase_file() : void {
		FileManager::erase($this, true);
	}


	const PULL_QUERY = <<<SQL
SELECT * FROM media WHERE medium_class = 'audio' AND
(medium_id = :id OR medium_longid = :id)
SQL; #---|

}
?>
