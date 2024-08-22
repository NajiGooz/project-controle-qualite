<?php

namespace App\Http\Controllers;

use App\Models\ParametreAnalyseProcess;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParametreAnalyseProcessController extends Controller
{
    public function index()
    {
        try {
            $paramAnalysesProcess = ParametreAnalyseProcess::with('analyseProcess', 'parametreAnalyse')->orderBy('codeAP')->get();
            if (is_null($paramAnalysesProcess)) {
                return response()->json(['status' => 204, 'message' => 'parametre analyse not found']);
            }
            return response()->json($paramAnalysesProcess);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function showParamAnalyseProcess($codePAP)
    {
        try {
            $paramAnalyseProcess = ParametreAnalyseProcess::with('analyseProcess', 'parametreAnalyse')->findOrFail($codePAP);
            return response()->json($paramAnalyseProcess);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Parametre Analyse Process not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeParamAnalyseProcess(Request $request)
    {
        try {
            $parametresAP = $request->all();
            // Loop through each parameter passed in the request
            foreach ($parametresAP as $parametreAP) {
                // Validate each parameter's data
                $validator = Validator::make($parametreAP, [
                    'codePAP' => 'required|string|unique:parametre_analyse_processes',
                    'valeurMin' => 'required|numeric',
                    'valeurMax' => 'required|numeric',
                    'codeParam' => 'required|string|exists:parametre_analyses,codeParam',
                    'codeAP' => 'required|string|exists:analyse_processes,codeAP',
                ]);

                if ($parametreAP['valeurMax'] <= $parametreAP['valeurMin']) {
                    return response()->json(['message' => "La valeur maximale doit être supérieure à la valeur minimale."], 500);
                }

                // If validation fails, return error response
                if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()], 422);
                }

                // Check if the ParametreAnalyseProcess already exists
                $existParamAnalyseProcess = ParametreAnalyseProcess::where([
                    'codeParam' => $parametreAP['codeParam'],
                    'codeAP' => $parametreAP['codeAP'],
                ])->first();

                if ($existParamAnalyseProcess) {
                    return response()->json([
                        'error' => 'Parametre analyse Process already exists for this analyse Process and this parametre d\'analyse'
                    ], 409);
                }

                // Create the ParametreAnalyseProcess entry in the database
            }
            foreach ($parametresAP as $parametreAP) {
                ParametreAnalyseProcess::create($parametreAP);
            }

            return response()->json([
                'message' => 'Parametres analyse Process created successfully',
            ]);

        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }


    public function updateParamAnalyseProcess(Request $request, $codePAP)
    {
        try {
            $request->validate([
                'codePAP' => 'required|string|unique:parametre_analyse_processes,codePAP,' . $codePAP . ',codePAP',
                'valeurMin' => 'required|numeric',
                'valeurMax' => 'required|numeric',
                'codeParam' => 'required|string|exists:parametre_analyses,codeParam',
                'codeAP' => 'required|string|exists:analyse_processes,codeAP',
            ]);
            $paramAnalyseProcess = ParametreAnalyseProcess::findOrFail($codePAP);
            $paramAnalyseProcess->update($request->all());
            return response()->json([
                'message' => 'Parametre analyse Process updated successfully',
                $paramAnalyseProcess
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parametre Analyse Process not found'], 404);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteParamAnalyseProcess($codePAP)
    {
        try {
            $paramAnalyseProcess = ParametreAnalyseProcess::findOrFail($codePAP);
            $paramAnalyseProcess->delete();
            return response()->json(['message' => 'Parametre Analyse Process deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Parametre Analyse Process not found'], 404);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }
}
