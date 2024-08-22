<?php

namespace App\Http\Controllers;

use App\Models\PlanControle;
use App\Models\ProcessFabrication;
use App\Models\Produit;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class PlanControleController extends Controller
{
    public function index()
    {
        $planControles = PlanControle::with('produit')->get();
        if (is_null($planControles)) {
            return response()->json(['status' => 404, 'message' => 'les plans de Controle n\'existe pas']);
        }
        return response()->json($planControles);
    }
    public function storePlanControle(Request $request)
    {
        try {
            $request->validate([
                'codePlan' => 'required|string|unique:plan_controles',
                'etatPlan' => 'required|string',
                'codeProduit' => 'required|string|exists:produits,codeProduit',
            ]);

            $existPlanControle = PlanControle::where(['codeProduit' => $request->codeProduit])->first();
            if ($existPlanControle) {
                return response()->json([
                    'message' => 'Un plan de contrôle existe déjà pour ce produit'
                ], 409);
            }
            PlanControle::create($request->all());
            return response()->json(['message' => 'Plan de contrôle créé avec succès']);
        } catch (QueryException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    public function showPlanControle($id)
    {
        try {
            $planControle = PlanControle::with('produit')->where(['codePlan' => $id])->first();
            return response()->json($planControle);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'PlanControle not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function updatePlanControle(Request $request, $id)
    {
        try {

            $request->validate([
                'codePlan' => 'required|string|unique:plan_controles,codeplan,' . $id,
                'etatPlan' => 'required|string',
                'codeProduit' => 'required|string|exists:produits,codeProduit',
            ]);
            $planControle = PlanControle::findOrFail($id);
            // $existPlanControle = PlanControle::where(['codeProduit' => $request->codeProduit])->first();
            // if ($existPlanControle) {
            //     return response()->json([
            //         'message' => 'Un plan de contrôle existe déjà pour ce produit'
            //     ], 409);
            // }
            $planControle->update($request->all());
            return response()->json(['message' => 'plan controle a ete modifier', 'planControle' => $planControle]);
        } catch (QueryException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function deletePlanControle($id)
    {
        try {
            $planControle = PlanControle::findOrFail($id);
            $ProcessFabrication = ProcessFabrication::where(['codePlan' => $planControle->codePlan])->first();
            if ($ProcessFabrication) {
                return response()->json(['status' => 500, 'message' => 'Cette Plan de Controle a un process de fabrication et ne peut pas être supprimée.'], 500);
            }
            if (is_null($planControle)) {
                return response()->json(['status' => 404, 'message' => 'PlanControle not found'], 404);
            }
            $planControle->delete();
            return response()->json(['message' => 'PlanControle deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'ParametreAnalyse not found'], 404);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }
}

