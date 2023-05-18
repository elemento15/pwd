<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Encryption\DecryptException;

class CredentialProperty extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['credential_id','platform_type_id','answer'];

    /**
     * Set the property's answer encrypted.
     *
     * @param  string  $value
     * @return void
     */
    public function setAnswerAttribute($value)
    {
        $this->attributes['answer'] = encrypt($value);
    }

    /**
     * Get the property's answer decrypted.
     *
     * @param  string  $value
     * @return string
     */
    public function getAnswerAttribute($value)
    {
        try {
            return decrypt($value);
        } catch (DecryptException $e) {
            return "";
        }
    }
}
