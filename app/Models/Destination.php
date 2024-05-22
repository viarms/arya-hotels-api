<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Casts\Section;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Destination extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $appends = [
        'hotelsCount',
        'lowestPrice',
        'availableHotels',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'show',
        'hero',
        'general',
        'intro',
        'hotels',
        'about',
        'benefits',
        'gallery',
        'services',
        'faq',
        'catalogue',
        'adventures',
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

        'intro' => Section::class,
        'hotels' => Section::class,
        'about' => Section::class,
        'benefits' => Section::class,
        'gallery' => Section::class,
        'services' => Section::class,
        'faq' => Section::class,
        'catalogue' => Section::class,
        'adventures' => Section::class,
    ];

    public function availableHotels() : HasMany
    {
        return $this->hasMany(Hotel::class, 'destination_id', 'id')->where('show', true);
    }

    public function getAvailableHotelsAttribute() : \Illuminate\Database\Eloquent\Collection
    {
        return $this->availableHotels()->get(['id', 'show', 'slug', 'general']);
    }

    public function getHotelsCountAttribute() : int
    {
        return $this->availableHotels()->count('*');
    }

    public function getLowestPriceAttribute() : int
    {
        $hotels = $this->availableHotels()->get();
        $lowestPrice = 999999999999;

        foreach ($hotels as $hotel) {
            $price = (int) $hotel->general['price'];

            if ($price === 0) {
                continue;
            }

            if ($price < $lowestPrice) {
                $lowestPrice = $price;
            }
        }

        return $lowestPrice;
    }
}
