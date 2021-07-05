<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\SessionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthenticateWithIngcoStorehub
{
    use SessionHandler;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        \Log::debug(["Ingco Storehub Session" => collect(Session::all())->only(['api', 'access_token', 'refresh_token'])->toArray()]);

        return $this->has('access_token')
            ? $next($request)
            : redirect('login');
    }
}
