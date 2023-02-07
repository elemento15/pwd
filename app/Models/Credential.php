<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['platform_id','profile_id','comments'];

    /**
     * Get the platform for the credential.
     */
    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * Get the profile for the credential.
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the properties for the credential.
     */
    public function properties()
    {
        return $this->hasMany(CredentialProperty::class);
    }
}
