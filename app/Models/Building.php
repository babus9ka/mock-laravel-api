<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Building",
 *     type="object",
 *     required={"address", "latitude", "longitude"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the building",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string",
 *         description="Address of the building",
 *         example="123 Main Street"
 *     ),
 *     @OA\Property(
 *         property="latitude",
 *         type="number",
 *         format="float",
 *         description="Latitude of the building",
 *         example=40.7128
 *     ),
 *     @OA\Property(
 *         property="longitude",
 *         type="number",
 *         format="float",
 *         description="Longitude of the building",
 *         example=-74.0060
 *     ),
 *     @OA\Property(
 *         property="organizations",
 *         type="array",
 *         @OA\Items(
 *             ref="#/components/schemas/Organization"
 *         ),
 *         description="List of organizations located in the building"
 *     )
 * )
 */
class Building extends Model
{
    use HasFactory;

    protected $fillable = ['address', 'latitude', 'longitude'];

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }
}
