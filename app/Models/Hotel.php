<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Casts\Section;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Random\Engine\Secure;

class Hotel extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'show',
        'destination_id',
        'hero',
        'general',
        'overview',
        'location',
        'video',
        'suitesAndRooms',
        'activities',
        'dining',
        'fitness',
        'wedding',
        'offers',
        'discover',
        'gallery',
        'complexGallery',
        'awards',
        'faq',
        'catalogue',
        'map',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'show' => 'boolean',

        'hero' => Section::class,
        'general' => Section::class,
        'overview' => Section::class,
        'location' => Section::class,
        'video' => Section::class,
        'suitesAndRooms' => Section::class,
        'activities' => Section::class,
        'dining' => Section::class,
        'fitness' => Section::class,
        'wedding' => Section::class,
        'offers' => Section::class,
        'discover' => Section::class,
        'gallery' => Section::class,
        'complexGallery' => Section::class,
        'awards' => Section::class,
        'faq' => Section::class,
        'catalogue' => Section::class,
        'map' => Section::class,
    ];

    public function destination() : HasOne
    {
        return $this->hasOne(Destination::class, 'slug', 'destination_id');
    }

    protected function destinationId() : Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Destination::where('id', $value)->get()->first()->slug,
        );
    }
}
