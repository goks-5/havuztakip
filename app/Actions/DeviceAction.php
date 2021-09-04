<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class DeviceAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Cihazı Tanımla';
    }

    public function getIcon()
    {
        return 'voyager-plus';
    }

    public function getPolicy()
    {
        return 'delete';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-success pull-right',
        ];
    }
    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'tanimsiz_cihazlar';
    }

    public function getDefaultRoute()
    {

        return route('voyager.cihazlar.create', array("mac"=>$this->data->mac,"device_id"=>$this->data->device_id,));

    }

}
