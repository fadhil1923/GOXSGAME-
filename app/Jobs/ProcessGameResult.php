<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessGameResult implements ShouldQueue
{
    use Queueable;

    public $gameId;

    public $playerId;

    public $score;

    /**
     * Create a new job instance.
     */
    public function __construct($gameId, $playerId, $score)
    {
        $this->gameId = $gameId;
        $this->playerId = $playerId;
        $this->score = $score;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Logika berat: Menghitung XP yang didapat pemain berdasarkan skor
        $xpGained = $this->score * 1.5;

        // Asumsi kita mengupdate data player di database
        // $player = Player::find($this->playerId);
        // $player->xp += $xpGained;
        // $player->save();

        // 2. Menggunakan Fitur Cache
        // Menyimpan status leaderboard ke dalam cache selama 10 menit
        Cache::remember("leaderboard_game_{$this->gameId}", 600, function () {
            // Logika database berat untuk menyusun ulang klasemen / leaderboard
            return "Leaderboard Data for Game {$this->gameId}";
        });

        Log::info("Game result processed untuk player {$this->playerId}. XP Gained: {$xpGained}");
    }
}
