<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Activity",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the activity",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the activity",
 *         example="Activity Name"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer",
 *         description="ID of the parent activity",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="parent",
 *         ref="#/components/schemas/Activity",
 *         description="Parent activity"
 *     ),
 *     @OA\Property(
 *         property="children",
 *         type="array",
 *         @OA\Items(
 *             ref="#/components/schemas/Activity"
 *         ),
 *         description="List of child activities"
 *     ),
 *     @OA\Property(
 *         property="organizations",
 *         type="array",
 *         @OA\Items(
 *             ref="#/components/schemas/Organization"
 *         ),
 *         description="List of organizations associated with the activity"
 *     )
 * )
 */
class Activity extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Activity::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Activity::class, 'parent_id');
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_activity');
    }
}
