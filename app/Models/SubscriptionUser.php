<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="SubscriptionUser",
 *     required={"id", "user_id", "subscription_id"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="SubscriptionUser ID"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="User ID"
 *     ),
 *     @OA\Property(
 *         property="subscription_id",
 *         type="integer",
 *         description="Subscription ID"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the record was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the record was last updated"
 *     )
 * )
 */

//Relation many-to-many between User & Subscription
class SubscriptionUser extends Model
{
    use HasFactory;
    # table subscription_users

    protected $table = 'subscription_users';

    protected $fillable = [
        'user_id',
        'subscription_id',
    ];
}
