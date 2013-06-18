<?php

use \Phalcon\Tag as Tag;

class UserController extends ControllerBase
    {

    function indexAction()
    {
        $this->session->conditions = null;
    }

    public function searchAction()
{

        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = \Phalcon\Mvc\Model\Criteria::fromInput($this->di, "User", $_POST);
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

        $user = User::find($parameters);
        if (count($user) == 0) {
            $this->flash->notice("The search did not find any user");
            return $this->dispatcher->forward(array("controller" => "user", "action" => "index"));
        }

        $paginator = new \Phalcon\Paginator\Adapter\Model(array(
            "data" => $user,
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

            $user = User::findFirst('id="'.$id.'"');
            if (!$user) {
                $this->flash->error("user was not found");
                return $this->dispatcher->forward(array("controller" => "user", "action" => "index"));
            }
            $this->view->setVar("id", $user->id);
        
            Tag::displayTo("id", $user->id);
            Tag::displayTo("username", $user->username);
            Tag::displayTo("fullname", $user->fullname);
            Tag::displayTo("email", $user->email);
            Tag::displayTo("password", $user->password);
            Tag::displayTo("role", $user->role);
            Tag::displayTo("active", $user->active);
            Tag::displayTo("last_login_time", $user->last_login_time);
            Tag::displayTo("create_time", $user->create_time);
            Tag::displayTo("update_time", $user->update_time);
        }
    }

    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array("controller" => "user", "action" => "index"));
        }

        $user = new User();
        $user->id = $this->request->getPost("id");
        $user->username = $this->request->getPost("username");
        $user->fullname = $this->request->getPost("fullname");
        $user->email = $this->request->getPost("email", "email");
        $user->password = $this->request->getPost("password");
        $user->role = $this->request->getPost("role");
        $user->active = $this->request->getPost("active");
        $user->last_login_time = $this->request->getPost("last_login_time");
        $user->create_time = $this->request->getPost("create_time");
        $user->update_time = $this->request->getPost("update_time");
        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error((string) $message);
            }
            return $this->dispatcher->forward(array("controller" => "user", "action" => "new"));
        } else {
            $this->flash->success("user was created successfully");
            return $this->dispatcher->forward(array("controller" => "user", "action" => "index"));
        }

    }

    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array("controller" => "user", "action" => "index"));
        }

        $id = $this->request->getPost("id", "int");
        $user = User::findFirst("id='$id'");
        if (!$user) {
            $this->flash->error("user does not exist ".$id);
            return $this->dispatcher->forward(array("controller" => "user", "action" => "index"));
        }
        $user->id = $this->request->getPost("id");
        $user->username = $this->request->getPost("username");
        $user->fullname = $this->request->getPost("fullname");
        $user->email = $this->request->getPost("email", "email");
        $user->password = $this->request->getPost("password");
        $user->role = $this->request->getPost("role");
        $user->active = $this->request->getPost("active");
        $user->last_login_time = $this->request->getPost("last_login_time");
        $user->create_time = $this->request->getPost("create_time");
        $user->update_time = $this->request->getPost("update_time");
        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error((string) $message);
            }
            return $this->dispatcher->forward(array("controller" => "user", "action" => "edit", "params" => array($user->id)));
        } else {
            $this->flash->success("user was updated successfully");
            return $this->dispatcher->forward(array("controller" => "user", "action" => "index"));
        }

    }

    public function deleteAction($id){

        $id = $this->filter->sanitize($id, array("int"));

        $user = User::findFirst('id="'.$id.'"');
        if (!$user) {
            $this->flash->error("user was not found");
            return $this->dispatcher->forward(array("controller" => "user", "action" => "index"));
        }

        if (!$user->delete()) {
            foreach ($user->getMessages() as $message){
                $this->flash->error((string) $message);
            }
            return $this->dispatcher->forward(array("controller" => "user", "action" => "search"));
        } else {
            $this->flash->success("user was deleted");
            return $this->dispatcher->forward(array("controller" => "user", "action" => "index"));
        }
    }

}
