<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use Illuminate\Http\Request;
use Exception;

class AnalyseController extends Controller
{
    public function index()
    {
        $analyses = Analyse::all();
        if (is_null($analyses)) {
            return response()->json(['status' => 404, 'message' => 'Analyses not found']);
        }
        return response()->json($analyses);
    }

    public function showAnalyse($id)
    {
        $analyse = Analyse::find($id);
        if (is_null($analyse)) {
            return response()->json(['status' => 404, 'message' => 'Analyse not found']);
        }
        return response()->json($analyse);
    }

    public function storeAnalyse(Request $request)
    {
        try {
            $request->validate([
                'codeAnalyse' => 'required|string|unique:analyses',
                'libelleAnalyse' => 'required|string|unique:analyses',
            ]);
            Analyse::create($request->all());
            return response()->json(['message' => 'Analyse created successfully']);
        } catch (Exception $e) {
            // Handle all other exceptions
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function updateAnalyse(Request $request, $id)
    {
        try {
            $request->validate([
                'codeAnalyse' => 'required|string|unique:analyses',
                'libelleAnalyse' => 'required|string|unique:analyses',
            ]);
            $analyse = Analyse::find($id);
            if (is_null($analyse)) {
                return response()->json(['status' => 404, 'message' => 'Analyse not found']);
            }
            $analyse->update($request->all());
            return response()->json(['message' => 'Analyse updated successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function deleteAnalyse($id)
    {
        $analyse = Analyse::find($id);
        if (is_null($analyse)) {
            return response()->json(['status' => 404, 'message' => 'Analyse not found']);
        }
        $analyse->delete();
        return response()->json(['message' => 'Analyse deleted successfully']);
    }
}
