<?php


class IndexController extends Controller {


	public function indexAction() {
		$get = $this->getRequest()->getQuery("get", "default value");
		$this->response(['code' => 200, 'msg' => 'success']);
	}
}
