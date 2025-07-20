<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Enums\Roles;
use App\Models\Client;
use App\Models\User;

/**
 * Class ClientPolicy
 *
 * @package App\Policies
 */
class ClientPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     * @param User $user
     *
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     * @param User $user
     * @param Client $client
     *
     * @return bool
     */
    public function view(User $user, Client $client): bool
    {
        return $user->role_id === Roles::CLIENT ? ($user->client->id === $client->id) : true;
    }

    /**
     * Determine whether the user can update the model.
     * @param User $user
     * @param Client $client
     *
     * @return bool
     */
    public function update(User $user, Client $client): bool
    {
        return $user->client->id === $client->id;
    }

    /**
     * Determine whether the user can update the model.
     * @param User $user
     * @param Client $client
     *
     * @return bool
     */
    public function edit(User $user, Client $client): bool
    {
        return $user->client->id === $client->id;
    }

    /**
     * Determine whether the user can update the model.
     * @param User $user
     * @param Client $client
     *
     * @return bool
     */
    public function updateClientProfile(User $user, Client $client): bool
    {
        return $user->client->id === $client->id;
    }

    /**
     * Determine whether the user can update the model.
     * @param User $user
     * @param Client $client
     *
     * @return bool
     */
    public function updateOfficeDetails(User $user, Client $client): bool
    {
        return $user->client->id === $client->id;
    }

    /**
     * Determine whether the user can delete the model.
     * @param User $user
     * @param Client $client
     *
     * @return bool
     */
    public function delete(User $user, Client $client): bool
    {
        //
    }
}
