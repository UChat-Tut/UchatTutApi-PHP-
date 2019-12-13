<?php
require_once 'Api.php';
require_once 'models/EventsModel.php';

class EventsApi extends Api
{
    public $apiName = 'events';

    /**
     * Метод GET
     * Вывод списка всех записей
     * http://ДОМЕН/events
     * @return string
     */
    public function indexAction()
    {
        $eventsModel = new EventsModel();

        foreach ($this->requestParams as $requestName => $requestParam) {
            if ($requestName == 'fields') {
                $fields = explode(',', str_replace(' ', '', $requestParam));
                $eventsModel->addFields($fields);
            } else {
                $eventsModel->addFilter($requestName, $requestParam);
            }
            if ($requestName == 'token') {
                //оставить только по токену потом


                $userModel = new UsersModel();
                $userModel->addFilter('access_token', $requestParam);
                $userModel->addFields(['id']);
                $user = $userModel->getOne();

                if ($user) {

                    print_r($userModel);
                    $eventsModel->addFilter('owner_id', $user['id']);

                    if ($events = $eventsModel->getAll()) {
                        return $this->response($events, 200);
                    }
                    return $this->responseError(103);
                }


                return $this->responseError(108);

                //оставить только по токену потом

            }
        }


        $events = $eventsModel->getAll();
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
            $eventsModel = new EventsModel();

            foreach ($this->requestParams as $requestName => $requestParam) {
                if ($requestName == 'fields') {
                    $fields = explode(',', str_replace(' ', '', $requestParam));
                    $eventsModel->addFields($fields);
                } else {
                    $eventsModel->addFilter($requestName, $requestParam);
                }

            }

            $eventsModel->addFilter('id', $id);
            $event = $eventsModel->getOne();
            if ($event) {
                return $this->response($event, 200);
            }
        }
        return $this->responseError(103);
    }

    /**
     * Метод POST
     * Создание новой записи
     * http://ДОМЕН/events + параметры запроса name, email
     * @return string
     */
    public function createAction()
    {
        $title = $this->requestParams['title'] ?? '';
        $datetime_start = $this->requestParams['datetime_start'] ?? '';
        $datetime_end = $this->requestParams['datetime_end'] ?? '';
        $repeat_mode = $this->requestParams['repeat_mode'] ?? '';
        $token = $this->requestParams['token'] ?? '';

        if ($title && $datetime_start && $datetime_end && $repeat_mode && $token) {
            $userModel = new UsersModel();
            $userModel->addFilter('access_token', $token);
            $userModel->addFields(['id']);
            $user = $userModel->getOne();

            if ($user) {
                $owner_id = $user['id'];

                $event = new EventsModel([
                    'title' => $title,
                    'owner_id' => $owner_id,
                    'datetime_start' => $datetime_start,
                    'datetime_end' => $datetime_end,
                    'repeat_mode' => $repeat_mode
                ]);
                if ($event->saveNew()) {
                    return $this->response('Data saved.', 200);
                }
            }
            return $this->responseError(108);
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
        $parse_url = parse_url($this->requestUri[0]);
        $eventId = $parse_url['path'] ?? null;

        $title = $this->requestParams['title'] ?? '';
        $datetime_start = $this->requestParams['datetime_start'] ?? '';
        $datetime_end = $this->requestParams['datetime_end'] ?? '';
        $repeat_mode = $this->requestParams['repeat_mode'] ?? '';
        $token = $this->requestParams['token'] ?? '';
        if ($title && $datetime_start && $datetime_end && $repeat_mode && $token) {

            $eventModel = new EventsModel();
            $eventModel->addFilter('id', $eventId);
            $eventModel->addFields(['owner_id']);
            $event = $eventModel->getOne();
            if (!$eventId || !$event) {
                return $this->responseError(103);
            }

            $userModel = new UsersModel();
            $userModel->addFilter('access_token', $token);
            $userModel->addFields(['id']);
            $user = $userModel->getOne();
            if ($user) {
                if ($event['owner_id'] == $user['id']) {
                    // Заполняем объект свойствами
                    $event = new EventsModel([
                        'title' => $title,
                        'owner_id' => $event['owner_id'],
                        'datetime_start' => $datetime_start,
                        'datetime_end' => $datetime_end,
                        'repeat_mode' => $repeat_mode
                    ]);

                    $upd_event = $event->saveNew();

                    if ($upd_event) {
                        return $this->response('Data updated.', 200);
                    }

                }
            } else {
                return $this->responseError(108);
            }
        }
        return $this->responseError(102);
    }

    /**
     * Метод DELETE
     * Удаление отдельной записи (по ее id)
     * http://ДОМЕН/users/1
     * @return string
     */
    public function deleteAction()
    {
        $parse_url = parse_url($this->requestUri[0]);
        $eventId = $parse_url['path'] ?? null;

        $token = $this->requestParams['token'] ?? '';

        if ($token) {
            $user = UsersModel::getByParam('access_token', $token);

            if ($user) {
                $owner_id = $user->id;

                $event = (new EventsModel)->getByParam('id', $eventId);

                if (!$eventId || !$event) {
                    return $this->responseError(103);
                }

                if ($owner_id == $event->owner_id) {
                    if (R::trash($event)) {
                        return $this->response('Data deleted.', 200);
                    }
                }
                return $this->responseError(108);
            }
            return $this->responseError(108);
        }
        return $this->responseError(102);
    }

}
