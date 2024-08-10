<?php

namespace App\Http\Controllers;

use App\Models\UniteMesure;
use Exception;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class UniteMesureController extends Controller
{
    public function index()
    {
        $unitesMesure = UniteMesure::all();
        if (is_null($unitesMesure)) {
            return response()->json(['status' => 404, 'message' => 'Unités de Mesure not found']);
        }
        ;
        return response()->json($unitesMesure);
    }

    public function showUniteMesure($id)
    {
        $uniteMesure = UniteMesure::find($id);
        if (is_null($uniteMesure)) {
            return response()->json(['status' => 404, 'message' => 'Unité de Mesure not found']);
        }
        ;
        return response()->json($uniteMesure);
    }
    public function storeUniteMesure(Request $request)
    {
        try {
            $request->validate([
                'codeUnite' => 'required|string|unique:unite_mesures',
                'libelleUnite' => 'required|string|unique:unite_mesures',
            ]);
            $uniteMesure = UniteMesure::create($request->all());
            return response()->json(['message' => 'Unite Mesure creer avec success', $uniteMesure]);
        } catch (QueryException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function updateUniteMesure(Request $request, $id)
    {
        try {
            $request->validate([
                'codeUnite' => 'required|string|unique:unite_mesures',
                'libelleUnite' => 'required|string|unique:unite_mesures',
            ]);
            $uniteMesure = UniteMesure::find($id);
            if (is_null($uniteMesure)) {
                return response()->json(['status' => 404, 'message' => 'Unité de Mesure not found']);
            }
            $uniteMesure->update($request->all());
            return response()->json(['message' => 'Unite de mesure modifier avec sucess', $uniteMesure]);
        } catch (QueryException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteUniteMesure($id)
    {
        $uniteMesure = UniteMesure::find($id);
        if (is_null($uniteMesure)) {
            return response()->json(['status' => 404, 'message' => 'Unité de Mesure not found']);
        }
        $uniteMesure->delete();
        return response()->json(['message' => 'Unité de Mesure supprimer avec success']);
    }
}
