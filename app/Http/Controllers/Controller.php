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
    $this->authorize('browse_database');
    $root_path = base_path();
    $process = new Process('cd ' . $root_path . '; ./deploy.sh');
    $process->run(function ($type, $buffer) {
        echo  "<pre> $buffer </pre> \n";
        Log::info("deploy : $buffer");
    });
  }

  public function serverInfo(){

    $data['disk_free_space'] = disk_free_space('/');

    $data['commit_hash'] = trim(exec('git log --pretty="%h" -n1 HEAD'));

    $data['commit_date'] = trim(exec('git log -n1 --pretty=%ci HEAD'));

    $data['ip'] = request()->server('SERVER_ADDR');
    
    $load = sys_getloadavg();

    $data['cpu_load_1_minute'] = $load[0] * 100  . "%" ;
    
    $data['cpu_load_5_minutes'] = $load[1] * 100  . "%" ;
    
    $data['memory_usage'] = self::get_server_memory_usage() ;
    
    return response()->json($data);
  }

  public static function get_server_memory_usage(){

    $free = shell_exec('free');
    $free = (string)trim($free);
    $free_arr = explode("\n", $free);
    $mem = explode(" ", $free_arr[1]);
    $mem = array_filter($mem);
    $mem = array_merge($mem);
    $memory_usage = $mem[2]/$mem[1]*100;

    return $memory_usage;
  }

}
