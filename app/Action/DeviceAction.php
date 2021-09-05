<?php

namespace app\Actions;

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
        return 'add';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-success pull-right',
        ];
    }

    public function getDefaultRoute()
    {
        return route('my.route');
    }
}
