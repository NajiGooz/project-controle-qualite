<?php

namespace App\Http\Controllers;

use App\Models\ParametreAnalyse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ParametreAnalyseController extends Controller
{
    public function index()
    {
        try {
            $parametreAnalyses = ParametreAnalyse::with('analyse', 'uniteMesure')->get();
            if ($parametreAnalyses->isEmpty()) {
                return response()->json(['status' => 204, 'message' => 'parametre analyse not found']);
            }
            return response()->json($parametreAnalyses);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function storeParametreAnalyse(Request $request)
    {
        try {
            $request->validate([
                'codeParam' => 'required|string|unique:parametre_analyses',
                'libelleParam' => 'required|string|unique:parametre_analyses',
                'codeAnalyse' => 'required|string|exists:analyses,codeAnalyse',
                'codeUnite' => 'required|string|exists:unite_mesures,codeUnite',
            ]);
            $existParametreanalyse = ParametreAnalyse::where([
                'codeAnalyse' => $request->codeAnalyse,
                'codeUnite' => $request->codeUnite
            ])->first();
            if ($existParametreanalyse) {
                return response()->json([
                    'error' => 'Parametre d\'analyse déjà existant pour cette analyse et cette unité de mesure'
                ], 409);
            }
            $parametreAnalyse = ParametreAnalyse::create($request->all());
            return response()->json([
                'message' => 'parametre analyse creer avec success',
                $parametreAnalyse
            ]);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'error' => $e->getMessage()]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }
    }

    public function showParametreAnalyse($id)
    {
        try {
            $parametreAnalyse = ParametreAnalyse::with('analyse', 'uniteMesure')->findOrFail($id);
            return response()->json($parametreAnalyse);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'ParametreAnalyse not found'], 404);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }

    public function updateParametreAnalyse(Request $request, $id)
    {
        try {
            $request->validate([
                'codeParam' => 'required|string',
                'libelleParam' => 'required|string',
                'codeAnalyse' => 'required|string|exists:analyses,codeAnalyse',
                'codeUnite' => 'required|string|exists:unite_mesures,codeUnite',
            ]);
            $parametreAnalyse = ParametreAnalyse::findOrFail($id);
            if (is_null($parametreAnalyse)) {
                return response()->json(['error' => 'ParametreAnalyse not found'], 404);
            }
            $existParametreanalyse = ParametreAnalyse::where([
                'codeAnalyse' => $request->codeAnalyse,
                'codeUnite' => $request->codeUnite
            ])->where('id', '!=', $id)->first();

            if ($existParametreanalyse) {
                return response()->json([
                    'error' => 'Parametre d\'analyse déjà existant pour cette analyse et cette unité de mesure'
                ], 409);
            }

            $parametreAnalyse->update($request->all());
            return response()->json(['message' => 'ParametreAnalyse updated successfully', $parametreAnalyse]);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'error' => $e->getMessage()]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }
    }

    public function deleteParametreAnalyse($id)
    {
        try {
            $parametreAnalyse = ParametreAnalyse::findOrFail($id);
            $parametreAnalyse->delete();
            return response()->json(['message' => 'ParametreAnalyse deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'ParametreAnalyse not found'], 404);
        } catch (QueryException $e) {
            return response()->json(['status' => 404, 'error' => $e->getMessage()]);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'error' => $e->getMessage()]);
        }
    }
}
