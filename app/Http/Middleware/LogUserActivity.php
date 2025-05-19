<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        if (Auth::check()) {
            $user = Auth::user(); // Use Auth::user() instead of auth()->user()

            $logData = [
                'user_id' => $user->id,
                'email' => $user->email,
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'status_code' => $response->getStatusCode(),
                'execution_time_ms' => round($executionTime, 2),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toIso8601String(),
            ];

            Log::channel('user_activity')->info('User activity', $logData);
        }

        return $response;
    }
}
