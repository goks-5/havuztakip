<?php

namespace App;

use TCG\Voyager\Models\DataType as BaseDataType;

class DataType extends BaseDataType
{ 
    

    public function addRows()
    {
        return $this->rows()->where('add', 1)->orWhere('field', 'company_id');
    }

}
