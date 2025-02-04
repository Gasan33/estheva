<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\MedicalReportResource;
use App\Models\Doctor;
use App\Models\MedicalReports;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Http\Request;

class MedicalReportsController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $reports = MedicalReports::with('patient', 'doctor', 'treatment')->get();
        return response()->json($reports);
    }

    public function userMedicalReports(Request $request)
    {
        $reports = MedicalReports::with('patient', 'doctor', 'treatment')
            ->where('patient_id', $request->patient_id)
            ->get();
        return response()->json($reports);
    }

    // Show the form for creating a new resource.
    public function create()
    {
        // You could return a view with form data if you're using Blade views
        $patients = User::where('role', 'patient')->pluck('id', 'name');
        $doctors = Doctor::pluck('id', 'name');
        $treatments = Treatment::pluck('id', 'name');

        return response()->json([
            'patients' => $patients,
            'doctors' => $doctors,
            'treatments' => $treatments,
        ]);
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:doctors,id',
            'treatment_id' => 'required|exists:treatments,id',
            'report_date' => 'required|date',
            'report_details' => 'required|string',
            'attachments' => 'nullable|array',
        ]);

        $report = MedicalReports::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'treatment_id' => $request->treatment_id,
            'report_date' => $request->report_date,
            'report_details' => $request->report_details,
            'attachments' => json_encode($request->attachments),
        ]);

        return response()->json($report, 201);
    }

    // Display the specified resource.
    public function show($id)
    {
        $report = MedicalReports::with('patient', 'doctor', 'treatment')->findOrFail($id);
        return response()->json($report);
    }

    // Show the form for editing the specified resource.
    public function edit($id)
    {
        // Similar to `create`, but you would likely pass the existing report's data
        $report = MedicalReports::findOrFail($id);
        $patients = User::where('role', 'patient')->pluck('id', 'name');
        $doctors = Doctor::pluck('id', 'name');
        $treatments = Treatment::pluck('id', 'name');

        return response()->json([
            'report' => $report,
            'patients' => $patients,
            'doctors' => $doctors,
            'treatments' => $treatments,
        ]);
    }

    // Update the specified resource in storage.
    public function update(Request $request, $id)
    {
        $request->validate([
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:doctors,id',
            'treatment_id' => 'required|exists:treatments,id',
            'report_date' => 'required|date',
            'report_details' => 'required|string',
            'attachments' => 'nullable|array',
        ]);

        $report = MedicalReports::findOrFail($id);
        $report->update([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'treatment_id' => $request->treatment_id,
            'report_date' => $request->report_date,
            'report_details' => $request->report_details,
            'attachments' => json_encode($request->attachments),
        ]);

        return response()->json($report);
    }

    // Remove the specified resource from storage.
    public function destroy($id)
    {
        $report = MedicalReports::findOrFail($id);
        $report->delete();

        return response()->json(['message' => 'Medical report deleted successfully']);
    }
}