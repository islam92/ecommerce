<?php

namespace App\Models;

use App\Observers\MainCategoryObserve;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainCategory extends Model
{
    use HasFactory;

    protected $table = 'main_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'translation_lang',
        'translation_of',
        'name',
        'slug',
        'photo',
        'active',
        'created_at',
        'updated_at',
    ];

    protected static function boot(){
        parent::boot();
        MainCategory::observe(MainCategoryObserve::class);
    }

    public function scopeActive($query){
        return $query->where('active', 1);
    }

    public function scopeSelection($query){
        return $query->select('id', 'translation_lang', 'name', 'slug', 'photo', 'active', 'translation_of');
    }


    public function getPhotoAttribute($val)
    {
        return ($val !== null) ? asset('assets/' . $val) : "";

    }


    public function scopeDefaultCategory($query){
        return  $query -> where('translation_of',0);
    }

    public function getActive() {
        return $this->active == 1 ? 'active' : 'inactive';
    }

    /**
     * Get all of the comments for the MainCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(self::class, 'translation_of');
    }


    /**
     * Get all of the vendors for the MainCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class, 'category_id', 'id');
    }

    /**
     * Get all of the subCategories for the MainCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class, 'category_id', 'id');
    }

}
