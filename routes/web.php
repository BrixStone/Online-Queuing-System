<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserControllers;
use Livewire\Volt\Volt;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/tracker', function () {
    return view('show');
})->name('user.show');



Volt::route('/cashier', 'cashier-dashboard');

Route::post('/submit', [UserControllers::class ,'store'])->name('submit.form');
Route::get('/queue/check-device', [UserControllers::class, 'checkDevice']);


Route::get('/queue/status/{token}', [UserControllers::class, 'status'])->name('queue.status')->whereUuid('token');;

// Route::post('/submit-form', function (Request $request) {
//     $validatedData = $request->validate([
//         'name' => 'required|string|max:255',
//     ]);
//     return response()->json(['message' => 'Form submitted successfully!', 'data' => $validatedData]);

//     return redirect('/')->with('success', 'Form submitted successfully!');
// })->name('submit.form');
