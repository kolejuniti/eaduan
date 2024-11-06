<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PICController extends Controller
{
    public function dashboard()
    {
        return view('pic.dashboard');
    }

    public function generalComplaintLists()
    {   
        $pic = Auth::User();

        $complaintLists = DB::table('general_complaints')
            ->join('eduhub.students', 'general_complaints.ic', '=', 'eduhub.students.ic')
            ->join('sections', 'general_complaints.section_id', '=', 'sections.id')
            ->join('complaint_types', 'general_complaints.complaint_type_id', '=', 'complaint_types.id')
            ->join('status', 'general_complaints.status_id', '=', 'status.id')
            ->select(DB::raw("DATE_FORMAT(general_complaints.created_at, '%d-%m-%Y %H:%i:%s') as date_of_complaint"),
                'general_complaints.id',
                'general_complaints.category',
                'eduhub.students.name AS complainant_name',
                'general_complaints.phone_number AS phone_number',
                'sections.name AS section',
                'complaint_types.name AS complaint_type',
                DB::raw("DATE_FORMAT(general_complaints.date_of_receipt, '%d-%m-%Y') as date_of_receipt"),
                DB::raw("DATE_FORMAT(general_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                'status.name AS status',
                'general_complaints.status_id AS status_id',
                'general_complaints.created_at',
                'general_complaints.user_id'
            )->where('general_complaints.section_id', $pic->section_id)
            ->union(
                DB::table('general_complaints')
                    ->join('eduhub.users', 'general_complaints.ic', '=', 'eduhub.users.ic')
                    ->join('sections', 'general_complaints.section_id', '=', 'sections.id')
                    ->join('complaint_types', 'general_complaints.complaint_type_id', '=', 'complaint_types.id')
                    ->join('status', 'general_complaints.status_id', '=', 'status.id')
                    ->select(DB::raw("DATE_FORMAT(general_complaints.created_at, '%d-%m-%Y %H:%i:%s') as date_of_complaint"),
                        'general_complaints.id',
                        'general_complaints.category',
                        'eduhub.users.name AS complainant_name', // Change to 'users' table name here
                        'general_complaints.phone_number AS phone_number',
                        'sections.name AS section',
                        'complaint_types.name AS complaint_type',
                        DB::raw("DATE_FORMAT(general_complaints.date_of_receipt, '%d-%m-%Y') as date_of_receipt"),
                        DB::raw("DATE_FORMAT(general_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                        'status.name AS status',
                        'general_complaints.status_id AS status_id',
                        'general_complaints.created_at',
                        'general_complaints.user_id'
                    )->where('general_complaints.section_id', $pic->section_id)
            )
            ->orderBy('created_at', 'DESC') // Order by the field in the combined result
            ->get();

        return view('pic.generalcomplaint', compact('complaintLists'));
    }

    public function generalComplaintDetails(Request $request)
    {   
        $id = $request->input('id'); // ID from the request

        $complaintLists = DB::table('general_complaints')
            ->join('eduhub.students', 'general_complaints.ic', '=', 'eduhub.students.ic')
            ->join('sections', 'general_complaints.section_id', '=', 'sections.id')
            ->join('complaint_types', 'general_complaints.complaint_type_id', '=', 'complaint_types.id')
            ->join('status', 'general_complaints.status_id', '=', 'status.id')
            ->leftjoin('eaduan.users', 'general_complaints.user_id', '=', 'eaduan.users.id')
            ->select(DB::raw("DATE_FORMAT(general_complaints.created_at, '%d-%m-%Y %H:%i:%s') as date_of_complaint"),
                'general_complaints.id AS id',
                'eduhub.students.name AS complainant_name',
                'general_complaints.phone_number AS phone_number',
                'sections.name AS section',
                'complaint_types.name AS complaint_type',
                'general_complaints.notes AS notes',
                'status.name AS status',
                DB::raw("DATE_FORMAT(general_complaints.date_of_receipt, '%d-%m-%Y') as date_of_receipt"),
                DB::raw("DATE_FORMAT(general_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                'general_complaints.action_notes AS action_notes',
                'general_complaints.user_id AS user_id',
                'general_complaints.status_id AS status_id',
                'general_complaints.cancel_notes AS cancel_notes',
                'eaduan.users.name AS user_name'
            )
            ->where('general_complaints.id', '=', $id) // Filter by the complaint ID
            ->union(
                DB::table('general_complaints')
                    ->join('eduhub.users', 'general_complaints.ic', '=', 'eduhub.users.ic')
                    ->join('sections', 'general_complaints.section_id', '=', 'sections.id')
                    ->join('complaint_types', 'general_complaints.complaint_type_id', '=', 'complaint_types.id')
                    ->join('status', 'general_complaints.status_id', '=', 'status.id')
                    ->leftjoin('eaduan.users', 'general_complaints.user_id', '=', 'eaduan.users.id')
                    ->select(DB::raw("DATE_FORMAT(general_complaints.created_at, '%d-%m-%Y %H:%i:%s') as date_of_complaint"),
                        'general_complaints.id AS id',
                        'eduhub.users.name AS complainant_name', // From 'users' table
                        'general_complaints.phone_number AS phone_number',
                        'sections.name AS section',
                        'complaint_types.name AS complaint_type',
                        'general_complaints.notes AS notes',
                        'status.name AS status',
                        DB::raw("DATE_FORMAT(general_complaints.date_of_receipt, '%d-%m-%Y') as date_of_receipt"),
                        DB::raw("DATE_FORMAT(general_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                        'general_complaints.action_notes AS action_notes',
                        'general_complaints.user_id AS user_id',
                        'general_complaints.status_id AS status_id',
                        'general_complaints.cancel_notes AS cancel_notes',
                        'eaduan.users.name AS user_name'
                    )
                    ->where('general_complaints.id', '=', $id) // Filter by the complaint ID
            )
            ->get(); // Return a single record

        //fetch status data
        $status = DB::table('status')->whereIn('id', [2,3])->get();

        if ($request->ajax()) {
            return response()->json(['complaintLists' => $complaintLists, 'status' => $status]);
        }

        return view('pic.generalcomplaint', compact('complaintLists', 'status'));
    }

    public function complaintUpdate(Request $request, $id) 
    {
        $pic = Auth::User();

        $date_of_action = $request->input('date_of_action');
        $action_notes = $request->input('action_notes');
        $status = $request->input('status');

        // Get the existing date_of_action from the database
        $existingComplaint = DB::table('general_complaints')
                           ->select('date_of_action')
                           ->where('id', $id)
                           ->first();

        // Prepare the data to update
        $updateData = [
            'user_id' => $pic->id,
            'action_notes' => ucfirst($action_notes),
            'status_id' => $status,
        ];

        // Only update date_of_action if it's currently null
        if (is_null($existingComplaint->date_of_action)) {
            $updateData['date_of_action'] = $date_of_action;
        }

        // Perform the update
        DB::table('general_complaints')
            ->where('id', $id)
            ->update($updateData);

        return redirect()->route('pic.generalcomplaint')->with('success', 'Aduan telah dikemaskini.');
    }

    public function complaintCancel(Request $request, $id) 
    {
        $request->validate([
            'cancel_notes' => 'required|string'
        ]);

        $cancel_notes = $request->input('cancel_notes'); // Get the notes from the request

        DB::table('general_complaints')
            ->where('general_complaints.id', $id)
            ->update([
                'cancel_notes' => ucfirst($cancel_notes),
                'status_id' => 4
            ]);

        return redirect()->route('pic.generalcomplaint')->with('danger', 'Aduan telah dibatalkan.');
    }
}
