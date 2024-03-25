<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class stuff extends Model
{
    use softDeletes;
    protected $fillable = ["name", "category"];


    public function stuffStcock()
    {
        return $this->hasOne(stuffStcoc::class);
    }

    public function inboundStuffs()
    {
        return $this->hasMany(InboundStuffs::class);
    }

    public function lendings()
    {
        return $this->hasMany(Lending::class);
    }
}
