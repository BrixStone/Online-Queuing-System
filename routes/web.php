<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});


Route::post('/submit-form', function (Request $request) {
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
    ]);
    return response()->json(['message' => 'Form submitted successfully!', 'data' => $validatedData]);

    return redirect('/')->with('success', 'Form submitted successfully!');
})->name('submit.form');