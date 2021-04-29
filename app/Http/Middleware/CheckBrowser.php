<?php

namespace App\Http\Middleware;

use Closure;
use Jenssegers\Agent\Agent;

class CheckBrowser
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
        $agent = new Agent();
        if (($agent->isDesktop() && $agent->browser() != 'Chrome') || (!$agent->isDesktop())) {
            return response()->make(view('pages.errors'));
        }
        return $next($request);
    }
}
