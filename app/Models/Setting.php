<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'order',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get value with proper type casting
     */
    public function getTypedValue()
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $this->value;
            case 'json':
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }

    /**
     * Set value with proper type conversion
     */
    public function setTypedValue($value)
    {
        switch ($this->type) {
            case 'boolean':
                $this->value = $value ? '1' : '0';
                break;
            case 'integer':
                $this->value = (string) $value;
                break;
            case 'json':
                $this->value = json_encode($value);
                break;
            default:
                $this->value = $value;
        }
    }

    /**
     * Clear cache when updating
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('settings');
        });

        static::deleted(function () {
            Cache::forget('settings');
        });
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAllAsArray(): array
    {
        return Cache::remember('settings', 3600, function () {
            $settings = [];
            foreach (self::all() as $setting) {
                $settings[$setting->key] = $setting->getTypedValue();
            }
            return $settings;
        });
    }

    /**
     * Get setting value by key
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->getTypedValue() : $default;
    }

    /**
     * Set setting value by key
     */
    public static function setValue(string $key, $value): void
    {
        $setting = self::firstOrCreate(['key' => $key]);
        $setting->setTypedValue($value);
        $setting->save();
    }

    /**
     * Get settings by group
     */
    public static function getByGroup(string $group): array
    {
        return self::where('group', $group)
            ->orderBy('order')
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->getTypedValue()];
            })
            ->toArray();
    }
}

