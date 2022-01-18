<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ResetToken extends AbstractAction
{
    public function getTitle()
    {
        return 'Token Sıfırla';
    }

    public function getIcon()
    {
        return 'voyager-key';
    }

    public function getPolicy()
    {
        return 'delete';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-warning pull-right',
        ];
    }
    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'cihazlar';
    }

    public function getDefaultRoute()
    {

        return route('reset_token', array("id"=>$this->data->id));

    }

}
