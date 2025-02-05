<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="OrganizationPhone",
 *     type="object",
 *     required={"organization_id", "phone_number"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the phone record",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="organization_id",
 *         type="integer",
 *         description="ID of the organization",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="phone_number",
 *         type="string",
 *         description="Phone number of the organization",
 *         example="+1234567890"
 *     ),
 *     @OA\Property(
 *         property="organization",
 *         ref="#/components/schemas/Organization",
 *         description="Associated organization"
 *     )
 * )
 */
class OrganizationPhone extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id', 'phone_number'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
