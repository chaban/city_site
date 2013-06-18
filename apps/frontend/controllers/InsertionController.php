<?php

use \Phalcon\Tag as Tag;

class InsertionController extends ControllerBase
    {

    function indexAction()
    {
        $this->session->conditions = null;
    }

    public function searchAction()
{

        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = \Phalcon\Mvc\Model\Criteria::fromInput($this->di, "Insertion", $_POST);
            $this->session->conditions = $query->getConditions();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
            if ($numberPage <= 0) {
                $numberPage = 1;
            }
        }

        $parameters = array();
        if ($this->session->conditions) {
            $parameters["conditions"] = $this->session->conditions;
        }
        $parameters["order"] = "id";

        $insertion = Insertion::find($parameters);
        if (count($insertion) == 0) {
            $this->flash->notice("The search did not find any insertion");
            return $this->dispatcher->forward(array("controller" => "insertion", "action" => "index"));
        }

        $paginator = new \Phalcon\Paginator\Adapter\Model(array(
            "data" => $insertion,
            "limit"=> 10,
            "page" => $numberPage
        ));
        $page = $paginator->getPaginate();

        $this->view->setVar("page", $page);
    }

    public function newAction()
    {

    }

    public function editAction($id)
    {

        $request = $this->request;
        if (!$request->isPost()) {

            $id = $this->filter->sanitize($id, array("int"));

            $insertion = Insertion::findFirst('id="'.$id.'"');
            if (!$insertion) {
                $this->flash->error("insertion was not found");
                return $this->dispatcher->forward(array("controller" => "insertion", "action" => "index"));
            }
            $this->view->setVar("id", $insertion->id);
        
            Tag::displayTo("id", $insertion->id);
            Tag::displayTo("category", $insertion->category);
            Tag::displayTo("body", $insertion->body);
            Tag::displayTo("status", $insertion->status);
            Tag::displayTo("price_range", $insertion->price_range);
            Tag::displayTo("price", $insertion->price);
            Tag::displayTo("buy_sell", $insertion->buy_sell);
            Tag::displayTo("create_user_id", $insertion->create_user_id);
            Tag::displayTo("update_user_id", $insertion->update_user_id);
            Tag::displayTo("create_time", $insertion->create_time);
            Tag::displayTo("update_time", $insertion->update_time);
        }
    }

    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array("controller" => "insertion", "action" => "index"));
        }

        $insertion = new Insertion();
        $insertion->id = $this->request->getPost("id");
        $insertion->category = $this->request->getPost("category");
        $insertion->body = $this->request->getPost("body");
        $insertion->status = $this->request->getPost("status");
        $insertion->price_range = $this->request->getPost("price_range");
        $insertion->price = $this->request->getPost("price");
        $insertion->buy_sell = $this->request->getPost("buy_sell");
        $insertion->create_user_id = $this->request->getPost("create_user_id");
        $insertion->update_user_id = $this->request->getPost("update_user_id");
        $insertion->create_time = $this->request->getPost("create_time");
        $insertion->update_time = $this->request->getPost("update_time");
        if (!$insertion->save()) {
            foreach ($insertion->getMessages() as $message) {
                $this->flash->error((string) $message);
            }
            return $this->dispatcher->forward(array("controller" => "insertion", "action" => "new"));
        } else {
            $this->flash->success("insertion was created successfully");
            return $this->dispatcher->forward(array("controller" => "insertion", "action" => "index"));
        }

    }

    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array("controller" => "insertion", "action" => "index"));
        }

        $id = $this->request->getPost("id", "int");
        $insertion = Insertion::findFirst("id='$id'");
        if (!$insertion) {
            $this->flash->error("insertion does not exist ".$id);
            return $this->dispatcher->forward(array("controller" => "insertion", "action" => "index"));
        }
        $insertion->id = $this->request->getPost("id");
        $insertion->category = $this->request->getPost("category");
        $insertion->body = $this->request->getPost("body");
        $insertion->status = $this->request->getPost("status");
        $insertion->price_range = $this->request->getPost("price_range");
        $insertion->price = $this->request->getPost("price");
        $insertion->buy_sell = $this->request->getPost("buy_sell");
        $insertion->create_user_id = $this->request->getPost("create_user_id");
        $insertion->update_user_id = $this->request->getPost("update_user_id");
        $insertion->create_time = $this->request->getPost("create_time");
        $insertion->update_time = $this->request->getPost("update_time");
        if (!$insertion->save()) {
            foreach ($insertion->getMessages() as $message) {
                $this->flash->error((string) $message);
            }
            return $this->dispatcher->forward(array("controller" => "insertion", "action" => "edit", "params" => array($insertion->id)));
        } else {
            $this->flash->success("insertion was updated successfully");
            return $this->dispatcher->forward(array("controller" => "insertion", "action" => "index"));
        }

    }

    public function deleteAction($id){

        $id = $this->filter->sanitize($id, array("int"));

        $insertion = Insertion::findFirst('id="'.$id.'"');
        if (!$insertion) {
            $this->flash->error("insertion was not found");
            return $this->dispatcher->forward(array("controller" => "insertion", "action" => "index"));
        }

        if (!$insertion->delete()) {
            foreach ($insertion->getMessages() as $message){
                $this->flash->error((string) $message);
            }
            return $this->dispatcher->forward(array("controller" => "insertion", "action" => "search"));
        } else {
            $this->flash->success("insertion was deleted");
            return $this->dispatcher->forward(array("controller" => "insertion", "action" => "index"));
        }
    }

}
