<?php

namespace App\Modules\Management\TodoManagement\AddTodo\Actions;

class GetAllData
{
    static $model = \App\Modules\Management\TodoManagement\AddTodo\Models\Model::class;

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

            $with = ['categoryId'];
            $condition = [];

            $data = self::$model::query();

            if (auth()->user()) {
                $data = $data->where('user_id', auth()->id());
            }

            if (request()->has('search') && request()->input('search')) {
                $searchKey = request()->input('search');
                $data = $data->where(function ($q) use ($searchKey) {
                    $q->where('name', 'like', '%' . $searchKey . '%');

                    $q->orWhere('description', 'like', '%' . $searchKey . '%');
                });
            }

            if ($start_date && $end_date) {
                if ($end_date > $start_date) {
                    $data->whereBetween('created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
                } elseif ($end_date == $start_date) {
                    $data->whereDate('created_at', $start_date);
                }
            }

            if ($status == 'trased') {
                $data = $data->trased();
            }

            if ($task_status !== null && $task_status !== '') {
                $data->where('task_status', $task_status);
            }
            if ($priority !== null && $priority !== '') {
                $data->where('priority', $priority);
            }

            if (request()->has('get_all') && (int)request()->input('get_all') === 1) {
                $data = $data
                    ->with($with)
                    ->select($fields)
                    ->where($condition)
                    ->where('status', $status)
                    // ->limit($pageLimit)
                    ->orderBy($orderByColumn, $orderByType)
                    ->get();
                return entityResponse($data);
            } else if ($status == 'trased') {
                $data = $data
                    ->with($with)
                    ->select($fields)
                    ->where($condition)
                    ->orderBy($orderByColumn, $orderByType)
                    ->paginate($pageLimit);
            } else {
                $data = $data
                    ->with($with)
                    ->select($fields)
                    ->where($condition)
                    ->where('status', $status)
                    ->orderBy($orderByColumn, $orderByType)
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
