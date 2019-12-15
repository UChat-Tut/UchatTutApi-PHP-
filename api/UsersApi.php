<?php
require_once 'Api.php';
require_once 'models/UsersModel.php';

class UsersApi extends Api
{
    public $apiName = 'users';

    /**
     * Метод GET
     * Вывод списка всех записей
     * http://ДОМЕН/events
     * @return string
     */
    public function indexAction()
    {
        $usersModel = new UsersModel();

        foreach ($this->requestParams as $requestName => $requestParam) {
            if ($requestName == 'fields') {
                $fields = explode(',', str_replace(' ', '', $requestParam));
                $usersModel->addFields($fields);
            } else {
                $usersModel->addFilter($requestName, $requestParam);
            }

        }

        $events = $usersModel->getAll();
        if ($events) {
            return $this->response($events, 200);
        }
        return $this->responseError(103);
    }

    /**
     * Метод GET
     * Просмотр отдельной записи (по id)
     * http://ДОМЕН/events/1
     * @return string
     */
    public function viewAction()
    {
        //id должен быть первым параметром после /users/x
        $id = array_shift($this->requestUri);

        if ($id) {
            $usersModel = new UsersModel();

            foreach ($this->requestParams as $requestName => $requestParam) {
                if ($requestName == 'fields') {
                    $fields = explode(',', str_replace(' ', '', $requestParam));
                    $usersModel->addFields($fields);
                } else {
                    $usersModel->addFilter($requestName, $requestParam);
                }

            }

            $usersModel->addFilter('id', $id);
            $user = $usersModel->getOne();
            if ($user) {
                return $this->response($user, 200);
            }
        }
        return $this->responseError(103);
    }

    /**
     * Метод POST
     * Создание новой записи
     * http://ДОМЕН/users + параметры запроса name, email
     * @return string
     */
    public function createAction()
    {
        return $this->response('Method not allowed', 405);

        $name = $this->requestParams['name'] ?? '';
        $surname = $this->requestParams['surname'] ?? '';
        $middlename = $this->requestParams['email'] ?? '';
        $isTutor = $this->requestParams['email'] ?? '';
        $email = $this->requestParams['email'] ?? '';
        $password = $this->requestParams['password'] ?? '';

        if ($name && $surname && $email) {

            if (true) {
                return $this->response('Data saved.', 200);
            }
            return $this->responseError(105);
        }
        return $this->responseError(102);
    }

    /**
     * Метод PUT
     * Обновление отдельной записи (по ее id)
     * http://ДОМЕН/users/1 + параметры запроса name, email
     * @return string
     */
    public function updateAction()
    {
        return $this->response('Method not allowed', 405);

        $parse_url = parse_url($this->requestUri[0]);
        $userId = $parse_url['path'] ?? null;

        $user = UsersModel::getByParam("id", $userId);
        if (!$userId || !$user) {
            return $this->responseError(103);
        }

        $name = $this->requestParams['name'] ?? '';
        $surname = $this->requestParams['surname'] ?? '';
        $email = $this->requestParams['email'] ?? '';

        if ($name && $surname && $email) {

            $upd_user = UsersModel::save($user);
            if ($upd_user) {
                return $this->response('Data updated.', 200);
            }
        }
        return $this->responseError(106);
    }

    /**
     * Метод DELETE
     * Удаление отдельной записи (по ее id)
     * http://ДОМЕН/users/1
     * @return string
     */
    public function deleteAction()
    {
        return $this->response('Method not allowed', 405);

        $parse_url = parse_url($this->requestUri[0]);
        $userId = $parse_url['path'] ?? null;

        $user = UsersModel::getByParam("id", $userId);

        if (!$userId || !$user) {
            return $this->responseError(103);
        }

        if (R::trash($user)) {
            return $this->response('Data deleted.', 200);
        }

        return $this->responseError(107);
    }

}
