<?php

namespace App\Traits;

use Illuminate\Support\Facades\Session;

trait SessionHandler
{
    public function isSessioned($key, $value): bool
    {
        return Session::get($key) == $value;
    }

    public function has($key): bool
    {
        return Session::has($key) && !is_null(Session::get($key));
    }

    public function getIfExists($key)
    {
        return $this->has($key) ? Session::get($key) : null;
    }

    public function forgetAndPut($key, $value)
    {
        Session::forget($key);
        Session::put($key, $value);
    }
}
