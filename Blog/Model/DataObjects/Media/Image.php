<?php
namespace Blog\Model\DataObjects\Media;
use \Blog\Model\DataObjects\Medium;
use \Blog\Model\FileManager;
use \Blog\Model\Exceptions\IllegalValueException;
use \Blog\Config\MediaConfig;

class Image extends Medium {
	public static string $class = 'image';


	protected function import_custom(string $property, array $data) : void {
		if($this->is_new() && $property == 'file'){
			$file = FileManager::receive('file');
		} else if(!$this->is_new() && $property == 'rewrite' && $data['rewrite'] == true){
			$file = FileManager::pull($this->id);
		} else {
			return;
		}

		$variants = [];

		if(!in_array($file->type, MediaConfig::IMAGE_TYPES)){
			throw new IllegalValueException('file', '', 'image');
		}

		$this->type = $file->type;
		$this->extension = $file->extension;
		$this->files['original'] = $file;
		$this->variants = ['original'];

		if(in_array($file->type, MediaConfig::RESIZABLE_IMAGE_TYPES)){
			foreach(array_keys(MediaConfig::IMAGE_RESIZE_WIDTHS) as $width){
				$resized = $file->resize($width, upscaling:false);

				if($resized != null){
					$this->files[$width] = $resized;
					$this->variants[] = $width;
				}
			}
		}

		if($property == 'rewrite'){
			FileManager::erase($this, true);
			$this->write_file();
		}
	}


	protected function write_file() : void {
		foreach($this->files as $variant => $file){
			FileManager::write($file, $this, $variant);
		}
	}


	protected function push_file() : void {
		FileManager::push($this->files['original'], $this);
	}


	protected function erase_file() : void {
		FileManager::erase($this, true);
	}


	public function srcset() : ?string {
		$sources = [];

		foreach($this->variants as $variant){
			if(empty(MediaConfig::IMAGE_RESIZE_WIDTHS[$variant])){
				continue;
			}

			$sources[] = $this->src($variant).' '.MediaConfig::IMAGE_RESIZE_WIDTHS[$variant][0].'w';
		}

		return implode(', ', $sources);
	}


	const PULL_QUERY = <<<SQL
SELECT * FROM media WHERE medium_class = 'image' AND
(medium_id = :id OR medium_longid = :id)
SQL; #---|

}
?>
