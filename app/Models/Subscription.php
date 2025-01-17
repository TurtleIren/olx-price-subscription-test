<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @OA\Schema(
 *     schema="Subscription",
 *     required={"id", "url"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Subscription ID"
 *     ),
 *     @OA\Property(
 *         property="url",
 *         type="string",
 *         description="URL of the subscription (must be unique)"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="decimal",
 *         description="Price of the subscription",
 *         nullable=true,
 *         example=99.99
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the subscription was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the subscription was last updated"
 *     )
 * )
 */


class Subscription extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'price'];

    /**
     * Відношення до користувачів.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subscription_users', 'subscription_id', 'user_id');
    }

    /**
     * Групувати підписки за URL.
     */
    public static function allSubscriptions(): \Illuminate\Database\Eloquent\Collection
    {
        return self::all();
    }
}
