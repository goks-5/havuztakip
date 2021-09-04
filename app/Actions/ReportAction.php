<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ReportAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Raporu Al';
    }

    public function getIcon()
    {
        return 'voyager-file-text';
    }

    public function getPolicy()
    {
        return 'browse';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-success pull-right view',
        ];
    }
    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'raporlar' || $this->dataType->slug == 'custom-reports' ;
    }

    public function getDefaultRoute()
    {
        if($this->dataType->slug == 'raporlar'){

          return route('report_excel', array("id"=>$this->data->id));
        }else if($this->dataType->slug == 'custom-reports'){

          return route('custom_report_excel', array("id"=>$this->data->id));
        }

    }

}
