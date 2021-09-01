<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendors';

    protected $fillable = [
        'latitude', 'longitude','name', 'mobile', 'password', 'address', 'email', 'logo', 'category_id', 'active', 'created_at', 'updated_at'
    ];

    protected $hidden = ['category_id', 'password'];

    public function scopeActive($query) {
        return $query->where('active', 1);
    }

    public function scopeSelection($query) {
        return $query->select('latitude', 'longitude', 'id', 'category_id', 'active', 'name', 'address', 'email', 'logo', 'mobile');
    }


    public function getActive() {
        return $this->active == 1 ? 'active' : 'inactive';
    }

    public function setPasswordAttribute($password){
        if(!empty($password)) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

    public function getLogoAttribute($val)
    {
        return ($val !== null) ? asset('assets/' . $val) : "";
    }


    /**
     * Get the user that owns the Vendor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(MainCategory::class, 'category_id', 'id');
    }


}
