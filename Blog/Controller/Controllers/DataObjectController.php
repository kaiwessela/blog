<?php
namespace Blog\Controller\Controllers;
use \Astronauth\Main as Astronauth;
use \Blog\Controller\Controller;
use \Blog\Controller\Call;
use \Blog\Controller\Request;
use \Blog\Model\Abstracts\DataObject;
use \Blog\Model\Abstracts\DataObjectList;
use \Blog\Controller\Pagination\Pagination;
use \Blog\Model\Exceptions\InputFailedException;
use \Blog\Controller\Substitution;
use Exception;

class DataObjectController extends Controller {
	public Request $request;
	public Astronauth $astronauth;

	public int $status;
	public Call $call;

	public DataObject|DataObjectList|null $object;

	public ?Pagination $pagination;
	public ?InputFailedException $errors;


	public function prepare(Call $call) : void {
		if(is_subclass_of($call->dataobject, DataObjectList::class)){
			if($call->action != 'list'){
				throw new Exception('DataObjectCtl. | Prepare » invalid action for list.');
			}

			$check_amount_and_page = true;

			if(!is_int($call->amount)){
				throw new Exception('DataObjectCtl. | Prepare » invalid amount.');
			}

			if(!is_int($call->page)){
				throw new Exception('DataObjectCtl. | Prepare » invalid page.');
			}

		} else if(is_subclass_of($call->dataobject, DataObject::class)){
			if(!in_array($call->action, ['show', 'new', 'edit', 'delete'])){
				throw new Exception('DataObjectCtl. | Prepare » invalid action.');
			}

			if(!is_string($call->identifier)){
				throw new Exception('DataObjectCtl. | Prepare » invalid identifier.');
			}

			$check_amount_and_page = $call->dataobject::PAGINATABLE;
		}

		if($check_amount_and_page){
			if(!is_int($call->amount)){
				throw new Exception('DataObjectCtl. | Prepare » invalid amount.');
			}

			if(!is_int($call->page)){
				throw new Exception('DataObjectCtl. | Prepare » invalid page.');
			}
		}

		$this->call = $call;
		$on = $call->dataobject;
		$this->object = new $on();
		$this->errors = null;
	}


	public function execute() : void {
		if($this->call->action == 'list'){
			$limit = $this->call->amount;

			if($this->call->page == null || $this->call->page == 1){
				$offset = null;
			} else {
				$offset = $this->call->amount * ($this->call->page - 1);
				$last_page = ceil($this->object->count() / $this->call->amount);

				if($this->object->count == 0){
					$this->status = 24;
					return;
				} else if($this->call->page > $last_page || $this->call->page == 0){
					$this->status = 44; // TODO this shows a 404 page. maybe this is not good.
					return;
				}
			}

			try {
				$this->object->pull($limit, $offset, $this->call->options);
				$this->status = 20;
			} catch(EmptyResultException $e){
				$this->status = 24;
				return;
			}

		} else if($this->call->action == 'new'){
			if($this->request->is_post()){
				$this->object->generate();

				try {
					$this->object->import($this->request->post);
					$this->object->push();
					$this->status = 21;
				} catch(InputFailedException $e){
					$this->status = 41;
					$this->errors = $e;
				}
			} else {
				$this->object = null;
				$this->status = 24;
			}

		} else if($this->call->action == 'count'){
			// TODO

		} else {
			if($this->call->action == 'show' && $this->object::PAGINATABLE){
				$limit = $this->call->amount;
				$offset = is_null($this->call->page)
					? null : ($this->call->amount * ($this->call->page - 1));
			} else {
				$limit = null;
				$offset = null;
			}

			try {
				$this->object->pull($this->call->identifier, $limit, $offset, $this->call->options);
			} catch(EmptyResultException $e){
				$this->status = 44;
				return;
			}

			if($this->request->is_get() || $this->call->action == 'show'){
				$this->status = 20;
				return;
			} else if($this->call->action == 'edit'){
				try {
					$this->object->import($this->request->post);
					$this->object->push();
					$this->status = 22;
				} catch(InputFailedException $e){
					$this->status = 41;
					$this->errors = $e;
					return;
				}
			} else if($this->call->action == 'delete'){
				$this->object->delete();
				$this->status = 23;
				return;
			}
		}
	}

	public function process() : void { // TODO maybe rename
		// TODO error handling

		if($this->object::PAGINATABLE && !empty($this->call->options['pagination'])){
			$url_substitution = new Substitution($this->call->options['pagination'], $this->request);
			$url_scheme = $url_substitution->resolve();

			$this->pagination = new Pagination($this->call->page, $this->call->amount, $this->object->count(), $url_scheme);
		} else {
			$this->pagination = null;
		}

		$this->object?->export();
		// return $this->object; // maybe
	}
}
?>
