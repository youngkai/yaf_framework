<?php


class IndexController extends Controller {


	public function indexAction() {
        $this->response(['code' => 200, 'msg' => 'success', 'data' => ['name' => 'aaa']]);
	}
}
