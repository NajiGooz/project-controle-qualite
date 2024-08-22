<?php

namespace App\Http\Controllers;

use App\Models\AnalyseProcess;
use App\Models\ProcessFabrication;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class AnalyseProcessController extends Controller
{
    public function index()
    {
        try {
            $analysesProcess = AnalyseProcess::with('analyse', 'processFabrication')->get();
            if ($analysesProcess->isEmpty()) {
                return response()->json(['message' => 'No AnalyseProcess found'], 404);
            }
            return response()->json($analysesProcess);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function showAnalyseProcess($id)
    {
        try {
            $analyseprocess = AnalyseProcess::with('analyse', 'processFabrication')->findOrFail($id);
            return response()->json($analyseprocess);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Analyse Process not found'], 404);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }
    public function storeAnalyseProcess(Request $request)
    {
        try {
            $request->validate([
                'codeAP' => 'required|string|unique:analyse_processes',
                'codeAnalyse' => 'required|string|exists:analyses,codeAnalyse',
                'codeEtape' => 'required|string|exists:process_fabrications,codeEtape',
                'codePlan' => 'required|string|exists:process_fabrications,codePlan',
            ]);

            $existAnalyseProcess = AnalyseProcess::where([
                'codeAnalyse' => $request->codeAnalyse,
                'codeEtape' => $request->codeEtape,
                'codePlan' => $request->codePlan
            ])->first();
            if ($existAnalyseProcess) {
                return response()->json(['message' => 'AnalyseProcess already exists'], 500);
            }
            $existProcessFabrication = ProcessFabrication::where([
                'codeEtape' => $request->codeEtape,
                'codePlan' => $request->codePlan
            ])->first();
            if (!$existProcessFabrication) {
                return response()->json(['message' => 'ProcessFabrication not found'], 404);
            }
            $AnalyseProcess = AnalyseProcess::create($request->all());
            return response()->json(['message' => 'AnalyseProcess created successfully', "analyseProcess" => $AnalyseProcess]);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateAnalyseProcess(Request $request, $id)
    {
        try {
            $request->validate([
                'codeAP' => 'required|string|unique:analyse_processes,codeAP,' . $id,
                'codeAnalyse' => 'required|string|exists:analyses,codeAnalyse',
                'codeEtape' => 'required|string|exists:process_fabrications,codeEtape',
                'codePlan' => 'required|string|exists:process_fabrications,codePlan',
            ]);
            $analyseProcess = AnalyseProcess::findOrFail($id);
            $existAnalyseProcess = AnalyseProcess::where([
                'codeAnalyse' => $request->codeAnalyse,
                'codeEtape' => $request->codeEtape,
                'codePlan' => $request->codePlan
            ])->first();
            if ($existAnalyseProcess) {
                return response()->json(['message' => 'AnalyseProcess already exists'], 500);
            }
            $existProcessFabrication = ProcessFabrication::where([
                'codeEtape' => $request->codeEtape,
                'codePlan' => $request->codePlan
            ])->first();
            if (!$existProcessFabrication) {
                return response()->json(['message' => 'ProcessFabrication not found']);
            }
            $analyseProcess->update($request->all());
            return response()->json(['message' => 'AnalyseProcess updated successfully', $analyseProcess]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'AnalyseProcess not found'], 404);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteAnalyseProcess($codeAP)
    {
        try {
            $analyseProcess = AnalyseProcess::where(['codeAP' => $codeAP])->first();
            $analyseProcess->delete();
            return response()->json(['message' => 'AnalyseProcess deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'AnalyseProcess not found'], 404);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }
}
