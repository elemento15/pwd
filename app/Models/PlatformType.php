<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['label','type_id'];

    /**
     * Get the platform for the type.
     */
    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * Get the platform for the type.
     */
    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}
