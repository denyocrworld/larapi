<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::get('/', function () {
    return "hello world";
});
Route::get('/api/{endpoint}', function ($endpoint) {
    $page = request()->query('page', 1); // Halaman saat ini
    $perPage = request()->query('per_page', 10); // Jumlah item per halaman

    $data = Cache::get($endpoint, []);
    $totalItems = count($data);

    // Mengurutkan data berdasarkan id secara descending
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
Route::post('/api/{endpoint}', function ($endpoint) {
    $requestData = request()->all();

    // Mendapatkan data terakhir yang ada di cache untuk endpoint
    $data = Cache::get($endpoint, []);

    // Mendapatkan nilai nextId dari cache atau mengatur ke 1 jika belum ada
    $nextId = Cache::get("{$endpoint}_nextId", 1);

    // Menambahkan ID dan created_at ke data yang akan disimpan
    // $requestData['id'] = $nextId;
    $now = Carbon::now();
    $newData = [
        'id' => $nextId,
        'created_at' => $now,
        'date' => $now->formatLocalized('%A, %e %B %Y'),
        'time' => $now->format('H:i:s'),
    ];
    $requestData = $newData + $requestData;

    // Menyimpan data baru dengan ID di cache
    $data[] = $requestData;
    Cache::put($endpoint, $data);

    // Menyimpan nilai nextId yang sudah ditingkatkan di cache
    Cache::put("{$endpoint}_nextId", $nextId + 1);

    return response()->json($requestData, 201);
});

Route::put('/api/{endpoint}/{id}', function ($endpoint, $id) {
    $requestData = request()->all();
    $data = Cache::get($endpoint, []);

    $updated = false;

    foreach ($data as $key => $item) {
        if ($item['id'] == $id) {
            // Memperbarui hanya kolom yang diperlukan berdasarkan requestData
            $data[$key] = array_merge($item, $requestData);
            $updated = true;
            break;
        }
    }

    if ($updated) {
        Cache::put($endpoint, $data);
        return response()->json($data[$key]);
    } else {
        return response()->json(['message' => 'Data not found'], 404);
    }
});

// DELETE /api/{endpoint}/{id}
Route::delete('/api/{endpoint}/{id}', function ($endpoint, $id) {
    $data = Cache::get($endpoint, []);

    if (isset($data[$id])) {
        $deletedItem = $data[$id];
        unset($data[$id]);
        Cache::put($endpoint, $data);
        return response()->json($deletedItem);
    } else {
        return response()->json(['error' => 'Item not found'], 404);
    }
});

Route::delete('/api/{endpoint}/action/delete-all', function ($endpoint) {
    Cache::forget($endpoint);
    Cache::forget("{$endpoint}_nextId");

    return response()->json(['message' => 'All data deleted'], 200);
});
