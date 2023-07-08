<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class DeviceInfo extends AbstractAction
{
    public function getTitle()
    {
        return 'Cihaz Durumu';
    }

    public function getIcon()
    {
        return 'voyager-bar-chart';
    }

    public function getPolicy()
    {
        return 'show';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-info pull-right',
        ];
    }
    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'cihazlar';
    }

    public function getDefaultRoute()
    {

        return route('deviceInfos', array("id"=>$this->data->id));

    }

}
