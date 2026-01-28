<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class JobListing extends Model
{
    use HasFactory;

    protected $table = 'jobs_listing';

    protected $fillable = [
        'employer_id',
        'title',
        'slug',
        'description',
        'location',
        'job_type',
        'salary_min',
        'salary_max',
        'company_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($job) {
            if (empty($job->slug)) {
                $job->slug = Str::slug($job->title);
            }
        });
    }

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'job_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
