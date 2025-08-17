<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'inv_categories';
    protected $fillable = [
        'name',
        'types_id',
        'slug',
        'enabled',
        'sort',
    ];

    public function generateSlug(): string
    {
        $words = explode(' ', $this->name);
        $slug = '';

        if (count($words) === 1) {
            $slug = strtoupper(substr($words[0], 0, 2));
        } else {
            $slug = strtoupper(substr($words[0], 0, 2)) . strtoupper(substr(end($words), 0, 1));
        }
        $slug = $this->makeSlugUnique($slug);
        return $slug;
    }
    /**
     * Check and modify the slug to ensure uniqueness.
     * @param string $slug
     * @return string
     */
    protected function makeSlugUnique(string $slug): string
    {
        while (self::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $words = explode(' ', $this->name);
            if (count($words) > 1) {
                $slug = strtoupper(substr($words[0], 0, 1)) . strtoupper(substr($words[1], 0, 2));
            } else {
                $slug = strtoupper(substr($words[0], 0, 2));
            }
        }
        return $slug;
    }
    public function type()
    {
        return $this->belongsTo(Type::class, 'types_id');
    }
    public function items()
    {
        return $this->hasMany(Item::class, 'category_id');
    }

    public function countItems(): int
    {
        return $this->items()
            ->join('inv_stocks', 'inv_items.id', '=', 'inv_stocks.item_id')
            ->sum('inv_stocks.quantity');
    }

}
