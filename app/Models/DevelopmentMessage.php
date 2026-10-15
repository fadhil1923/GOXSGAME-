<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevelopmentMessage extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'role',
        'category',
        'subject',
        'message',
        'status',
        'admin_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'feature_request' => '💡 Saran Fitur Baru',
            'bug_report' => '🐛 Laporan Kendala / Bug',
            'ui_ux' => '🎨 Tampilan & Desain',
            'question' => '❓ Pertanyaan / Bantuan',
            default => '💬 Masukan Lainnya',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'read' => 'Sudah Dibaca',
            'in_progress' => 'Sedang Diproses',
            'resolved' => 'Selesai Ditanggapi',
            default => 'Menunggu Review',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'read' => 'bg-[#2563EB] text-white',
            'in_progress' => 'bg-[#06B6D4] text-black',
            'resolved' => 'bg-[#4ADE80] text-black',
            default => 'bg-[#FFE600] text-black',
        };
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            'teacher' => 'bg-[#4ADE80] text-black',
            'student' => 'bg-[#2563EB] text-white',
            'admin' => 'bg-[#FB7185] text-black',
            default => 'bg-stone-300 text-black',
        };
    }
}
