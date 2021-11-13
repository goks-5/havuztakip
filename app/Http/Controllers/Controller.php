<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\VoyagerBaseController;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;
class Controller extends VoyagerBaseController
{
  public function index(Request $request)
  {
  }

  public function updateFromGit(){
    $this->authorize('browse_admin');
    $root_path = base_path();
    $process = new Process('cd ' . $root_path . '; ./deploy.sh');
    $process->run(function ($type, $buffer) {
        echo  "<pre> $buffer </pre> \n";
        Log::info("deploy : $buffer");
    });
  }

}
