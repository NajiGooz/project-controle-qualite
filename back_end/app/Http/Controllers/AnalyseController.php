<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Models\ParametreAnalyse;
use Illuminate\Http\Request;
use Exception;

class AnalyseController extends Controller
{
    public function index()
    {
        $analyses = Analyse::all();
        if (is_null($analyses)) {
            return response()->json(['status' => 404, 'message' => 'Analyses not found'], 404);
        }
        return response()->json($analyses);
    }

    public function showAnalyse($id)
    {
        $analyse = Analyse::find($id);
        if (is_null($analyse)) {
            return response()->json(['status' => 404, 'message' => 'Analyse not found'], 404);
        }
        return response()->json($analyse);
    }

    public function storeAnalyse(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
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
                'codeAnalyse' => 'required|string|unique:analyses,codeAnalyse,' . $id,
                'libelleAnalyse' => 'required|string|unique:analyses,libelleAnalyse,' . $id,
            ]);
            $analyse = Analyse::find($id);
            if (is_null($analyse)) {
                return response()->json(['status' => 404, 'message' => 'Analyse not found'], 404);
            }
            $analyse->update($request->all());
            return response()->json(['message' => 'Analyse updated successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteAnalyse($id)
    {
        try {
            $analyse = Analyse::find($id);
            if (is_null($analyse)) {
                return response()->json(['message' => 'Analyse not found'], 404);
            }
            $parametreAnalyse = ParametreAnalyse::where(['codeAnalyse' => $analyse->codeAnalyse])->first();
            if ($parametreAnalyse) {
                return response()->json(['status' => 400, 'message' => 'Cette Analyse est liée à une Analyse de Process ou un Parametre d\'analyse et ne peut pas être supprimée.'], 400);
            } else {
                $analyse->delete();
                return response()->json(['message' => 'Analyse deleted successfully']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }
}
