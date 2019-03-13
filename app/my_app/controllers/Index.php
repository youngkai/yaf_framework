<?php


class IndexController extends Controller {


	public function indexAction() {
		$result = (new TestModel())->getAll();
		$this->response(['code' => 200, 'msg' => 'success', 'data' => $result]);
	}
}
