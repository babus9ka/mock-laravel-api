<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\OrganizationController;

Route::prefix('v1')->group(function () {
    // Получения всех организаций
    Route::get('organizations', [OrganizationController::class, 'index']);
    // Получения организаций по building_id
    Route::get('organizations/building/{building_id}', [OrganizationController::class, 'getOrganizationsByBuilding']);
    // Получения организаций по activity_id
    Route::get('organizations/activity/{activity_id}', [OrganizationController::class, 'getOrganizationsByActivity']);
    // Получение организаци в радиусе
    Route::get('organizations/nearby', [OrganizationController::class, 'getOrganizationsNearby']);
    // Получение организаци по айди
    Route::get('organizations/{id}', [OrganizationController::class, 'show']);
    // Получение организаци по активности
    Route::get('organizations/search-by-activity/{activity}', [OrganizationController::class, 'searchByActivity']);
    // Поиск организации по названию
    Route::get('organizations/search-by-name/{name}', [OrganizationController::class, 'searchByName']);

});