<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Section implements CastsAttributes
{
    private const BOOLEAN_KEYS = [
        'show',
        'selected',
        'isCurrent',
        'isDone',
        'soldout',
        'showOnHomepage',
        'showOnHotelpage',
    ];

    private string $locale = 'en';

    public function __construct(string $locale = 'en')
    {
        $this->locale = $locale;
    }

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, ?array $attributes) : mixed
    {
        return $this->walk(\json_decode($value ?? '[]', true) ?? []);
    }

    private function walk(array $input) : array
    {
        foreach ($input as $key => $value) {
            if (\is_array($value)) {
                $input[$key] = $this->walk($value);

                continue;
            }

            if (\in_array($key, static::BOOLEAN_KEYS, true)) {
                $input[$key] = (\is_numeric($value) && ((int) $value === 0)) || $value === 'false' || $value === false
                    ? false
                    : true;
            } else {
                $isKeyCurrentLocale = \str_contains($key, '_' . $this->locale);
                $localeKey = $key . '_' . $this->locale;
                $cleanedKey = \str_replace('_' . $this->locale, '', $key);

                if (isset($input[$localeKey])) {
                    $input[$key] = $input[$localeKey];
                    unset($input[$localeKey]);
                } else if ($isKeyCurrentLocale && !isset($input[$cleanedKey])) {
                    $input[$cleanedKey] = $value;
                    unset($input[$key]);
                }
            }
        }

        return $input;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return \json_encode($value);
    }
}
