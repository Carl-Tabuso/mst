<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobOrderCorrectionRequest;
use App\Models\JobOrderCorrection;
use Illuminate\Http\Request;

class JobOrderCorrectionController extends Controller
{
    public function store(StoreJobOrderCorrectionRequest $request)
    {
        dd($request->all());

        JobOrderCorrection::create($request->validated());

        return back()->with(['message' => __('responses.correction')]);
    }

    public function update(Request $request, JobOrderCorrection $correction)
    {
        //
    }
}
