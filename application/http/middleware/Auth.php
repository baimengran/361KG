<?php

namespace app\http\middleware;

use think\facade\Request;
use think\facade\Response;

class Auth
{
    public function handle($request, \Closure $next)
    {
        if (!session('uid') || !session('username')) {
           return redirect('login/index');
        }
        return $next($request);
    }
}
