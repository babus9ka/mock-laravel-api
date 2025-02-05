<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationActivity extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id', 'activity_id'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}

