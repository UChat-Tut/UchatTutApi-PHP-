<?php
require_once 'db.php';
require_once 'Model.php';

/**
 *
 */
class UsersModel extends Model
{
    // Config
    protected $table_name = 'users';
    protected $fields = ['id', 'name', 'surname', 'middlename', 'isTutor', 'email', 'salt', 'pass_hash', 'access_token'];

    protected $start_fields = ['name', 'surname'];
    protected $allowed_extra_fields = ['id', 'name', 'surname', 'middlename', 'isTutor', 'pass_hash', 'email', 'access_token'];
    protected $validFilterableFields = ['id', 'name', 'surname', 'middlename', 'isTutor', 'email'];
}
