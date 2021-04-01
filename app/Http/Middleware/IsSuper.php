<?php

namespace DLW\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;
use Auth;

class IsSuper
{
    public function handle($request, Closure $next)
    {
        if(Auth::guard('admin')->user()->is_super == false && $request->route()->getName() !== 'admin.profile' && $request->profile !== 'profile'){
            abort('404');
        }
        return $next($request);
    }
}
