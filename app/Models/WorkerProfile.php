<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'nationality_country_code',
        'birth_year',
        'education_summary',
        'work_experience',
        'skills',
        'recommendations',
        'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toSnapshot(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'nationality_country_code' => $this->nationality_country_code,
            'birth_year' => $this->birth_year,
            'education_summary' => $this->education_summary,
            'work_experience' => $this->work_experience,
            'skills' => $this->skills,
            'recommendations' => $this->recommendations,
            'photo_path' => $this->photo_path,
        ];
    }
}
