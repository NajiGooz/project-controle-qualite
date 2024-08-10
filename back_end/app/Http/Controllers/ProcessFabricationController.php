<?php

namespace App\Http\Controllers;

use App\Models\ProcessFabrication;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProcessFabricationController extends Controller
{

    public function index()
    {
        try {
            $processFabrications = ProcessFabrication::with('planControle', 'etapeProcess')->get();

            if ($processFabrications->isEmpty()) {
                return response()->json(['status' => 204, 'message' => 'No process de fabrication found']);
            }

            return response()->json($processFabrications);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => 'An internal server error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function storeProcessFabrication(Request $request)
    {
        try {
            $request->validate([
                'orderEtape' => 'required|integer',
                'codeEtape' => 'required|string|exists:etape_processes,codeEtape',
                'codePlan' => 'required|string|exists:plan_controles,codePlan'
            ]);
            $existingProcess = ProcessFabrication::where([
                'orderEtape' => $request->orderEtape,
                'codePlan' => $request->codePlan,
            ])->first();
            if ($existingProcess) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Process de fabrication exists deja avec order d\'Etape et plan d controle.'
                ], 409);
            }
            $processFabrication = ProcessFabrication::create($request->all());

            return response()->json(['message' => 'Process de Fabrication créé avec succès', 'processFabrication' => $processFabrication]);
        } catch (QueryException $e) {
            Log::error('QueryException: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Erreur lors de la création du process de fabrication', 'details' => $e->getMessage()], 500);
        } catch (Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Une erreur interne est survenue', 'details' => $e->getMessage()], 500);
        }
    }

    public function showProcessFabrication($codePlan, $codeEtape)
    {
        try {
            $processFabrication = ProcessFabrication::with(['planControle', 'etapeProcess'])
                ->where('codePlan', $codePlan)
                ->where('codeEtape', $codeEtape)
                ->firstOrFail();

            return response()->json($processFabrication);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Process de fabrication not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateProcessFabrication(Request $request, $codePlan, $codeEtape)
    {
        try {
            // Validate the request
            $request->validate([
                'orderEtape' => 'required|integer',
                'codeEtape' => 'required|string|exists:etape_processes,codeEtape',
                'codePlan' => 'required|string|exists:plan_controles,codePlan'
            ]);

            // Find the existing process fabrication record
            $processFabrication = ProcessFabrication::where('codePlan', $codePlan)
                ->where('codeEtape', $codeEtape)
                ->firstOrFail();

            // Check if another record with the same orderEtape and codePlan exists
            $existingProcess = ProcessFabrication::where('orderEtape', $request->orderEtape)
                ->where('codePlan', $request->codePlan)
                ->where('codeEtape', '!=', $codeEtape) // Exclude the current record
                ->first();

            if ($existingProcess) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Process de fabrication existe déjà avec cet ordre d\'étape et plan de contrôle.'
                ], 409);
            }

            // Debugging information before update
            Log::info('Updating processFabrication with data: ', [
                'orderEtape' => $request->orderEtape,
                'codeEtape' => $request->codeEtape,
                'codePlan' => $request->codePlan,
            ]);

            // Perform the update
            DB::table('process_fabrications')
                ->where('codePlan', $codePlan)
                ->where('codeEtape', $codeEtape)
                ->update([
                    'orderEtape' => $request->orderEtape,
                    'codeEtape' => $request->codeEtape,
                    'codePlan' => $request->codePlan
                ]);

            // Fetch the updated process fabrication record
            $updatedProcessFabrication = ProcessFabrication::where('codePlan', $request->codePlan)
                ->where('codeEtape', $request->codeEtape)
                ->first();

            return response()->json(['message' => 'Process de fabrication a été modifié avec succès', 'processFabrication' => $updatedProcessFabrication]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Process de fabrication non trouvé'], 404);
        } catch (QueryException $e) {
            Log::error('QueryException: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Erreur lors de la modification du process de fabrication', 'details' => $e->getMessage()], 500);
        } catch (Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }




    public function deleteProcessFabrication($codePlan, $codeEtape)
    {
        try {
            $processFabrication = ProcessFabrication::where(['codePlan' => $codePlan, 'codeEtape' => $codeEtape])->firstOrFail();
            $processFabrication->delete();
            return response()->json(['message' => 'process de fabrication est suprimmer avec success']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Process de fabrication non trouvé'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }


}
