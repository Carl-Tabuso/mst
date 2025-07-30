<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSafetyInspectionRequest;
use App\Models\Form3HaulingChecklist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class SafetyInspectionController extends Controller
{
    public function update(UpdateSafetyInspectionRequest $request, Form3HaulingChecklist $checklist): RedirectResponse
    {
        $validated = $request->safe();

        if ($request->isFullyAccomplished) {
            DB::transaction(function () use ($checklist) {
                $checklist->checkAllFields();
                $checklist->setHaulingToInProgress();
            });
        } else {
            $checklist->update([
                'is_vehicle_inspection_filled' => $validated->boolean('is_vehicle_inspection_filled'),
                'is_uniform_ppe_filled'        => $validated->boolean('is_uniform_ppe_filled'),
                'is_tools_equipment_filled'    => $validated->boolean('is_tools_equipment_filled'),
                'is_certify'                   => $validated->boolean('is_certify'),
            ]);
        }

        [$title, $description] = explode('|', __('responses.checklist', [
            'date' => $checklist->form3Hauling->date,
        ]));

        return back()->with(['message' => compact('title', 'description')]);
    }
}
