<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $guarded = ['id'];

    public function lead(){
        return $this->belongsTo(Lead::class);
    }
}
