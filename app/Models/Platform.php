<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','url','comments'];

    /**
     * Get the types for the platform.
     */
    public function types()
    {
        return $this->hasMany(PlatformType::class);
    }
}
