<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   
        $user = auth()->user();

        // 管理者の場合は、admin.homeへ
        if (auth('admin')->check()) {
            return redirect()->route('admin.home');
        }

        // 有料会員の場合はリダイレクト
        if ($user && $user->subscribed('premium_plan')) {
        return redirect('/')->with('flash_message', 'すでに有料会員です。');
        }

        if (! $request->user()?->Subscribed('premium_plan')) {
            return $next($request);
        }
        return redirect('/subscription/edit');
    }
}
