<?php

namespace App\Http\Controllers;

use App\Models\ParametreAnalyseProcess;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ParametreAnalyseProcessController extends Controller
{
    public function index()
    {
        try {
            $paramAnalysesProcess = ParametreAnalyseProcess::with('analyseProcess', 'parametreAnalyse')->get();
            if ($paramAnalysesProcess->isEmpty()) {
                return response()->json(['status' => 204, 'message' => 'Parametre analyses Process not found']);
            }
            return response()->json($paramAnalysesProcess);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function showParamAnalyseProcess($codePAP)
    {
        try {
            $paramAnalyseProcess = ParametreAnalyseProcess::with('analyseProcess', 'parametreAnalyse')->findOrFail($codePAP);
            return response()->json($paramAnalyseProcess);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parametre Analyse Process not found'], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function storeParamAnalyseProcess(Request $request)
    {
        try {
            $request->validate([
                'codePAP' => 'required|string|unique:parametre_analyse_processes',
                'valeurMin' => 'required|numeric',
                'valeurMax' => 'required|numeric',
                'codeParam' => 'required|string|exists:parametre_analyses,codeParam',
                'codeAP' => 'required|string|exists:analyse_processes,codeAP',
            ]);

            $existParamAnalyseProcess = ParametreAnalyseProcess::where([
                'codeParam' => $request->codeParam,
                'codeAP' => $request->codeAP,
            ])->first();
            if ($existParamAnalyseProcess) {
                return response()->json([
                    'error' => 'Parametre analyse Process exists deja avec cette analyse Process et cette parametre d\'analyse'
                ], 409);
            }
            $paramAnalyseProcess = ParametreAnalyseProcess::create($request->all());
            return response()->json([
                'message' => 'Parametre analyse Process created successfully',
                $paramAnalyseProcess
            ]);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function updateParamAnalyseProcess(Request $request, $codePAP)
    {
        try {
            $request->validate([
                'codePAP' => 'required|string|unique:parametre_analyse_processes',
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
            return response()->json(['status' => 404, 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function deleteParamAnalyseProcess($codePAP)
    {
        try {
            $paramAnalyseProcess = ParametreAnalyseProcess::findOrFail($codePAP);
            $paramAnalyseProcess->delete();
            return response()->json(['message' => 'Parametre Analyse Process deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Parametre Analyse Process not found'], 404);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }
}
