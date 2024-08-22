<?php

use App\Http\Controllers\AnalyseController;
use App\Http\Controllers\AnalyseProcessController;
use App\Http\Controllers\EtapeProcessController;
use App\Http\Controllers\ParametreAnalyseController;
use App\Http\Controllers\ParametreAnalyseProcessController;
use App\Http\Controllers\PlanControleController;
use App\Http\Controllers\ProcessFabricationController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\UniteMesureController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


/*produit Routes*/
Route::get('/produits', [ProduitController::class, 'index']);
Route::get('/produit/{id}', [ProduitController::class, 'showProduit']);
Route::post('/produit', [ProduitController::class, 'storeProduit']);
Route::put('/produit/{id}', [ProduitController::class, 'updateProduit']);
Route::delete('/produit/{id}', [ProduitController::class, 'deleteProduit']);
/*produit Routes*/

/*planControle Routes*/
Route::post('planControle', [PlanControleController::class, 'storePlanControle']);
Route::get('planControle/{id}', [PlanControleController::class, 'showPlanControle']);
Route::get('plansControle', [PlanControleController::class, 'index']);
Route::put('planControle/{id}', [PlanControleController::class, 'updatePlanControle']);
Route::delete('planControle/{id}', [PlanControleController::class, 'deletePlanControle']);
/*planControle Routes*/

/*EtapePrcess Routes*/
Route::post('etapeProcess', [EtapeProcessController::class, 'storeEtapeProcess']);
Route::get('etapesProcess', [EtapeProcessController::class, 'index']);
Route::get('etapeProcess/{id}', [EtapeProcessController::class, 'showEtapeProcess']);
Route::put('etapeProcess/{id}', [EtapeProcessController::class, 'updateEtapeProcess']);
Route::delete('etapeProcess/{id}', [EtapeProcessController::class, 'deleteEtapeProcess']);
/*EtapePrcess Routes*/


/*processFabrication Routes*/
Route::post('processFabrication', [ProcessFabricationController::class, 'storeProcessFabrication']);
Route::get('processFabrications', [ProcessFabricationController::class, 'index']);
Route::get('processFabrication/{codePlan}/{codeEtape}', [ProcessFabricationController::class, 'showProcessFabrication']);
Route::put('processFabrication/{codePlan}/{codeEtape}', [ProcessFabricationController::class, 'updateProcessFabrication']);
Route::delete('processFabrication/{codePlan}/{codeEtape}', [ProcessFabricationController::class, 'deleteProcessFabrication']);
/*processFabrication Routes*/


/*Analuse Routes*/
Route::get('analyses', [AnalyseController::class, 'index']);
Route::post('analyse', [AnalyseController::class, 'storeAnalyse']);
Route::put('analyse/{id}', [AnalyseController::class, 'updateAnalyse']);
Route::get('analyse/{id}', [AnalyseController::class, 'showAnalyse']);
Route::delete('analyse/{id}', [AnalyseController::class, 'deleteAnalyse']);
/*Analuse Routes*/


/*Unite de Mesure Routes*/
Route::get('unites', [UniteMesureController::class, 'index']);
Route::post('unite', [UniteMesureController::class, 'storeUniteMesure']);
Route::put('unite/{id}', [UniteMesureController::class, 'updateUniteMesure']);
Route::get('unite/{id}', [UniteMesureController::class, 'showUniteMesure']);
Route::delete('unite/{id}', [UniteMesureController::class, 'deleteUniteMesure']);
/*Unite de Mesure Routes*/


/*Parametre Analyse Routes*/
Route::get('parametreAnalyses', [ParametreAnalyseController::class, 'index']);
Route::post('parametreAnalyse', [ParametreAnalyseController::class, 'storeParametreAnalyse']);
Route::get('parametreAnalyse/{id}', [ParametreAnalyseController::class, 'showParametreAnalyse']);
Route::put('parametreAnalyse/{id}', [ParametreAnalyseController::class, 'updateParametreAnalyse']);
Route::delete('parametreAnalyse/{id}', [ParametreAnalyseController::class, 'deleteParametreAnalyse']);
/*Parametre Analyse Routes*/


/*Analyse Process Analyse Routes*/
Route::get('analysesProcess', [AnalyseProcessController::class, 'index']);
Route::post('analyseProcess', [AnalyseProcessController::class, 'storeAnalyseProcess']);
Route::get('analyseProcess/{id}', [AnalyseProcessController::class, 'showAnalyseProcess']);
Route::put('analyseProcess/{id}', [AnalyseProcessController::class, 'updateAnalyseProcess']);
Route::delete('analyseProcess/{codeAP}', [AnalyseProcessController::class, 'deleteAnalyseProcess']);
/*Analyse Process Analyse Routes*/


Route::get('paramAnalysesProcess', [ParametreAnalyseProcessController::class, 'index']);
Route::post('paramAnalyseProcess', [ParametreAnalyseProcessController::class, 'storeParamAnalyseProcess']);
Route::get('paramAnalyseProcess/{id}', [ParametreAnalyseProcessController::class, 'showparamAnalyseProcess']);
Route::put('paramAnalyseProcess/{id}', [ParametreAnalyseProcessController::class, 'updateparamAnalyseProcess']);
Route::delete('paramAnalyseProcess/{id}', [ParametreAnalyseProcessController::class, 'deleteparamAnalyseProcess']);

