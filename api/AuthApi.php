<?php
require_once 'Api.php';
require_once 'models/UsersModel.php';

class AuthApi extends Api
{
    public $apiName = 'authorization';

    public function indexAction()
    {
        return $this->response('Method not allowed', 405);
    }

    public function viewAction()
    {
        return $this->response('Method not allowed', 405);
    }

    /**
     * Метод POST
     * Получение токена
     * http://ДОМЕН/authorization + параметры запроса ...
     * @return string
     */
    public function createAction()
    {
        $email = $this->requestParams['email'] ?? '';
        $password = $this->requestParams['password'] ?? '';

        if ($email && $password) {
            $userModel = new UsersModel();
            $userModel->addFields(['id', 'pass_hash']);;
            $userModel->addFilter('email', $email);

            $user = $userModel->getOne();
            if ($user) {
                $pass_hash = $user['pass_hash'];
                if (password_verify($password, $pass_hash)) {
                    $salt = uniqid(mt_rand(), true);
                    $access_token = password_hash($email . $salt . $password, PASSWORD_BCRYPT);

                    if ($userModel->update($user['id'], ['salt' => $salt, 'access_token' => $access_token])) {
                        return $this->response([
                            'access_token' => $access_token
                        ], 200);
                    }

                }
                return $this->responseError(104);
            }
            return $this->responseError(103);
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
