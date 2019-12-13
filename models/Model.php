<?php


abstract class Model
{
    protected $table_name = '';
    protected $fields = [];
    protected $start_fields = [];
    protected $allowed_extra_fields = [];
    protected $validFilterableFields = [];


    protected $data = [];
    protected $filters = [];
    protected $extra_fields = [];

    function __construct($data = [])
    {
        if (isset($data)) {
            $this->data = $data;
        }
    }

    public function getAll()
    {
        $filters_str = '';

        if (count($this->filters) != 0) {

            $i = 0;
            foreach ($this->filters as $filter_key => $filter_value) {
                $filters_str .= $filter_key . ' ' . $filter_value['operator'] . ' ' . $filter_value['value'] . ' ';

                if ($i != count($this->filters) - 1) {
                    $filters_str .= 'AND ';
                }
                $i++;
            }
        }

        $this->data = R::findAll($this->table_name, $filters_str);
        return $this->data;
    }

    public function getOne()
    {
//        $sql_request_str = 'SELECT ';
//
//        $fields_str = '';
//
//        $fields_str .= '`';
//        $fields_str .= implode("`, `", $this->start_fields);
//        $fields_str .= '`';
//
//        if(count($this->extra_fields) != 0){
//            $fields_str .= ', `';
//            $fields_str .= implode("`, `", $this->extra_fields);
//            $fields_str .= '`';
//        }
//
//        $sql_request_str .=  $fields_str.' FROM `' .$this->table_name.'`';
//

        $filters_str = '';

        if (count($this->filters) != 0) {

            $i = 0;
            foreach ($this->filters as $filter_key => $filter_value) {
                $filters_str .= $filter_key . ' ' . $filter_value['operator'] . ' "' . $filter_value['value'] . '" ';

                if ($i != count($this->filters) - 1) {
                    $filters_str .= 'AND ';
                }
                $i++;
            }
        }


        $this->data = R::findOne($this->table_name, $filters_str);
        return $this->data;
    }

    public function saveNew()
    {
        $element = R::dispense($this->table_name);
        foreach ($this->data as $key => $value) {
            $element[$key] = $value;
        }
        // Сохраняем объект
        return R::store($element);
    }

    public function getData()
    {
        return $this->data;
    }

    public function getOnlyResponseData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fields)) {
                $this->data[$key] = $value;
            }
        }
        return $this->data;
    }

    public function save()
    {
        return R::store($this->data);
    }

    public function addFields($fields)
    {
        foreach ($fields as $field) {
            $field = strtolower($field);
            if ($field == null or !in_array($field, $this->allowed_extra_fields)) continue;
            array_push($this->extra_fields, $field);
        }
        if (count($this->extra_fields) == 0) {
            return false;
        }
        return true;
    }

    public function addFilter($field, $value, $operator = '=')
    {
        if ($value == null) return false;

        $this->filters[$field] = array(
            'value' => $value,
            'operator' => $operator
        );

        return true;

    }


}