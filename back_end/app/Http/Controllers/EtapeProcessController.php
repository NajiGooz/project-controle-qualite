<?php

namespace App\Http\Controllers;

use App\Models\EtapeProcess;
use App\Models\ProcessFabrication;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class EtapeProcessController extends Controller
{
    public function index()
    {
        $etapesProcess = EtapeProcess::all();
        if (is_null($etapesProcess)) {
            return response()->json(['status' => 404, 'message' => 'les Etapes de Process not found']);
        }
        return response()->json($etapesProcess);
    }

    public function showEtapeProcess($id)
    {
        $etapeProcess = EtapeProcess::find($id);
        if (is_null($etapeProcess)) {
            return response()->json(['status' => 404, 'message' => 'l\'Etape de Process not found']);
        }
        return response()->json($etapeProcess);
    }

    public function storeEtapeProcess(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'codeEtape' => 'required|string|unique:etape_processes',
                'libelleEtape' => 'required|string|unique:etape_processes',
            ]);
            EtapeProcess::create($request->all());
            return response()->json(['message' => 'EtapeProcess created successfully']);
        } catch (Exception $e) {
            // Handle all other exceptions
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function updateEtapeProcess(Request $request, $id)
    {
        try {
            $request->validate([
                'codeEtape' => 'required|string|unique:etape_processes,codeEtape,' . $id,
                'libelleEtape' => 'required|string|unique:etape_processes,libelleEtape,' . $id,
            ]);
            $etapeProcess = EtapeProcess::find($id);
            if (is_null($etapeProcess)) {
                return response()->json(['status' => 404, 'message' => 'l\'Etape de Process not found']);
            }
            $etapeProcess->update($request->all());
            return response()->json(['message' => 'EtapeProcess updated successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteEtapeProcess($id)
    {
        try {
            $etapeProcess = EtapeProcess::find($id);
            if (is_null($etapeProcess)) {
                return response()->json(['status' => 404, 'message' => 'l\'Etape de Process not found']);
            }
            $processFabrication = ProcessFabrication::where(['codeEtape' => $etapeProcess->codeEtape])->first();
            if ($processFabrication) {
                return response()->json(['status' => 400, 'message' => 'Cette Etape de Process est liée à un Process de fabrication et ne peut pas être supprimée'], 400);  // you can customize the error message as per your requirement. 400 status code indicates bad request. 404 status code indicates not found. 500 status code indicates server error.
            }
            $etapeProcess->delete();
            return response()->json(['message' => 'EtapeProcess deleted successfully']);
        } catch (QueryException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

