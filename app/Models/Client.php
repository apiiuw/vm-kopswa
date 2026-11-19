<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'code',
        'npwp',
        'nitku',
        'address',
        'pic_name',
        'phone',
        'email',
        'notes',
    ];

    public function contracts()
    {
        return $this->hasMany(Vendor::class, 'client_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'client_id');
    }
}
