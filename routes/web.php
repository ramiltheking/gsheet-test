<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\SheetController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, "index"]);

Route::post("/truncate", [ProductController::class, "truncateDB"]);
Route::post("/fill-table", [ProductController::class, "fillTable"]);

Route::post("/change-url", [SheetController::class, "changeURL"]);
Route::post("/pull-data", [SheetController::class, "pullData"]);
Route::get("/sandbox", [SheetController::class, "sandbox"]);
Route::get("/fetch", [SheetController::class, "displaySheetData"]);
Route::get("/fetch/{count}", [SheetController::class, "displaySheetData"]);
