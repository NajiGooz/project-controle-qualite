<?php

namespace App\Http\Controllers;

use App\Models\PlanControle;
use App\Models\Produit;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index()
    {
        $produits = Produit::all();
        if (is_null($produits)) {
            return response()->json(['status' => 404, 'message' => 'Produits not found'], 404);
        }
        return response()->json($produits);
    }

    public function showProduit($id)
    {
        $produit = Produit::find($id);
        if (is_null($produit)) {
            return response()->json(['status' => 404, 'message' => 'Produit not found']);
        }
        return response()->json($produit);
    }

    public function storeProduit(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'codeProduit' => 'required|string|unique:produits',
                'libelleProduit' => 'required|string|unique:produits',
            ]);
            Produit::create($request->all());
            return response()->json(['message' => 'Produit created successfully']);
        } catch (Exception $e) {
            // Handle all other exceptions
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function updateProduit(Request $request, $id)
    {
        try {
            $request->validate([
                'codeProduit' => 'required|string|unique:produits,codeProduit,' . $id,
                'libelleProduit' => 'required|string|unique:produits,libelleProduit,' . $id,
            ]);
            $produit = Produit::find($id);
            if (is_null($produit)) {
                return response()->json(['status' => 404, 'message' => 'Produit not found']);
            }
            $produit->update($request->all());
            return response()->json($produit);
        } catch (QueryException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }


    public function deleteProduit($id)
    {
        $produit = Produit::find($id);
        if (is_null($produit)) {
            return response()->json(['status' => 404, 'message' => 'Produit not found'], 404);
        }
        $planControle = PlanControle::where(['codeProduit' => $produit->codeProduit])->first();
        if ($planControle) {
            return response()->json(['status' => 400, 'message' => 'Impossible de supprimer ce produit car il est utilisé dans un plan de contrôle'], 400);
        }
        $produit->delete();
        return response()->json(['message' => 'Produit deleted successfully'], 200);
    }
}
