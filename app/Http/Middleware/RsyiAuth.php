<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * التحقق من تسجيل دخول المستخدم وصلاحياته.
 */
class RsyiAuth
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        // غير مسجّل
        if (! Session::has('user_id')) {
            return redirect()->route('login')
                ->withErrors(['auth' => 'يجب تسجيل الدخول أولاً.']);
        }

        // التحقق من الأدوار إذا طُلب
        if (! empty($roles)) {
            $userRoles = Session::get('user_roles', []);

            // administrator يمر دائماً
            if (! in_array('administrator', $userRoles, true)) {
                $allowed = array_intersect($roles, $userRoles);
                if (empty($allowed)) {
                    abort(403, 'ليس لديك صلاحية الوصول لهذه الصفحة.');
                }
            }
        }

        return $next($request);
    }
}
