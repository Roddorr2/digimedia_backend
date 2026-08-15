<?php

use App\Http\Controllers\Api\ContactanosController;
use App\Http\Controllers\Api\ModalesController;
use App\Http\Controllers\Api\ReclamacionesController;
use App\Http\Controllers\Api\ServiciosController;
use App\Http\Controllers\Api\ModalServiciosController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

// Serve images from storage with proper MIME types (fixes CORB issues)
Route::get('/storage/images/{path}', function ($path) {
    $filePath = storage_path('app/public/images/' . $path);
    
    if (!File::exists($filePath)) {
        abort(404);
    }
    
    $mimeType = File::mimeType($filePath) ?: 'image/webp';
    
    $headers = [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline',
        'Cache-Control' => 'public, max-age=31536000',
    ];
    
    return Response::file($filePath, $headers);
})->where('path', '.*');
