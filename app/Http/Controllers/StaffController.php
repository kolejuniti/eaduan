<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function dashboard()
    {
        return view('staff.dashboard');
    }

    public function showDamageForm()
    {
        //fetch damage types and details
        $damagetypes = DB::table('damage_types')->get();
        
        //fetch locations
        $locations = DB::table('location_details')
            ->select('group_name', 'name')
            ->orderBy('id')
            ->get()
            ->groupBy('group_name');

        // Get the logged-in staff
        $staff = Auth::guard('staff')->user();
        $staff_ic = $staff->ic;

        return view('staff.damageform', compact('staff', 'damagetypes', 'locations'));
    }

    public function getDamageTypeDetails($damagetypeId)
    {
        $damagetypedetails = DB::table('damage_type_details')->where('damage_type_id', $damagetypeId)->get();
        return response()->json($damagetypedetails);
    }

    public function submitDamageForm(Request $request)
    {  
        // Get the logged-in staff
        $staff = Auth::guard('staff')->user();
        $staff_ic = $staff->ic;

        $date_of_complaint = $request->input('date_of_complaint');
        $phone = $request->input('phone');
        $block = $request->input('block');
        $no_unit = $request->input('no_unit');
        $damage_type = $request->input('damagetypes');
        $damage_type_detail = $request->input('damagetypedetails');
        $notes = $request->input('notes');

        $complaintId = DB::table('damage_complaints')->insertGetId([
            'ic'=>$staff_ic,
            'type_of_user'=>1,
            'date_of_complaint'=>$date_of_complaint,
            'phone_number'=>$phone,
            'block'=>$block,
            'no_unit'=>$no_unit,
            'location'=>"UNITI VILLAGE",
            'damage_type_id'=>$damage_type,
            'damage_type_detail_id'=>$damage_type_detail,
            'notes'=>ucfirst($notes),
        ]);

        // Insert into damage_complaint_logs using the complaint ID
        DB::table('damage_complaint_logs')->insert([
            'damage_complaint_id' => $complaintId,
            'notes' => 'Terima aduan daripada staf',
            'status_id' => 1
        ]);

        // Redirect to the desired route with a success message
        return redirect()->back()->with('success', [
            'Aduan kerosakan anda telah berjaya dihantar.',
            'Aduan kerosakan anda akan diselesaikan dalam tempoh masa 7 hari berkerja.'
        ]);
    }

    public function showGeneralForm()
    {   
        //fetch section
        // $sections = DB::table('sections')->get();
        //fetch general complaints
        $complaintTypes = DB::table('complaint_types')->get();

        // Get the logged-in staff
        $staff = Auth::guard('staff')->user();
        $staff_ic = $staff->ic;

        return view('staff.generalform', compact('staff', 'complaintTypes'));
    }

    public function submitGeneralForm(Request $request)
    {  
        // Get the logged-in staff
        $staff = Auth::guard('staff')->user();
        $staff_ic = $staff->ic;

        $date_of_complaint = $request->input('date_of_complaint');
        $phone = $request->input('phone');
        $category = $request->input('category');
        // $section = $request->input('section');
        $complaint_type = $request->input('complaintType');
        $location = $request->input('location');
        $notes = $request->input('notes');

        DB::table('general_complaints')->insert([
            'ic'=>$staff_ic,
            'type_of_user'=>1,
            'date_of_complaint'=>$date_of_complaint,
            'phone_number'=>$phone,
            'category'=>$category,
            // 'section_id'=>$section,
            'complaint_type_id'=>$complaint_type,
            'location'=>strtoupper($location),
            'notes'=>ucfirst($notes),
            'status_id'=>1,
        ]);

        // Redirect to the desired route with a success message
        return redirect()->back()->with('success', [
            'Aduan umum anda telah berjaya dihantar.',
            'Aduan umum anda akan diselesaikan dalam tempoh masa 7 hari berkerja.'
        ]);
    }

    public function damageComplaintList()
    {
        // Get the logged-in staff
        $staff = Auth::guard('staff')->user();
        $staff_ic = $staff->ic;

        $damageLists = DB::table('damage_complaints')
            ->join('damage_types', 'damage_complaints.damage_type_id', '=', 'damage_types.id')
            ->join('damage_type_details', 'damage_complaints.damage_type_detail_id', '=', 'damage_type_details.id')
            ->leftJoin(DB::raw('(SELECT damage_complaint_logs.damage_complaint_id, status.name AS latest_status
                                FROM damage_complaint_logs
                                JOIN status ON damage_complaint_logs.status_id = status.id
                                WHERE damage_complaint_logs.id = 
                                (SELECT MAX(id) FROM damage_complaint_logs AS logs WHERE logs.damage_complaint_id = damage_complaint_logs.damage_complaint_id)
                                ) AS latest_log'), 'damage_complaints.id', '=', 'latest_log.damage_complaint_id')
            ->where('damage_complaints.ic', '=', $staff_ic) // Using exact match for staff IC
            ->select(
                'damage_complaints.id AS id',
                DB::raw("DATE_FORMAT(damage_complaints.created_at, '%d-%m-%Y %H:%i:%s') as date_of_complaint"),
                DB::raw("DATE_FORMAT(damage_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                DB::raw("DATE_FORMAT(damage_complaints.date_of_completion, '%d-%m-%Y') as date_of_completion"),
                'damage_types.name AS damage_types',
                'damage_type_details.name AS damage_type_details',
                'latest_log.latest_status AS status' // Latest status from the subquery
            )
            ->get();

        return view('staff.damagereport', compact('damageLists'));
    }

    public function damageComplaintDetails(Request $request)
    {
        $id = $request->input('id');

        // Fetch the single complaint using first()
        $complaintLists = DB::table('damage_complaints')
            ->join('damage_types', 'damage_complaints.damage_type_id', '=', 'damage_types.id')
            ->join('damage_type_details', 'damage_complaints.damage_type_detail_id', '=', 'damage_type_details.id')
            ->where('damage_complaints.id', '=', $id)
            ->select(
                'damage_complaints.id AS id',
                DB::raw("DATE_FORMAT(damage_complaints.created_at, '%d-%m-%Y %H:%i:%s') as date_of_complaint"),
                'damage_complaints.block',
                'damage_complaints.no_unit',
                'damage_types.name AS damage_type',
                'damage_type_details.name AS damage_type_detail',
                'damage_complaints.notes',
                DB::raw("DATE_FORMAT(damage_complaints.date_of_action, '%d-%m-%Y') as date_of_action")
            )
            ->first();  // Use first() to fetch a single record

        // Initialize an empty array for logs
        $complaintLogs = [];

        // Fetch logs for the single complaint if it exists
        if ($complaintLists) {
            $logs = DB::table('damage_complaint_logs')
                ->join('status', 'damage_complaint_logs.status_id', '=', 'status.id')
                ->select(
                    DB::raw("DATE_FORMAT(damage_complaint_logs.created_at, '%d-%m-%Y') as date_of_log"),
                    'status.name AS log_status', 
                    'damage_complaint_logs.notes AS log_notes'
                )
                ->where('damage_complaint_logs.damage_complaint_id', '=', $complaintLists->id)
                ->get();

            // Store the logs in the array
            $complaintLogs[$complaintLists->id] = $logs;
        }

        // Return JSON response for AJAX
        if ($request->ajax()) {
            return response()->json(['complaintLists' => $complaintLists, 'complaintLogs' => $complaintLogs]);
        }

        // If not an AJAX request, return the view
        return view('staff.damagereport', compact('complaintLists', 'complaintLogs'));
    } 

    public function generalComplaintList()
    {
        // Get the logged-in staff
        $staff = Auth::guard('staff')->user();
        $staff_ic = $staff->ic;

        $generalLists = DB::table('general_complaints')
            ->leftjoin('sections', 'general_complaints.section_id', '=', 'sections.id')
            ->join('complaint_types', 'general_complaints.complaint_type_id', '=', 'complaint_types.id')
            ->join('status', 'general_complaints.status_id', '=', 'status.id')
            ->where('general_complaints.ic', 'LIKE', "{$staff_ic}")
            ->select(
                DB::raw("DATE_FORMAT(general_complaints.created_at, '%d-%m-%Y %H:%i:%s') as date_of_complaint"),
                'sections.name AS section', 'complaint_types.name AS complaint_types',
                DB::raw("DATE_FORMAT(general_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                DB::raw("DATE_FORMAT(general_complaints.date_of_receipt, '%d-%m-%Y') as date_of_receipt"),
                'general_complaints.action_notes AS action_notes', 'status.name AS status'
                )
            ->get();

        return view('staff.generalreport', compact('generalLists'));
    }
}
