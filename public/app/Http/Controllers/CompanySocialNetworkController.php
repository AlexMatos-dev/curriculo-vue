<?php

namespace App\Http\Controllers;

use App\Helpers\Validator;
use App\Models\CompanySocialNetwork;
use App\Models\CompanySocialNetworkType;
use Illuminate\Http\Request;

class CompanySocialNetworkController extends Controller
{
    public function showByCompanyId(int $companyId)
    {
        try
        {
            $socialNetwork = CompanySocialNetwork::query()
                ->where('company_id', '=', $companyId)
                ->with(['company', 'socialNetworkType']);

            $results = $socialNetwork->paginate(10);

            return response()->json($results);
        }
        catch (\Exception $e)
        {
            return response()->json(["message" => translate('social network not found'), "error" => $e], 400);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $request->validate([
                'social_network_profile' => 'required',
                'social_network_type_id' => 'required',
            ]);
            Validator::checkExistanceOnTable([
                'socialNetworkType' => ['object' => CompanySocialNetworkType::class, 'data' => $request->social_network_type_id]
            ]);
            $data = $request->all();
            $data['company_id'] = $this->getCompanyBySession()->company_id;
            $socialNetwork = CompanySocialNetwork::create($data);
            return response()->json($socialNetwork, 200);
        }
        catch (\Exception $e)
        {
            return response()->json(["message" => translate('an error occurred while creating the social network, please try again later'), "error" => $e->getMessage()], 400);
        }
    }

    public function update(int $socialNetworkId, Request $request)
    {
        try
        {
            $request->validate([
                'social_network_profile' => 'required',
                'social_network_type_id' => 'required',
            ]);
            $network = CompanySocialNetwork::findOrFail($socialNetworkId);
            $network->update($request->all());

            return response()->json(["message" => translate('social network updated successfully'), "data" => $network], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(["message" => translate('social network not found'), "Error" => $e], 400);
        }
    }

    public function destroy(int $socialNetworkId)
    {
        try
        {
            $socialNetwork = CompanySocialNetwork::findOrFail($socialNetworkId);
            $socialNetwork->delete();

            return response()->json(["message" => translate('social network deleted sucessfully')], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(["message" => translate('social network not found'), "Error" => $e], 400);
        }
    }
}
