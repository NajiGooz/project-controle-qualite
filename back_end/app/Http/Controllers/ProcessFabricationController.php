<?php

namespace App\Http\Controllers;

use App\Models\AnalyseProcess;
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
            $processFabrications = ProcessFabrication::with('planControle', 'etapeProcess')->orderBy('orderEtape')->get();

            if (is_null($processFabrications)) {
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

            // Create the new process fabrication record
            $processFabrication = ProcessFabrication::create($request->all());

            return response()->json(['message' => 'Process de fabrication créé avec succès', 'processFabrication' => $processFabrication]);
        } catch (QueryException $e) {
            Log::error('QueryException: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        } catch (Exception $e) {
            Log::error('Exception: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
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

            $countProcess = ProcessFabrication::where('codePlan', $codePlan)->count();
            if ($request->orderEtape > $countProcess) {
                return response()->json(['status' => 'error', 'message' => "L'ordre de l'étape dépasse le nombre total d'étapes disponibles. Veuillez vérifier l'ordre des étapes et réessayer."], 404);
            }
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
                // Swap the orderEtape between the two processes
                $tempOrderEtape = $existingProcess->orderEtape;
                $existingProcess->orderEtape = $processFabrication->orderEtape;
                $existingProcess->save();

                $processFabrication->orderEtape = $tempOrderEtape;
            }

            // Update the process fabrication record with new data
            $processFabrication->orderEtape = $request->orderEtape;
            $processFabrication->codeEtape = $request->codeEtape;
            $processFabrication->codePlan = $request->codePlan;
            $processFabrication->save();
            $allProcess = ProcessFabrication::with(['planControle', 'etapeProcess'])->where('codePlan', $codePlan)->orderBy('orderEtape')->get();
            return response()->json(['message' => 'Process de fabrication a été modifié avec succès', 'allProcess' => $allProcess]);
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
            $analyseProcess = AnalyseProcess::where(['codePlan' => $codePlan, 'codeEtape' => $codeEtape])->first();
            if ($analyseProcess) {
                return response()->json(['status' => 500, 'message' => 'Cette process de fabrication a des analyse et ne peut pas être supprimée.'], 500);
            }
            $processFabrications = ProcessFabrication::where('orderEtape', '>', $processFabrication->orderEtape)
                ->where('codePlan', $codePlan) // Assuming you want to limit to the same plan
                ->get();
            foreach ($processFabrications as $process) {
                $process->orderEtape = $process->orderEtape - 1;
                $process->save();
            }
            $processFabrication->delete();
            $allProcess = ProcessFabrication::with(['planControle', 'etapeProcess'])->where('codePlan', $codePlan)->orderBy('orderEtape')->get();
            return response()->json(['message' => 'process de fabrication est suprimmer avec success', 'allProcess' => $allProcess]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Process de fabrication non trouvé'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


}
