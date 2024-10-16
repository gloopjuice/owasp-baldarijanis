<?php
// forumpost.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'nosaukums',
        'saturs',
        'author_id',
        'date',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id')->select('id', 'name'); // Select only necessary fields
    }

    // Modify toArray method to include author's name only if the relationship exists
    public function toArray()
    {
        $array = parent::toArray();
        $array['author_name'] = $this->author ? $this->author->name : null; // Include author's name if the relationship exists
        return $array;
    }
}
