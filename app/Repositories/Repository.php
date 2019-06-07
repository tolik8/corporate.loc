<?php

namespace Corp\Repositories;

use Config;

abstract class Repository
{
    protected $model = false;

    public function get()
    {
        return $this->model->select('*')->get();
    }

}
