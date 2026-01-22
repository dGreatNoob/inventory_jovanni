<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class SetSpatieTeamContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       if (Auth::check()) {
            // Ensure department_id is properly encoded before passing to Spatie
            $departmentId = Auth::user()->department_id;

            // Convert to string and ensure UTF-8 encoding
            if ($departmentId !== null) {
                $departmentId = (string) $departmentId;

                // Handle different encodings
                if (!mb_check_encoding($departmentId, 'UTF-8')) {
                    $encoding = mb_detect_encoding($departmentId, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
                    if ($encoding && $encoding !== 'UTF-8') {
                        $departmentId = mb_convert_encoding($departmentId, 'UTF-8', $encoding);
                    } else {
                        $departmentId = mb_convert_encoding($departmentId, 'UTF-8', 'UTF-8');
                    }
                }
            }

            app(PermissionRegistrar::class)->setPermissionsTeamId($departmentId);
        }

        return $next($request);

    }
}
