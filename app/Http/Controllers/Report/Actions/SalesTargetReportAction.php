<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\UserSalesTarget;
use App\Models\User;
use Carbon\Carbon;

class SalesTargetReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $query = UserSalesTarget::query();

        if (!empty($filters['date_from'])) {
            $query->where('date', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (!empty($filters['date_to'])) {
            $query->where('date', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        $targets = $query->with('user')->get();

        // Group by user
        $users = [];
        foreach ($targets as $target) {
            $userId = $target->user_id;
            if (!isset($users[$userId])) {
                $users[$userId] = [
                    'user_id' => $userId,
                    'user_name' => $target->user ? $target->user->name : 'Unknown',
                    'target' => 0,
                    'completed' => 0,
                    'remains' => 0,
                ];
            }
            $users[$userId]['target'] += $target->target;
            $users[$userId]['completed'] += $target->completed;
            $users[$userId]['remains'] += $target->remains;
        }

        $data = [];
        foreach ($users as $user) {
            $achievementPercent = $user['target'] > 0 
                ? round(($user['completed'] / $user['target']) * 100, 2) 
                : 0;
            $extra = $user['completed'] > $user['target'] 
                ? $user['completed'] - $user['target'] 
                : 0;
            $shortfall = $user['target'] > $user['completed'] 
                ? $user['target'] - $user['completed'] 
                : 0;

            $data[] = array_merge($user, [
                'achievement_percent' => $achievementPercent,
                'extra' => $extra,
                'shortfall' => $shortfall,
                'achieved' => $user['completed'] >= $user['target'],
            ]);
        }

        $summary = [
            'total_users' => count($data),
            'total_target' => array_sum(array_column($data, 'target')),
            'total_completed' => array_sum(array_column($data, 'completed')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Sales Target Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Salesman', 'Target', 'Completed', 'Remains', 'Achievement %', 'Extra', 'Shortfall'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['user_name'] ?? '',
                $item['target'] ?? 0,
                $item['completed'] ?? 0,
                $item['remains'] ?? 0,
                $item['achievement_percent'] ?? 0,
                $item['extra'] ?? 0,
                $item['shortfall'] ?? 0,
            ];
        }
        return $rows;
    }

    public function getFiltersConfig(): array
    {
        return array_merge(parent::getFiltersConfig(), [
            [
                'type' => 'select',
                'name' => 'user_id',
                'label' => 'Salesman',
                'required' => false,
            ],
        ]);
    }
}
