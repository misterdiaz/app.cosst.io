<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $guarded = ['id'];

    public function getFullNameAttribute(){
        return $this->first_name." ".$this->last_name;
    }

    public function getComboNameAttribute(){
        return $this->full_name."(".$this->company_name.")";
    }

    public function clientId(){
        return $this->belongsTo(Client::class);
    }

    public function userId(){
        return $this->belongsTo(User::class);
    }
}
