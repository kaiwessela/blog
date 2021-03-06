<?php
namespace Blog\Model;
use \Blog\Config\Config;
use \Blog\Config\MediaConfig;
use \Blog\Model\DataObjects\Medium;
use \Blog\Model\FileManager\File;
use \Blog\Model\Exceptions\DatabaseException;
use \Blog\Model\Exceptions\EmptyResultException;
use Exception;
use JsonException;
use PDO;
use finfo;

class FileManager {

	public static function receive(string $property) : File {
		if($_SERVER['CONTENT_TYPE'] === 'application/json'){
			$mode = 'json';
		} else if(preg_match('/^multipart\/form-data;/', $_SERVER['CONTENT_TYPE'])){
			$mode = 'post';
		} else {
			throw new Exception('FileManager | Upload » invalid request Content-Type.');
		}

		$mimeinfo = new finfo(FILEINFO_MIME_TYPE);
		// $extninfo = new finfo(FILEINFO_EXTENSION);

		if($mode == 'post'){
			if(!isset($_FILES[$property]['error']) || is_array($_FILES[$property]['error'])){
				// invalid file data (undefined or multiple files or files corruption attack)
				throw new Exception('FileManager | Upload » invalid or unreadable file upload.');
			}

			if($_FILES[$property]['size'] > MediaConfig::MAX_UPLOAD_SIZE){
				// IDEA maybe special exception that can be caught by the import function because this is a common issue
				throw new Exception('FileManager | Upload » file exceeds config MAX_UPLOAD_SIZE.');
			}

			if($_FILES[$property]['error'] !== UPLOAD_ERR_OK){
				throw new Exception('FileManager ' . match($_FILES[$property]['error']){
					UPLOAD_ERR_INI_SIZE => '| Upload » file exceeds php.ini upload_max_filesize.',
					UPLOAD_ERR_FORM_SIZE => '| Upload » file exceeds form MAX_FILE_SIZE.',
					UPLOAD_ERR_PARTIAL => '| Upload » file was only partially uploaded.',
					UPLOAD_ERR_NO_FILE => '| Upload » no file was uploaded.',
					UPLOAD_ERR_NO_TMP_DIR => '| System » temporary folder missing.',
					UPLOAD_ERR_CANT_WRITE => '| System » failed to write file to disk.',
					UPLOAD_ERR_EXTENSION => '| System » upload stopped by php extension.',
					default => '| Upload » unknown upload error.'
				});
			}

			$filedata = file_get_contents($_FILES[$property]['tmp_name']);

			$sent_mime = $_FILES[$property]['type'];
			$sent_extension = array_reverse(explode('.', $_FILES[$property]['name']))[0];

			$finfo_mime = $mimeinfo->file($_FILES[$property]['tmp_name']);

		} else if($mode == 'json'){
			try {
				$input = json_decode(file_get_contents('php://input'), true, 512, \JSON_THROW_ON_ERROR);
			} catch(JsonException $e){
				throw new Exception('FileManager | Upload » json decoding failed.');
			}

			$filedata_raw = $input[$property];
			if(empty($filedata_raw)){
				throw new Exception('FileManager | Upload » no file was uploaded.');
			}

			preg_match('/^data:(.+);base64,/', $filedata_raw, $matches);
			$sent_mime = $matches[1] ?? null;

			$filedata_b64 = preg_replace('/^data:(.+);base64,/', '', $filedata_raw);
			if(empty($filedata_b64)){
				throw new Exception('FileManager | Upload » no file was uploaded.');
			}

			$filedata = base64_decode($filedata_b64, true);
			if($filedata === false){
				throw new Exception('FileManager | Upload » invalid base64 encoding.');
			}

			$finfo_mime = $mimeinfo->buffer($filedata);
		}

		if($sent_mime !== $finfo_mime){
			throw new Exception('FileManager | Upload » sent and read mime types do not match.');
		}

		if(empty($sent_extension) || !in_array($sent_extension, self::EXTENSIONS[$finfo_mime])){
			$extension = self::EXTENSIONS[$finfo_mime][0];
		} else {
			$extension = $sent_extension;
		}

		return new File($finfo_mime, $extension, $filedata);
	}


	public static function pull(string $id, string $variant = 'original') : File {
		$pdo = new PDO(
			'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME,
			Config::DB_USER,
			Config::DB_PASSWORD,
			[PDO::ATTR_PERSISTENT => true]
		);

		$s = $pdo->prepare(self::PULL_QUERY);
		if(!$s->execute(['id' => $id, 'variant' => $variant])){
			throw new DatabaseException($s);
		} else if($s->rowCount() != 1){
			throw new EmptyResultException('', '');
		} else {
			$r = $s->fetch();
			return new File($r['mediafile_type'], $r['mediafile_extension'], $r['mediafile_data']);
		}
	}


	public static function pull_all(string $id) : array {
		$pdo = new PDO(
			'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME,
			Config::DB_USER,
			Config::DB_PASSWORD,
			[PDO::ATTR_PERSISTENT => true]
		);

		$s = $pdo->prepare(self::PULL_ALL_QUERY);
		if(!$s->execute(['id' => $id])){
			throw new DatabaseException($s);
		} else if($s->rowCount() == 0){
			throw new EmptyResultException('', '');
		} else {
			$res = [];
			while($r = $s->fetch()){
				$file = new File($r['mediafile_type'], $r['mediafile_extension'], $r['mediafile_data']);

				if($r['mediafile_variant'] == null){
					$res['original'] = $file;
				} else {
					$res[$r['mediafile_variant']] = $file;
				}
			}
			return $res;
		}
	}


	public static function push(File $file, Medium $medium, string $variant = 'original') : void {
		$pdo = new PDO(
			'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME,
			Config::DB_USER,
			Config::DB_PASSWORD,
			[PDO::ATTR_PERSISTENT => true]
		);

		$values = [
			'id' => $medium->id,
			'variant' => $variant,
			'type' => $file->type,
			'extension' => $medium->extension,
			'data' => $file->data
		];

		$s = $pdo->prepare(self::PUSH_QUERY);
		if(!$s->execute($values)){
			throw new DatabaseException($s);
		}
	}


	public static function write(File $file, Medium $medium, string $variant = 'original') : void {
		$filename = self::filename($medium, $variant);
		$directory = preg_replace('/\/[^\/]*$/', '', $filename);

		self::mkdir($directory);

		if(!is_writable($directory)){
			throw new Exception("FileManager | Write » directory is not writable: $directory.");
		}

		if(file_put_contents($filename, $file->data) === false){
			throw new Exception("FileManager | Write » failed to write $filename.");
		}
	}


	public static function erase(Medium $medium, bool $all = false, string $variant = 'original') : void {
		if($all){
			$filename = preg_replace('/\/[^\/]*$/', '', self::filename($medium, $variant));
		} else {
			$filename = self::filename($medium, $variant);
		}

		if(file_exists($filename)){
			self::rm($filename);
		}
	}


	private static function filename(Medium $medium, string $variant) : string {
		$dirconfig = MediaConfig::DIRECTORIES[$medium::$class];

		if(!is_array($dirconfig) || !isset($dirconfig[0]) || !isset($dirconfig[1])){
			throw new Exception("FileManager | Config » illegal value: DIRECTORIES.");
		}

		if(preg_match('/\.\./', $dirconfig[0] . '/' . $dirconfig[1])){
			throw new Exception("FileManager | Config » illegal directory upwards navigation.");
		}

		if($variant == 'original'){
			$variant = null;
		}

		$id_and_variant = $medium->id . (($variant === null) ? '' : '_'.$variant);
		$longid_and_variant = $medium->longid . (($variant === null) ? '' : '_'.$variant);

		return $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $dirconfig[0] . DIRECTORY_SEPARATOR
			. str_replace(['$ID&VARIANT', '$LONGID&VARIANT', '$ID', '$LONGID', '$EXTENSION'],
			[$id_and_variant, $longid_and_variant, $medium->id, $medium->longid, $medium->extension],
			$dirconfig[1]);
	}


	public static function scan(Medium $medium) : array {
		if($medium->is_new()){
			throw new Exception("FileManager | Scan » medium must not be new.");
		}

		$result = [];

		try {
			$result['db'] = self::pull_all($medium->id);
		} catch(EmptyResultException $e){
			$result['db'] = null;
		}

		$finfo = new finfo(FILEINFO_MIME_TYPE);

		foreach($medium->variants as $variant){
			$filename = self::filename($medium, $variant);
			$result['storage'][$variant] = [
				'found' => file_exists($filename),
				'mime' => @$finfo->file($filename)
			];
		}

		return $result;
	}


	private static function rm(string $path) {
		if(!file_exists($path)){
			throw new Exception("FileManager | rm » file or directory not found: $path.");
		}

		if(!is_writable($path)){
			throw new Exception("FileManager | rm » file or directory not writable: $path.");
		}

		if(is_dir($path)){
			$children = scandir($path);

			if(!empty($children)){
				foreach($children as $child){
					if($child === '.' || $child === '..'){
						continue;
					}

					self::rm($path . DIRECTORY_SEPARATOR . $child);
				}
			}

			if(!rmdir($path)){
				throw new Exception("FileManager | rm » failed to remove $path.");
			}
		} else {
			if(!unlink($path)){
				throw new Exception("FileManager | rm » failed to remove $path.");
			}
		}
	}


	private static function mkdir(string $path) : void {
		if(file_exists($path)){
			if(!is_dir($path)){
				throw new Exception("FileManager | mkdir » $path exists but is not a directory.");
			}

			return;
		}

		if(!mkdir($path, recursive:true)){
			throw new Exception("FileManager | mkdir » failed to create $path.");
		}
	}


	const PULL_QUERY = <<<SQL
SELECT * FROM mediafiles
WHERE mediafile_medium_id = :id AND mediafile_variant = :variant
SQL; #---|


	const PULL_ALL_QUERY = <<<SQL
SELECT * FROM mediafiles WHERE mediafile_medium_id = :id
SQL; #---|


	const PUSH_QUERY = <<<SQL
INSERT INTO mediafiles
(mediafile_medium_id, mediafile_variant, mediafile_type, mediafile_extension, mediafile_data)
VALUES (:id, :variant, :type, :extension, :data)
SQL; #---|


	const EXTENSIONS = [ // TODO missing and not the best solution
		'application/gzip' => ['gz'],
		'application/javascript' => ['js'],
		'application/json' => ['json'],
		'application/msexcel' => ['xls', 'xla'],
		'application/mspowerpoint' => ['ppt', 'ppz', 'pps', 'pot'],
		'application/msword' => ['doc', 'dot'],
		'application/octet-stream' => ['txt'],
		'application/pdf' => ['pdf'],
		'application/rtf' => ['rtf'],
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],
		'application/vnd.openxmlformats-officedocument.wordprocessingml.sheet' => ['docx'],
		'application/xhtml+xml' => ['htm', 'html'],
		'application/xml' => ['xml'],
		'application/x-latex' => ['latex'],
		'application/x-tar' => ['tar'],
		'application/x-tex' => ['tex'],
		'application/zip' => ['zip'],
		'audio/mpeg' => ['mp3'],
		'audio/mp4' => ['mp4'],
		'audio/ogg' => ['ogg'],
		'audio/wav' => ['wav'],
		'image/bmp' => ['bmp'],
		'image/gif' => ['gif'],
		'image/jpeg' => ['jpg', 'jpeg'],
		'image/png' => ['png'],
		'image/svg+xml' => ['svg'],
		'image/tiff' => ['tiff', 'tif'],
		'image/vnd.wap.wbmp' => ['wbmp'],
		'image/x-icon' => ['ico'],
		'video/mpeg' => ['mpeg'],
		'video/mp4' => ['mp4'],
		'video/ogg' => ['ogg'],
		'video/webm' => ['webm']
	];


}
?>
