<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Organization",
 *     title="Organization",
 *     description="Модель организации",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="ООО Рога и Копыта"),
 *     @OA\Property(property="building", ref="#/components/schemas/Building"),
 *     @OA\Property(property="phones", type="array", @OA\Items(ref="#/components/schemas/OrganizationPhone")),
 *     @OA\Property(property="activities", type="array", @OA\Items(ref="#/components/schemas/Activity"))
 * )
 */
class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'building_id'];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function phones()
    {
        return $this->hasMany(OrganizationPhone::class);
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'organization_activity');
    }
}
