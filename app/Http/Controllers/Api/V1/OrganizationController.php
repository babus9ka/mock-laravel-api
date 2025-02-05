<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Building;
use App\Models\Activity;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Organization API",
 *     description="Документация API для получения организаций",
 *     @OA\Contact(
 *         email="shelton.arsen@gmail.com"
 *     )
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="ApiKeyAuth",
 *     type="apiKey",
 *     in="header",
 *     name="X-API-KEY",
 *     description="API ключ для доступа к API"
 * )
 */



class OrganizationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/organizations",
     *     summary="Получить все организации",
     *     tags={"Организации"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Organization"))
     *     )
     * )
     */

    public function index()
    {
        return response()->json(Organization::with(['building', 'phones', 'activities'])->get());
    }

    /**
     * @OA\Get(
     *     path="/api/v1/organizations/building/{building_id}",
     *     summary="Получить организации по зданию",
     *     tags={"Организации"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(
     *         name="building_id",
     *         in="path",
     *         description="ID здания",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций в указанном здании",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Organization"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Здание не найдено"
     *     )
     * )
     */
    public function getOrganizationsByBuilding($building_id)
    {
        $organizations = Organization::where('building_id', $building_id)
            ->with(['building', 'phones', 'activities'])
            ->get();

        if ($organizations->isEmpty()) {
            return response()->json(['message' => 'Организации в этом здании не найдены'], 404);
        }

        return response()->json($organizations);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/organizations/activity/{activity_id}",
     *     summary="Получить организации по виду деятельности",
     *     tags={"Организации"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(
     *         name="activity_id",
     *         in="path",
     *         description="ID вида деятельности",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций, относящихся к указанному виду деятельности",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Organization"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Организации по этому виду деятельности не найдены"
     *     )
     * )
     */
    public function getOrganizationsByActivity($activity_id)
    {
        $organizations = Organization::whereHas('activities', function ($query) use ($activity_id) {
            $query->where('activity_id', $activity_id);
        })
        ->with(['building', 'phones', 'activities'])
        ->get();

        if ($organizations->isEmpty()) {
            return response()->json(['message' => 'Организации не найдены по этому виду деятельности'], 404);
        }
        
        return response()->json($organizations);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/organizations/nearby",
     *     summary="Получить организации в заданном радиусе от точки",
     *     tags={"Организации"},
     *     security={{"ApiKeyAuth":{}}},
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         description="Широта точки",
     *         required=true,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         description="Долгота точки",
     *         required=true,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Радиус в километрах",
     *         required=true,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций в заданном радиусе",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Organization"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Недостаточно данных для вычислений"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Организации не найдены в этом радиусе"
     *     )
     * )
     */

    public function getOrganizationsNearby(Request $request)
    {
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius;

        // Проверка, что все необходимые параметры переданы
        if (!$latitude || !$longitude || !$radius) {
            return response()->json(['message' => 'Недостаточно данных для вычислений'], 400);
        }

        // Используем явное добавление в SQL запрос
        $buildings = Building::select('id', 'address', 'latitude', 'longitude')
            ->selectRaw(
                "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
                [$latitude, $longitude, $latitude]
            )
            ->having('distance', '<', $radius)  // Радиус в километрах
            ->orderBy('distance')
            ->get();

        if ($buildings->isEmpty()) {
            return response()->json(['message' => 'Организации не найдены в этом радиусе'], 404);
        }

        // Получаем организации для найденных зданий
        $organizations = Organization::whereIn('building_id', $buildings->pluck('id'))->with(['building', 'phones', 'activities'])->get();

        return response()->json($organizations);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/organizations/{id}",
     *     summary="Получить информацию об организации по ID",
     *     tags={"Организации"},
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Уникальный идентификатор организации",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация об организации",
     *         @OA\JsonContent(ref="#/components/schemas/Organization")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Некорректный запрос"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Организация не найдена"
     *     )
     * )
     */
    public function show($id)
    {
        $organization = Organization::with(['building', 'phones', 'activities'])->find($id);

        if (!$organization) {
            return response()->json(['message' => 'Организация не найдена'], 404);
        }

        return response()->json($organization);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/organizations/search-by-activity/{activity}",
     *     summary="Поиск организаций по виду деятельности",
     *     tags={"Организации"},
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Parameter(
     *         name="activity",
     *         in="path",
     *         required=true,
     *         description="Название вида деятельности",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций по виду деятельности",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Organization"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Некорректный запрос"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Вид деятельности не найден"
     *     )
     * )
     */
    public function searchByActivity($activity)
    {
        // Найдем родительскую активность
        $parentActivity = Activity::where('name', $activity)->first();

        if (!$parentActivity) {
            return response()->json(['message' => 'Вид деятельности не найден'], 404);
        }
        
        // Получим все дочерние виды деятельности этого родителя
        $activities = $parentActivity->children()->pluck('id')->toArray();
        
       
        // Добавим ID родительской активности в поиск, чтобы включить и сам родительский вид деятельности
        $activities[] = $parentActivity->id;
        
        // Найдем организации, относящиеся к этим видам деятельности
        $organizations = Organization::whereHas('activities', function($query) use ($activities) {
            $query->whereIn('activity_id', $activities);
        })->with(['building', 'phones', 'activities'])->get();

        return response()->json($organizations);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/organizations/search-by-name/{name}",
     *     summary="Поиск организаций по названию",
     *     tags={"Организации"},
     *     security={{"ApiKeyAuth": {}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="path",
     *         required=true,
     *         description="Название организации",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций по названию",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Organization"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Некорректный запрос"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Организации не найдены"
     *     )
     * )
     */
    public function searchByName($name)
    {
        // Найдем все организации, название которых содержит подстроку из $name
        $organizations = Organization::where('name', 'like', '%' . $name . '%')
                                    ->with(['building', 'phones', 'activities'])
                                    ->get();

        // Проверим, есть ли результаты
        if ($organizations->isEmpty()) {
            return response()->json(['message' => 'Организации не найдены'], 404);
        }

        return response()->json($organizations);
    }
}

