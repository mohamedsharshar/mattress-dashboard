<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInventoryPermission
{
    public function handle(
        Request $request,
        Closure $next,
        ?string $permission = null
    ): Response {
        $user = $request->user();

        abort_unless(
            $user,
            401
        );

        if (!$permission) {
            $routeName =
                $request->route()?->getName();

            $permission =
                config(
                    'inventory_permissions.routes',
                    []
                )[$routeName] ?? null;
        }

        abort_unless(
            $permission,
            403,
            'Inventory permission is not configured.'
        );

        $roles = config(
            'inventory_permissions.roles',
            []
        );

        $role =
            $user->role
            ?? 'viewer';

        $permissions =
            $roles[$role]
            ?? [];

        $allowed =
            in_array(
                '*',
                $permissions,
                true
            )
            ||
            in_array(
                $permission,
                $permissions,
                true
            );

        abort_unless(
            $allowed,
            403,
            'You do not have permission to perform this action.'
        );

        return $next($request);
    }
}