<?php

namespace App;

use TCG\Voyager\Models\DataType as BaseDataType;

class DataType extends BaseDataType
{ 
    

    public function addRows()
    {
        $typeId = $this->id;
        return  $this->rows()->where('add', 1)->orWhere(function($query) use ($typeId ){
            $query->where('field', 'company_id')
                ->where('data_type_id',$typeId );	
        });

    }

}
