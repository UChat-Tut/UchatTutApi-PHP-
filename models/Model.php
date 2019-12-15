<?php

abstract class Model
{
    //config
    protected $table_name;
    protected $fields = [];
    protected $start_fields = [];
    protected $allowed_extra_fields = [];
    protected $validFilterableFields = [];


    //extra
    protected $data = [];
    protected $filters = [];
    protected $extra_fields = [];

    protected $offset;
    protected $limit;
    protected $orderBy;
    protected $sort = true;

    public $count = 0;


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

    public function addOffset($value)
    {
        $this->offset = $value;
    }

    public function addLimit($value)
    {
        $this->limit = $value;
    }

    public function addOrderBy($value, $sort)
    {
        $this->orderBy = $value;
        $this->sort = $sort;
    }

    public function getAll()
    {
        $this->data = R::getAll($this->generateSQL());
        $this->count = count($this->data);
        return $this->data;
    }

    public function getOne()
    {
        $this->data = R::getRow($this->generateSQL());
        $this->count = 1;
        return $this->data;
    }

    public function saveNew($data)
    {
        $element = R::dispense($this->table_name);
        foreach ($data as $key => $value) {
            $element[$key] = $value;
        }
        // Сохраняем объект
        return R::store($element);
    }

    public function update($id, $data)
    {
        $row = R::load($this->table_name, $id);

        foreach ($data as $key => $value) {
            if (in_array($key, $this->fields)) {
                $row->{$key} = $value;
            }
        }

        return R::store($row);
    }

    public function delete($id)
    {
        try {
            $row = R::load($this->table_name, $id);
            R::trash($row);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function generateSQL()
    {
        $sql_request_str = 'SELECT ';

        $fields_str = '';

        $fields_str .= '`';
        $fields_str .= implode("`, `", $this->start_fields);
        $fields_str .= '`';

        if (count($this->extra_fields) != 0) {
            $fields_str .= ', `';
            $fields_str .= implode("`, `", $this->extra_fields);
            $fields_str .= '`';
        }

        $sql_request_str .= $fields_str . ' FROM `' . $this->table_name . '`';

        if (count($this->filters) != 0) {
            $sql_request_str .= " WHERE ";

            $filters_str = '';

            $i = 0;
            foreach ($this->filters as $filter_key => $filter_value) {
                $filters_str .= $filter_key . ' ' . $filter_value['operator'] . ' "' . $filter_value['value'] . '" ';

                if ($i != count($this->filters) - 1) {
                    $filters_str .= 'AND ';
                }
                $i++;
            }

            $sql_request_str .= $filters_str;
        }


        if ($this->orderBy) {
            $sql_request_str .= ' ORDER BY' . $this->orderBy;
            if ($this->sort) {
                $sql_request_str .= ' asc';
            } else {
                $sql_request_str .= ' desc';
            }
        }

        if ($this->limit) {
            $sql_request_str .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset) {
            $sql_request_str .= ' OFFSET ' . $this->offset;
        }
        return $sql_request_str;
    }

    protected function findModel($id)
    {
        return R::findOne($id);
    }
}