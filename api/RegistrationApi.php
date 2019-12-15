<?php
require_once 'Api.php';
require_once 'models/UsersModel.php';

class RegistrationApi extends Api
{
    public $apiName = 'registration';

    public function indexAction()
    {
        return $this->responseError(101);
    }

    public function viewAction()
    {
        return $this->responseError(101);
    }

    /**
     * Метод POST
     * Создание нового пользователя
     * http://ДОМЕН/registration + параметры запроса ...
     * @return string
     */
    public function createAction()
    {
        $name = $this->requestParams['name'] ?? '';
        $surname = $this->requestParams['surname'] ?? '';
        $middlename = $this->requestParams['middlename'] ?? '';
        $is_tutor = $this->requestParams['is_tutor'] ?? '';
        $email = $this->requestParams['email'] ?? '';
        $password = $this->requestParams['password'] ?? '';

        if ($name && $surname && $is_tutor != '' && $email && $password) {

            $userModel = new UsersModel();
            $userModel->addFilter('email', $email);
            $user = $userModel->getOne();
            if (!$user) {
                $userModel = new UsersModel();

                if ($userModel->saveNew(['name' => $name, 'surname' => $surname, 'middlename' => $middlename, 'is_tutor' => $is_tutor, 'email' => $email, 'pass_hash' => password_hash($password, PASSWORD_BCRYPT)
                ])) {
                    return $this->response('Data saved.', 200);
                }
                return $this->responseError(105);
            }
            return $this->responseError(109);
        }
        return $this->responseError(102);
    }

    public function updateAction()
    {
        return $this->responseError(101);
    }

    public function deleteAction()
    {
        return $this->responseError(101);
    }

}
