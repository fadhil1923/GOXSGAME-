<?php

use App\Http\Controllers\Admin\AiImportController;
use App\Http\Controllers\Admin\DevelopmentMessageController as AdminDevelopmentMessageController;
use App\Http\Controllers\Admin\GameController as AdminGameController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\AiNpcController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevelopmentMessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\RoomController as StudentRoomController;
use App\Http\Controllers\Teacher\GameSessionController;
use App\Http\Controllers\Teacher\RoomController as TeacherRoomController;
use Illuminate\Support\Facades\Route;

// Root: redirect ke halaman siswa (join) secara default
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        // Jika guru atau admin, masuk ke dashboard guru/admin
        if ($user->role === 'admin' || $user->role === 'teacher') {
            return redirect()->route('dashboard');
        }
    }

    // Jika guest atau student, langsung ke halaman join PIN
    return redirect()->route('student.join');
});

// Dashboard: redirect berdasarkan role
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile (Breeze bawaan)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ===== ADMIN & TEACHER MANAGE ROUTES =====
Route::middleware(['auth', 'verified', 'role:admin,teacher'])
    ->prefix('manage')
    ->name('manage.')
    ->group(function () {
        Route::resource('games', AdminGameController::class);

        // Master Data & User Management (Khusus Admin)
        Route::middleware('role:admin')->group(function () {
            Route::resource('users', UserController::class)->except(['show']);
            Route::resource('subjects', SubjectController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::resource('classes', SchoolClassController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
            Route::resource('feedback', AdminDevelopmentMessageController::class)->only(['index', 'update', 'destroy']);
        });

        // Soal per game (nested resource)
        Route::prefix('games/{game}/questions')->name('games.questions.')->group(function () {
            Route::get('/ai-import', [AiImportController::class, 'create'])->name('ai_import');
            Route::post('/ai-import', [AiImportController::class, 'store'])->name('ai_import.store');

            Route::get('/ai-generate', [AiImportController::class, 'createText'])->name('ai_generate');
            Route::post('/ai-generate', [AiImportController::class, 'storeText'])->name('ai_generate.store');

            Route::get('/', [AdminQuestionController::class, 'index'])->name('index');
            Route::get('/create', [AdminQuestionController::class, 'create'])->name('create');
            Route::post('/', [AdminQuestionController::class, 'store'])->name('store');
            Route::get('/{question}/edit', [AdminQuestionController::class, 'edit'])->name('edit');
            Route::patch('/{question}', [AdminQuestionController::class, 'update'])->name('update');
            Route::delete('/{question}', [AdminQuestionController::class, 'destroy'])->name('destroy');
        });
    });

// ===== TEACHER ROUTES =====
Route::middleware(['auth', 'verified', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::resource('rooms', TeacherRoomController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

        // Game Session Control
        Route::get('rooms/{room}/lobby', [GameSessionController::class, 'lobby'])->name('rooms.lobby');
        Route::get('rooms/{room}/students', [GameSessionController::class, 'students'])->name('rooms.students');
        Route::post('rooms/{room}/play', [GameSessionController::class, 'play'])->name('rooms.play');
        Route::post('rooms/{room}/next', [GameSessionController::class, 'next'])->name('rooms.next');
        Route::post('rooms/{room}/end', [GameSessionController::class, 'end'])->name('rooms.end');
        Route::get('rooms/{room}/results', [GameSessionController::class, 'results'])->name('rooms.results');
        Route::get('rooms/{room}/leaderboard-data', [GameSessionController::class, 'leaderboardData'])->name('rooms.leaderboardData');
    });

// ===== STUDENT PUBLIC ROUTES (JOIN) =====
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/join', [StudentRoomController::class, 'join'])->name('join');
    Route::post('/join', [StudentRoomController::class, 'enter'])->name('enter');
});

// ===== STUDENT ROUTES (AUTH REQUIRED) =====
Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', [StudentRoomController::class, 'dashboard'])->name('dashboard');
        Route::get('/room/{room}', [StudentRoomController::class, 'show'])->name('room');
        Route::get('/room/{room}/state', [StudentRoomController::class, 'state'])->name('room.state');
        Route::post('/room/{room}/answer', [StudentRoomController::class, 'answer'])->name('room.answer');
        Route::post('/room/{room}/powerup', [StudentRoomController::class, 'usePowerup'])->name('room.powerup');
        Route::get('/room/{room}/result', [StudentRoomController::class, 'result'])->name('result');
    });

// ===== AI NPC ROUTE (Laravel 13 AI SDK Example) =====
Route::post('/api/ai/npc/interact', [AiNpcController::class, 'interact'])->name('api.ai.npc.interact');

// ===== PESAN PENGEMBANGAN (FEEDBACK) GURU & SISWA =====
Route::get('/feedback', [DevelopmentMessageController::class, 'create'])->name('feedback.create');
Route::post('/feedback', [DevelopmentMessageController::class, 'store'])->name('feedback.store');

require __DIR__.'/auth.php';
