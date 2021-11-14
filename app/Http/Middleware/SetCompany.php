<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Company;

class SetCompany {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        if (!Auth::guest()) {

            $company_id = Auth::user()->company_id;
            $user_id = Auth::user()->id;
            if (!is_numeric($company_id) || $company_id == 0 ) {
                $tx = DB::table("company_users")->where("user_id", $user_id)->first();
                if (isset($tx->company_id)) {
                    $company_id = $tx->company_id;
                    User::where('id', $user_id)->update(['company_id' => $company_id]);
                } else {
                    $company_id = null;
                }
            } else {
                $tx = DB::table("company_users")->where(["user_id" => $user_id], ['company_id'=>$company_id])->first();
                if (!isset($tx->company_id)) {
                    User::where('id', $user_id)->update(['company_id' => null]);
                    $company_id = null;
                }
            }
            if (is_numeric($company_id)) {
                Auth::user()->company = Company::where('id', $company_id)->first();
                Auth::user()->company_id = $company_id;
            }
        }

        return $next($request);
    }

}
