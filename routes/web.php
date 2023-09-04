<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

Route::get('/', function () {
    return "hello world!!";
});

Route::get('{storage}/api/{endpoint}', function ($storage, $endpoint) {
    $page = request()->query('page', 1);
    $perPage = request()->query('per_page', 10);

    $storageKey = "{$storage}_{$endpoint}";

    $data = Cache::get($storageKey, []);
    $totalItems = count($data);

    usort($data, function ($a, $b) {
        return $b['id'] - $a['id'];
    });

    $currentPageItems = array_slice($data, ($page - 1) * $perPage, $perPage);
    $totalPages = ceil($totalItems / $perPage);

    return response()->json([
        "data" => $currentPageItems,
        "meta" => [
            "total" => $totalItems,
            "current_page" => $page,
            "per_page" => $perPage,
            "total_pages" => $totalPages,
        ],
    ]);
});

Route::post('{storage}/api/{endpoint}', function ($storage, $endpoint) {
    $requestData = request()->all();
    $storageKey = "{$storage}_{$endpoint}";

    $data = Cache::get($storageKey, []);
    $nextId = Cache::get("{$storage}_{$endpoint}_nextId", 1);

    $now = Carbon::now();
    $newData = [
        'id' => $nextId,
        'created_at' => $now,
        'date' => $now->formatLocalized('%A, %e %B %Y'),
        'time' => $now->format('H:i:s'),
    ];
    $requestData = $newData + $requestData;

    $data[] = $requestData;
    Cache::put($storageKey, $data);

    Cache::put("{$storage}_{$endpoint}_nextId", $nextId + 1);

    return response()->json($requestData, 201);
});
Route::put('{storage}/api/{endpoint}/{id}', function ($storage, $endpoint, $id) {
    $requestData = request()->all();
    $storageKey = "{$storage}_{$endpoint}";

    $data = Cache::get($storageKey, []);

    $updated = false;

    foreach ($data as $key => $item) {
        if ($item['id'] == $id) {
            $data[$key] = array_merge($item, $requestData);
            $updated = true;
            break;
        }
    }

    if ($updated) {
        Cache::put($storageKey, $data);
        return response()->json($data[$key]);
    } else {
        return response()->json(['message' => 'Data not found'], 404);
    }
});

// DELETE /{storage}/{endpoint}/{id}
Route::delete('{storage}/api/{endpoint}/{id}', function ($storage, $endpoint, $id) {
    $storageKey = "{$storage}_{$endpoint}";
    $data = Cache::get($storageKey, []);

    $success = false;
    foreach ($data as $key => $item) {
        if ($item['id'] == $id) {
            unset($data[$key]);
            $success = true;
            break;
        }
    }

    if ($success) {
        Cache::put($storageKey, $data);
        return response()->json([
            "data" => [
                "message" => "Data deleted"
            ]
        ]);
    } else {
        return response()->json(['error' => 'Item not found'], 404);
    }
});

Route::delete('{storage}/api/{endpoint}/action/delete-all', function ($storage, $endpoint) {
    $storageKey = "{$storage}_{$endpoint}";
    Cache::forget($storageKey);
    Cache::forget("{$storage}_{$endpoint}_nextId");
    return response()->json(['message' => 'All data deleted'], 200);
});
