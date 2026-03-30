<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasUuids;

    protected $table = 'post';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'content',
        'user_id',
    ];

    protected $appends = [
        'content_blocks',
        'first_content_block',
        'content_blocks_count',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(PostReaction::class);
    }

    public function getContentBlocksAttribute(): array
    {
        return $this->parseContentBlocks($this->attributes['content'] ?? null);
    }

    public function getFirstContentBlockAttribute(): ?string
    {
        return $this->content_blocks[0] ?? null;
    }

    public function getContentBlocksCountAttribute(): int
    {
        return count($this->content_blocks);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = Str::uuid();
            }
        });
    }

    private function parseContentBlocks(?string $content): array
    {
        if (!$content) {
            return [];
        }

        $decoded = json_decode($content, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter($decoded, fn ($item) => is_string($item) && $item !== ''));
        }

        return [$content];
    }
}
