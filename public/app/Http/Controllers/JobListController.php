<?php

namespace App\Http\Controllers;

use App\Models\JobList;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobListController extends Controller
{
    public function index(Request $request)
    {
        $query = JobList::query();

        if ($request->has("job_model"))
        {
            $query->where("job_model", $request->job_model);
        }

        if ($request->has("job_country"))
        {
            $query->where("job_country", $request->job_country);
        }

        if ($request->has("job_city"))
        {
            $query->where("job_city", $request->job_city);
        }

        if ($request->has("job_seniority"))
        {
            $query->where("job_seniority", "LIKE", "%" . $request->job_seniority . "%");
        }

        if ($request->has("job_salary_start"))
        {
            $query->where("job_salary", ">=", $request->job_salary_start);
        }

        if ($request->has("job_salary_end"))
        {
            $query->where("job_salary", "<=", $request->job_salary_end);
        }

        if ($request->has("job_skills"))
        {
            $query->where("job_skills", "LIKE", "%" . $request->job_skills . "%");
        }

        if ($request->has("job_english_level"))
        {
            $query->where("job_english_level", "LIKE", "%" . $request->job_english_level . "%");
        }

        if ($request->has("job_experience"))
        {
            $query->where("job_experience", "LIKE", "%" . $request->job_experience . "%");
        }

        $query->orderBy('created_at', 'desc');

        $results = $query->paginate(100);

        return response()->json($results);
    }

    public function show(int $joblistId)
    {
        try
        {
            $jobList = JobList::findOrFail($joblistId);

            return response()->json(["message" => "Vacant job found successfully.", "data" => $jobList], 200);
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json(["message" => "Vaga not found.", "Error" => $e], 404);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                "company_id"        => "required|integer",
                "job_model"         => "required|min:5|max:300",
                "job_country"       => "required|integer",
                "job_city"          => "required|integer",
                "job_seniority"     => "required|min:5|max:100",
                "job_description"   => "required|min:10|max:500",
                "job_english_level" => "nullable|max:100",
                "job_experience"    => "nullable|max:100",
            ]);

            if ($validator->fails())
            {
                return response()->json(["message" => "Validation failed", "Errors" => $validator->errors()], 400);
            }

            JobList::create($request->all());

            return response()->json(["message" => "Vacant job created successfully."], 201);
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json(["message" => "An error occurred while creating the job, please try again later.", "Error" => $e], 400);
        }
    }

    public function update(int $jobListId, Request $request)
    {
        try
        {
            $jobList = JobList::findOrFail($jobListId);
            $jobList->update($request->all());

            return response()->json(["message" => "Vacant job $jobList->job_model updated successfully.", "data => $jobList"], 200);
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json(["message" => "Vacant job not found.", "Error" => $e], 404);
        }
    }

    public function destroy(int $jobListId)
    {
        try
        {
            $jobList = JobList::findOrFail($jobListId);
            $jobList->delete();

            return response()->json(["message" => "Vacant job $jobList->job_model deleted sucessfully."], 200);
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json(["message" => "Vacant job not found.", "Error" => $e], 404);
        }
    }
}
