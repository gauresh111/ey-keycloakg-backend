<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inauguration extends Model
{
    use HasFactory;
    
    protected $table = 'readyForInauguration';
    protected $fillable = ['college_name'];
    public function profiles(){
        return $this->hasMany(Profile::class);
    }
}
