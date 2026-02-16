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

        // Heatmap Logic (Threat Density)
        $threatMap = [];
        foreach ($tasks as $dateKey => $dayTasks) {
            $score = 0;
            foreach ($dayTasks as $task) {
                if ($task->urgency == 'critical')
                    $score += 3;
                elseif ($task->urgency == 'high')
                    $score += 2;
                else
                    $score += 1;
            }
            $threatMap[$dateKey] = $score > 5 ? 'high' : ($score > 2 ? 'medium' : 'low');
        }

        // Mission Agenda (Upcoming 10 tasks)
        $agenda = \App\Models\Task::where('deadline', '>=', Carbon::now()->startOfDay())
            ->where('completed', false)
            ->orderBy('deadline', 'asc')
            ->orderBy('urgency', 'desc')
            ->limit(10)
            ->get();

        // Calendar Grid Logic
        $startOfWeek = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $calendar = [];
        $current = $startOfWeek->copy();

        while ($current <= $endOfWeek) {
            $calendar[] = $current->copy();
            $current->addDay();
        }

        return view('ops.index', compact('calendar', 'tasks', 'threatMap', 'agenda', 'date'));
    }

    public function updateDate(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'date' => 'required|date',
        ]);

        $task = \App\Models\Task::findOrFail($request->task_id);
        $task->deadline = $request->date;
        $task->save();

        return response()->json(['success' => true, 'message' => 'Directive rescheduled.']);
    }
}
