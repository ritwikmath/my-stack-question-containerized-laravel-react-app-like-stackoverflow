<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeSnippet extends Model
{
    use HasFactory;

    protected $fillable = ['body'];

    public function codeable()
    {
        return $this->morphTo();
    }
}
