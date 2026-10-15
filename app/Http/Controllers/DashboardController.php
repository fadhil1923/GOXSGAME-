<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Room;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use App\Models\VisitorLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            // Data statistik untuk dashboard admin
            $totalVisitors = VisitorLog::count();
            $todayVisitors = VisitorLog::whereDate('created_at', today())->count();
            $recentVisitors = VisitorLog::with('user')->latest()->take(8)->get();

            $totalUsers = User::count();
            $totalTeachers = User::where('role', 'teacher')->count();
            $totalGames = Game::count();
            $totalRooms = Room::count();
            $totalSubjects = Subject::count();
            $totalClasses = SchoolClass::count();

            return view('dashboard', compact(
                'totalVisitors',
                'todayVisitors',
                'recentVisitors',
                'totalUsers',
                'totalTeachers',
                'totalGames',
                'totalRooms',
                'totalSubjects',
                'totalClasses'
            ));
        }

        return match ($user->role) {
            'teacher' => redirect()->route('teacher.rooms.index'),
            'student' => redirect()->route('student.dashboard'),
            default => redirect()->route('student.join'),
        };
    }
}
