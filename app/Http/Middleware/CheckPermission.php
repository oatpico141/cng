<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        // Get the authenticated user
        $user = $request->user();

        // If user is not authenticated, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // Map route actions to permission actions
        $actionMap = [
            'index' => 'view',
            'show' => 'view',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'edit',
            'update' => 'edit',
            'destroy' => 'delete',
        ];

        // Get the current route action
        $routeAction = $request->route()->getActionMethod();

        // Determine the permission action required
        $permissionAction = $actionMap[$routeAction] ?? 'view';

        // Check if user has permission
        if (!$user->hasPermission($module, $permissionAction)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
