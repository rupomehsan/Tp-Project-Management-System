<?php

namespace App\Modules\Management\TasksManagement\Tasks\Actions;

class GetAllData
{
    static $model = \App\Modules\Management\TasksManagement\Tasks\Models\Model::class;

    public static function execute()
    {
        try {

            // dd(request()->all());

            $pageLimit = request()->input('limit') ?? 10;
            $orderByColumn = request()->input('sort_by_col') ?? 'priority';

            // Custom priority order: urgent, high, normal, low
            if ($orderByColumn === 'priority') {

                $priorityOrder = "'urgent','high','normal','low'";
                $orderByRaw = "FIELD(priority, $priorityOrder)";
            } else {
                $orderByRaw = null;
            }
            $orderByType = request()->input('sort_type') ?? 'desc';
            $status = request()->input('status') ?? 'active';
            $fields = request()->input('fields') ?? '*';
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            $task_status = request()->input('task_status');
            $priority = request()->input('priority');
            $with = ['user', 'taskGroup', 'projectId'];
            $condition = [];
            $user = auth()->user();

            $data = self::$model::query();

            // --- Role-based filtering ---
            if ($user->role_id == 2) {
                $data = $data->where('assigned_to', $user->id);
            }
            // --- End Role-based filtering ---

            if (request()->has('search') && request()->input('search')) {
                $searchKey = request()->input('search');
                $data = $data->where(function ($q) use ($searchKey) {
                    $q->where('name', 'like', '%' . $searchKey . '%');

                    $q->orWhere('description', 'like', '%' . $searchKey . '%');
                });
            }

            if (request()->has('project_id') && request()->input('project_id')) {
                $data = $data->where('project_id', request()->input('project_id'));
            }

            if ($start_date && $end_date) {
                if ($end_date > $start_date) {
                    $data->whereBetween('start_date', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
                    $data->orWhereBetween('end_date', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
                } elseif ($end_date == $start_date) {
                    $data->whereDate('start_date', $start_date);
                    $data->orWhereDate('end_date', $end_date);
                }
            }


            if ($task_status !== null && $task_status !== '') {
                $data->where('task_status', $task_status);
            }
            if ($priority !== null && $priority !== '') {
                $data->where('priority', $priority);
            }

            if ($status == 'trased') {
                $data = $data->trased();
            }
            $usePriorityOrder = ($orderByColumn === 'priority');
            $priorityOrderRaw = "FIELD(priority, 'urgent', 'normal', 'high', 'medium', 'low')";

            if (request()->has('get_all') && (int)request()->input('get_all') === 1) {
                $data = $data
                    ->with($with)
                    ->select($fields)
                    ->where($condition)
                    ->where('status', $status);
                if ($usePriorityOrder) {
                    $data = $data->orderByRaw($priorityOrderRaw);
                }
                $data = $data->orderBy($orderByColumn, $orderByType)
                    ->get();
                return entityResponse($data);
            } else if ($status == 'trased') {
                $data = $data
                    ->with($with)
                    ->select($fields)
                    ->where($condition);
                if ($usePriorityOrder) {
                    $data = $data->orderByRaw($priorityOrderRaw);
                }
                $data = $data->orderBy($orderByColumn, $orderByType)
                    ->paginate($pageLimit);
            } else {

                $data = $data
                    ->with($with)
                    ->select($fields)
                    ->where($condition)
                    ->where('status', $status);
                if ($usePriorityOrder) {
                    $data = $data->orderByRaw($priorityOrderRaw);
                }
                $data = $data->orderBy($orderByColumn, $orderByType)
                    ->paginate($pageLimit);
            }

            return entityResponse([
                ...$data->toArray(),
                "active_data_count" => self::$model::active()->count(),
                "inactive_data_count" => self::$model::inactive()->count(),
                "trased_data_count" => self::$model::trased()->count(),
            ]);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
