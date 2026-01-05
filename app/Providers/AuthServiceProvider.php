<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        Gate::define('view-user', function (User $authUser, User $targetUser) {

            if ($authUser->isOwner()) {
                return true;
            }

            return $authUser->address_id === $targetUser->address_id;
        });

        Gate::define('create-user', function (User $authUser, string $roleToCreate) {

            if ($authUser->isOwner()) {
                return in_array($roleToCreate, ['manager', 'staff']);
            }

            if ($authUser->isManager()) {
                return $roleToCreate === 'staff';
            }

            return false;
        });

        Gate::define('edit-user', function (User $authUser, User $targetUser) {

            if ($authUser->isOwner()) {
                return true;
            }

            if ($authUser->isManager()) {

                if ($targetUser->isOwner()) {
                    return false;
                }

                return $authUser->address_id === $targetUser->address_id;
            }

            return false;
        });

        Gate::define('delete-user', function (User $authUser, User $targetUser) {

            if ($authUser->isOwner()) {
                return true;
            }

            if ($authUser->isManager()) {

                if ($targetUser->isOwner()) {
                    return false;
                }

                return $authUser->address_id === $targetUser->address_id;
            }

            return false;
        });
    }
}
