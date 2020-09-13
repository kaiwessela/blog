<?php
namespace Blog\Frontend\Web\Controllers;
use \Blog\Frontend\Web\Controllers\Controller;
use \Blog\Backend\Models\Person;
use \Blog\Backend\Exceptions\EmptyResultException;
use \Blog\Frontend\Web\Modules\Pagination;
use \Blog\Frontend\Web\Modules\Picture;


class PersonController implements Controller {
	const MODEL = 'Person';

	public $errors = [
		'404' => false
	];

	/* @inherited
	const MODEL;

	private $params;
	private $models;

	public $objects;
	public $errors;
	*/

	public $pagination;

	public function process() {
		foreach($this->models as $key => &$model){
			$this->objects[$key] = $model->export();

			$this->objects[$key]->parsed_content = Parsedown::instance()->text($model->content);

			if($this->objects[$key]->image){
				$this->objects[$key]->picture = new Picture($model->image);
			}
		}
	}
}
?>