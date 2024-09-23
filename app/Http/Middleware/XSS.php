<?php

namespace App\Http\Middleware;

use App\Models\Utility;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class XSS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $settings = Utility::settings();
            if (!empty($settings['timezone'])) {
                Config::set('app.timezone', $settings['timezone']);
                date_default_timezone_set(Config::get('app.timezone', 'Asia/Jakarta'));
            }

            App::setLocale(Auth::user()->lang);
        }
        return $next($request);
    }
}
