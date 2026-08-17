<?php

namespace App\Models;

use App\Support\SocialPlatform;
use Database\Factories\SocialLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['url'])]
class SocialLink extends Model
{
    /** @use HasFactory<SocialLinkFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function platform(): Attribute
    {
        return Attribute::make(
            get: fn () => SocialPlatform::detect($this->url)['key'],
        );
    }

    protected function platformLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => SocialPlatform::detect($this->url)['label'],
        );
    }
}
