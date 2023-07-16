<?php

declare(strict_types=1);

namespace App\Http\Presenters;

use App\Contracts\PresenterCollectionInterface;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * @OA\Schema(
 *      schema="UserResponse",
 *      description="Returns user array response",
 *      type="object",
 *      @OA\Property(property="id", type="integer", format="int64", example="1"),
 *      @OA\Property(property="name", type="string", description="User name", example="Jacob"),
 *      @OA\Property(property="email", type="string", format="email", description="User email", example="test@example.com"),
 * )
 */

final class UserArrayPresenter implements PresenterCollectionInterface
{
    public function present(User $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ];
    }

    public function presentCollection(Collection $users): array
    {
        return $users->map(
            function (User $user) {
                return $this->present($user);
            }
        )->all();
    }
}
