<?php

/*
|--------------------------------------------------------------------------
| Rutas de módulos generados
|--------------------------------------------------------------------------
|
| php artisan make:modulo agrega acá las rutas de cada módulo nuevo.
| Este archivo se carga desde routes/web.php, dentro del grupo AdminAuth.
|
*/

use Illuminate\Support\Facades\Route;

// Familias (make:modulo)
Route::get('/admin/familia', [\App\Http\Controllers\FamiliaController::class, 'index'])->name('adm.familia');
Route::get('/admin/familia/creador', [\App\Http\Controllers\FamiliaController::class, 'create'])->name('adm.familia-creador');
Route::post('/admin/familia/store', [\App\Http\Controllers\FamiliaController::class, 'store'])->name('adm.familia-store');
Route::get('/admin/familia/editor/{id}', [\App\Http\Controllers\FamiliaController::class, 'show'])->name('adm.familia-editor');
Route::post('/admin/familia/update/{id}', [\App\Http\Controllers\FamiliaController::class, 'update'])->name('adm.familia-update');
Route::get('/admin/familia/destroy/{id}', [\App\Http\Controllers\FamiliaController::class, 'destroy'])->name('adm.familia-destroy');
Route::get('/admin/familia/switch/{id}', [\App\Http\Controllers\FamiliaController::class, 'switch'])->name('adm.familia-switch');
Route::get('/admin/familia/destacado/{id}', [\App\Http\Controllers\FamiliaController::class, 'destacado'])->name('adm.familia-destacado');
Route::post('/admin/familia/reordenar', [\App\Http\Controllers\FamiliaController::class, 'reordenar'])->name('adm.familia-reordenar');

// Sectores (make:modulo)
Route::get('/admin/sectore', [\App\Http\Controllers\SectoreController::class, 'index'])->name('adm.sectore');
Route::get('/admin/sectore/creador', [\App\Http\Controllers\SectoreController::class, 'create'])->name('adm.sectore-creador');
Route::post('/admin/sectore/store', [\App\Http\Controllers\SectoreController::class, 'store'])->name('adm.sectore-store');
Route::get('/admin/sectore/editor/{id}', [\App\Http\Controllers\SectoreController::class, 'show'])->name('adm.sectore-editor');
Route::post('/admin/sectore/update/{id}', [\App\Http\Controllers\SectoreController::class, 'update'])->name('adm.sectore-update');
Route::get('/admin/sectore/destroy/{id}', [\App\Http\Controllers\SectoreController::class, 'destroy'])->name('adm.sectore-destroy');
Route::get('/admin/sectore/switch/{id}', [\App\Http\Controllers\SectoreController::class, 'switch'])->name('adm.sectore-switch');
Route::get('/admin/sectore/destacar/{id}', [\App\Http\Controllers\SectoreController::class, 'destacar'])->name('adm.sectore-destacar');
Route::post('/admin/sectore/reordenar', [\App\Http\Controllers\SectoreController::class, 'reordenar'])->name('adm.sectore-reordenar');

// Distribuidores (make:modulo)
Route::get('/admin/distribuidore', [\App\Http\Controllers\DistribuidoreController::class, 'index'])->name('adm.distribuidore');
Route::get('/admin/distribuidore/creador', [\App\Http\Controllers\DistribuidoreController::class, 'create'])->name('adm.distribuidore-creador');
Route::post('/admin/distribuidore/store', [\App\Http\Controllers\DistribuidoreController::class, 'store'])->name('adm.distribuidore-store');
Route::get('/admin/distribuidore/editor/{id}', [\App\Http\Controllers\DistribuidoreController::class, 'show'])->name('adm.distribuidore-editor');
Route::post('/admin/distribuidore/update/{id}', [\App\Http\Controllers\DistribuidoreController::class, 'update'])->name('adm.distribuidore-update');
Route::get('/admin/distribuidore/destroy/{id}', [\App\Http\Controllers\DistribuidoreController::class, 'destroy'])->name('adm.distribuidore-destroy');
Route::get('/admin/distribuidore/switch/{id}', [\App\Http\Controllers\DistribuidoreController::class, 'switch'])->name('adm.distribuidore-switch');
Route::post('/admin/distribuidore/reordenar', [\App\Http\Controllers\DistribuidoreController::class, 'reordenar'])->name('adm.distribuidore-reordenar');

// Compromisos (make:modulo)
Route::get('/admin/compromiso', [\App\Http\Controllers\CompromisoController::class, 'index'])->name('adm.compromiso');
Route::get('/admin/compromiso/creador', [\App\Http\Controllers\CompromisoController::class, 'create'])->name('adm.compromiso-creador');
Route::post('/admin/compromiso/store', [\App\Http\Controllers\CompromisoController::class, 'store'])->name('adm.compromiso-store');
Route::get('/admin/compromiso/editor/{id}', [\App\Http\Controllers\CompromisoController::class, 'show'])->name('adm.compromiso-editor');
Route::post('/admin/compromiso/update/{id}', [\App\Http\Controllers\CompromisoController::class, 'update'])->name('adm.compromiso-update');
Route::get('/admin/compromiso/destroy/{id}', [\App\Http\Controllers\CompromisoController::class, 'destroy'])->name('adm.compromiso-destroy');
Route::get('/admin/compromiso/switch/{id}', [\App\Http\Controllers\CompromisoController::class, 'switch'])->name('adm.compromiso-switch');
Route::post('/admin/compromiso/reordenar', [\App\Http\Controllers\CompromisoController::class, 'reordenar'])->name('adm.compromiso-reordenar');

// Soportes (make:modulo)
Route::get('/admin/soporte', [\App\Http\Controllers\SoporteController::class, 'show'])->name('adm.soporte');
Route::post('/admin/soporte/update', [\App\Http\Controllers\SoporteController::class, 'update'])->name('adm.soporte-update');

// Postventas (make:modulo)
Route::get('/admin/postventa', [\App\Http\Controllers\PostventaController::class, 'show'])->name('adm.postventa');
Route::post('/admin/postventa/update', [\App\Http\Controllers\PostventaController::class, 'update'])->name('adm.postventa-update');

// Producto Clientes (make:modulo)
Route::get('/admin/producto-cliente', [\App\Http\Controllers\ProductoClienteController::class, 'index'])->name('adm.producto-cliente');
Route::get('/admin/producto-cliente/creador', [\App\Http\Controllers\ProductoClienteController::class, 'create'])->name('adm.producto-cliente-creador');
Route::post('/admin/producto-cliente/store', [\App\Http\Controllers\ProductoClienteController::class, 'store'])->name('adm.producto-cliente-store');
Route::get('/admin/producto-cliente/editor/{id}', [\App\Http\Controllers\ProductoClienteController::class, 'show'])->name('adm.producto-cliente-editor');
Route::post('/admin/producto-cliente/update/{id}', [\App\Http\Controllers\ProductoClienteController::class, 'update'])->name('adm.producto-cliente-update');
Route::get('/admin/producto-cliente/destroy/{id}', [\App\Http\Controllers\ProductoClienteController::class, 'destroy'])->name('adm.producto-cliente-destroy');
Route::get('/admin/producto-cliente/switch/{id}', [\App\Http\Controllers\ProductoClienteController::class, 'switch'])->name('adm.producto-cliente-switch');
