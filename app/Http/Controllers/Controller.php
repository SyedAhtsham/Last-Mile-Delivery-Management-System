<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use RealRashid\SweetAlert\Facades\Alert;
use RealRashid\SweetAlert\SweetAlertServiceProvider;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware(function ($request, $next){
            if(session('success_message')){
                Alert::success('Success!', session('success_message'))
                    ->focusConfirm(false)
                    ->autoClose(2500);

            }
            return $next($request);
        });

        $this->middleware(function ($request, $next){
            if(session('error_message')){
                Alert::error('Error!', session('error_message'))
                    ->focusConfirm(false)
                    ->autoClose(2500);

            }
            return $next($request);
        });

    }
}
