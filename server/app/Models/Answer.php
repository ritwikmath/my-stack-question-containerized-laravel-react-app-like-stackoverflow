<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = ['body', 'depricated', 'question_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function code()
    {
        return $this->morphOne(CodeSnippet::class, 'codeable');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
