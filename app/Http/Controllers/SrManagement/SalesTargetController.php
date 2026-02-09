<?php

namespace App\Http\Controllers\SrManagement;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSalesTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesTargetController extends Controller
{
    public function index(Request $request)
    {
        $targets = UserSalesTarget::with('user')
            ->orderBy('date', 'desc')
            ->paginate(20);

        $filterEmployees = User::whereIn('id', UserSalesTarget::distinct()->pluck('user_id')->filter())
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('backend.sr_management.sales_target.index', compact('targets', 'filterEmployees'));
    }

    public function analytics(Request $request)
    {
        $from = $request->get('from_date');
        $to = $request->get('to_date');
        $userId = $request->get('user_id');

        $query = UserSalesTarget::query();
        if ($from) {
            $query->where('date', '>=', $from);
        }
        if ($to) {
            $query->where('date', '<=', $to);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $totalTargets = (clone $query)->sum('target');
        $totalCompleted = (clone $query)->sum('completed');
        $totalRemains = (clone $query)->sum('remains');
        $totalEmployee = (clone $query)->distinct('user_id')->count('user_id');

        $achievePercent = $totalTargets > 0
            ? round(($totalCompleted / $totalTargets) * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_employee' => $totalEmployee,
                'total_targets' => (float) $totalTargets,
                'sales' => (float) $totalCompleted,
                'achieve_percent' => $achievePercent,
                'remains' => (float) $totalRemains,
            ],
        ]);
    }

    public function usersList(Request $request)
    {
        $q = $request->get('q', '');
        $limit = (int) $request->get('limit', 10);

        $query = User::query()->select('id', 'name', 'phone', 'email');

        if ($q !== '') {
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', '%' . $q . '%')
                    ->orWhere('phone', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%');
            });
        }

        $users = $query->limit($limit)->get();

        $results = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'text' => $user->name . ($user->phone ? ' (' . $user->phone . ')' : ''),
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function create()
    {
        $target = null;
        return view('backend.sr_management.sales_target.create', compact('target'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'target' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:65535',
        ]);

        $target = (float) $validated['target'];
        UserSalesTarget::create([
            'user_id' => $validated['user_id'],
            'date' => $validated['date'],
            'target' => $target,
            'completed' => 0,
            'remains' => $target,
            'is_evaluated' => false,
            'note' => $validated['note'] ?? null,
            'creator_id' => auth()->id(),
        ]);

        return redirect()->route('sales_targets.index')->with('success', 'Sales target created successfully.');
    }

    public function show($id)
    {
        $target = UserSalesTarget::with('user')->findOrFail($id);
        return view('backend.sr_management.sales_target.show', compact('target'));
    }

    public function edit($id)
    {
        $target = UserSalesTarget::with('user')->findOrFail($id);
        return view('backend.sr_management.sales_target.edit', compact('target'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:user_sales_targets,id',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'target' => 'required|numeric|min:0',
            'completed' => 'nullable|numeric|min:0',
            'remains' => 'nullable|numeric',
            'is_evaluated' => 'nullable|boolean',
            'note' => 'nullable|string|max:65535',
        ]);

        $targetModel = UserSalesTarget::findOrFail($validated['id']);
        $targetValue = (float) $validated['target'];
        $completed = isset($validated['completed']) ? (float) $validated['completed'] : $targetModel->completed;
        $remains = isset($validated['remains']) ? (float) $validated['remains'] : ($targetValue - $completed);

        $targetModel->update([
            'user_id' => $validated['user_id'],
            'date' => $validated['date'],
            'target' => $targetValue,
            'completed' => $completed,
            'remains' => $remains,
            'is_evaluated' => !empty($validated['is_evaluated']),
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('sales_targets.index')->with('success', 'Sales target updated successfully.');
    }
}
