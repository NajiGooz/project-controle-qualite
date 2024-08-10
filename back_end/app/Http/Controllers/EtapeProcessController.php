<?php

namespace App\Http\Controllers;

use App\Models\EtapeProcess;
use Exception;
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
                'codeEtape' => 'required|string',
                'libelleEtape' => 'required|string',
            ]);
            $etapeProcess = EtapeProcess::find($id);
            if (is_null($etapeProcess)) {
                return response()->json(['status' => 404, 'message' => 'l\'Etape de Process not found']);
            }
            $etapeProcess->update($request->all());
            return response()->json(['message' => 'EtapeProcess updated successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function deleteEtapeProcess($id)
    {
        $etapeProcess = EtapeProcess::find($id);
        if (is_null($etapeProcess)) {
            return response()->json(['status' => 404, 'message' => 'l\'Etape de Process not found']);
        }
        $etapeProcess->delete();
        return response()->json(['message' => 'EtapeProcess deleted successfully']);
    }
}

