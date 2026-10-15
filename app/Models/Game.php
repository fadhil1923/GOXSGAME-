<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'thumbnail_url', 'game_url', 'xp_reward', 'user_id', 'subject_id', 'topic_id', 'grade_level', 'difficulty'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    /**
     * Katalog thumbnail bawaan beresolusi tinggi berdasarkan mata pelajaran.
     */
    public static function defaultSubjectThumbnails(): array
    {
        return [
            'Matematika' => 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?w=800&auto=format&fit=crop&q=80',
            'IPA' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=800&auto=format&fit=crop&q=80',
            'IPS' => 'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?w=800&auto=format&fit=crop&q=80',
            'Bahasa Indonesia' => 'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?w=800&auto=format&fit=crop&q=80',
            'Bahasa Inggris' => 'https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?w=800&auto=format&fit=crop&q=80',
            'Coding' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format&fit=crop&q=80',
            'Seni Budaya' => 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?w=800&auto=format&fit=crop&q=80',
            'Olahraga' => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=800&auto=format&fit=crop&q=80',
            'Default' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=800&auto=format&fit=crop&q=80',
        ];
    }

    /**
     * Resolusi URL thumbnail otomatis bila thumbnail_url manual kosong.
     */
    public function getAutoThumbnailAttribute(): string
    {
        if (! empty($this->thumbnail_url)) {
            return $this->thumbnail_url;
        }

        $subjectName = $this->subject?->name;
        $catalog = self::defaultSubjectThumbnails();

        if ($subjectName && isset($catalog[$subjectName])) {
            return $catalog[$subjectName];
        }

        // Cek jika ada kecocokan parsial dari nama mata pelajaran
        if ($subjectName) {
            foreach ($catalog as $key => $url) {
                if (stripos($subjectName, $key) !== false || stripos($key, $subjectName) !== false) {
                    return $url;
                }
            }
        }

        return $catalog['Default'];
    }
}
