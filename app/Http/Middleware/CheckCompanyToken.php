<?php

namespace App\Http\Middleware;

use App\Device;
use App\Company;
use App\UndefineDevice;
use Closure;

class CheckCompanyToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $header = $request->header('Authorization');


        if (strlen($header) < 4) {
            return response()->json(['status' => 'error', 'message' => 'missing authorization', 'timestamp' => time(), 'date' => date('Y-m-d H:i:s')], 400);
        } else {
            $company = Company::where(['token' => $header])->first();
            if ($company) {
                $request->attributes->add(['company' => $company->id]);
                return $next($request);
            } else {
                return response()->json(['status' => 'error', 'message' => 'token not validate', 'timestamp' => time(), 'date' => date('Y-m-d H:i:s')], 403);
            }
        }
    }
}
