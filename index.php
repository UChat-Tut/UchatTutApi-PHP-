<?php
require_once 'api/RegistrationApi.php';
require_once 'api/AuthApi.php';
require_once 'api/EventsApi.php';
require_once 'api/UsersApi.php';

try {
    $requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $method = explode('?', $requestUri[1])[0];

    switch ($method) {
        case 'registration':
            $api = new RegistrationApi();
            break;
        case 'authorization':
            $api = new AuthApi();
            break;
        case 'users':
            $api = new UsersApi();
            break;
        case 'events':
            $api = new EventsApi();
            break;
        default:
            throw new RuntimeException('Invalid Method', 405);
            break;
    }
    if ($api != null) {
        echo $api->run();
    }
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}
