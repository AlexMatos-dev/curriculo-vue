<?php

namespace App\Http\Controllers;

use App\Models\CompanySocialNetwork;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CompanySocialNetworkController extends Controller
{
    public function showByCompanyId(int $companyId)
    {
        try
        {
            $socialNetwork = CompanySocialNetwork::query()
                ->where('company_id', '=', $companyId)
                ->with('company');

            $results = $socialNetwork->paginate(10);

            return response()->json($results);
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json(["message" => "Social network not found.", "Error" => $e], 404);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $request->validate([
                'social_network_profile'    => 'required',
                'company_id'                => 'required',
                'social_network_type'       => 'required',
            ]);

            $socialNetwork = CompanySocialNetwork::create($request->all());

            return response()->json($socialNetwork, 201);
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json(["message" => "An error occurred while creating the social network, please try again later.", "Error" => $e], 400);
        }
    }

    public function update(int $socialNetworkId, Request $request)
    {
        try
        {
            $network = CompanySocialNetwork::findOrFail($socialNetworkId);
            $network->update($request->all());

            return response()->json(["message" => "Social network updated successfully.", "data" => $network], 200);
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json(["message" => "Social network not found.", "Error" => $e], 404);
        }
    }

    public function destroy(int $socialNetworkId)
    {
        try
        {
            $socialNetwork = CompanySocialNetwork::findOrFail($socialNetworkId);
            $socialNetwork->delete();

            return response()->json(["message" => "Social network deleted sucessfully."], 200);
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json(["message" => "Social network not found.", "Error" => $e], 404);
        }
    }
}
