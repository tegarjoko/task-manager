<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class OpsController extends Controller
{
    public function index(Request $request)
    {
        // Get current month/year or default to now
        $date = $request->has('date') ?Carbon::parse($request->date) : Carbon::now();

        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        // Get tasks with deadlines in this month
        $tasks = \App\Models\Task::whereBetween('deadline', [$startOfMonth, $endOfMonth])
            ->orderBy('deadline')
            ->get()
            ->groupBy(function ($task) {
            return Carbon::parse($task->deadline)->format('Y-m-d');
        });

        // Calendar Grid Logic
        $startOfWeek = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $calendar = [];
        $current = $startOfWeek->copy();

        while ($current <= $endOfWeek) {
            $calendar[] = $current->copy();
            $current->addDay();
        }

        return view('ops.index', compact('calendar', 'tasks', 'date'));
    }
}
