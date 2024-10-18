<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ChangeTrackingService;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'author_id',
        'content',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($comment) {
            app(ChangeTrackingService::class)->logChange("Comment {$comment->id} created by User {$comment->author_id} on Post {$comment->post_id}: {$comment->content}");
        });

        static::updated(function ($comment) {
            app(ChangeTrackingService::class)->logChange("Comment {$comment->id} updated by User {$comment->author_id}: {$comment->content}");
        });

        static::deleted(function ($comment) {
            app(ChangeTrackingService::class)->logChange("Comment {$comment->id} deleted by User {$comment->author_id}");
        });
    }

    public function post()
    {
        return $this->belongsTo(ForumPost::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}