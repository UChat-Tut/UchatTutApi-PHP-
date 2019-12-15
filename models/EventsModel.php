<?php
require_once 'db.php';
require_once 'Model.php';

/**
 *
 */
class EventsModel extends Model
{
    // Config
    protected $table_name = 'events';
    protected $fields = ['id', 'title', 'owner_id', 'datetime_start', 'datetime_end', 'repeat_mode'];

    protected $start_fields = ['title'];
    protected $allowed_extra_fields = ['id', 'owner_id', 'datetime_start', 'datetime_end', 'repeat_mode'];
    protected $validFilterableFields = ['id', 'owner_id', 'datetime_start', 'datetime_end', 'repeat_mode'];
}
