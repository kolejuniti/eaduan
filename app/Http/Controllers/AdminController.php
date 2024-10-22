<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function user()
    {
        $users = DB::table('users')->where('type',2)->orderBy('name')->get();

        return view('admin.user', compact('users'));
    }

    public function addUser(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        // Insert user into the database
        $user = User::create([
            'name' => strtoupper($validatedData['name']),
            'email' => $validatedData['email'],
            'password' => Hash::make('12345678'),  // Consider using random password generation or user-defined
            'type' => ('2'),  // Assuming type 2 is predefined as "pic"
            'status' => ('1'),  // Assuming status 1 is predefined as "active"
        ]);

        // Redirect with success message
        return redirect()->back()->with('success', 'Pegawai baru berjaya ditambah ke dalam sistem.');
    }

    public function updateUser(Request $request, $id)
    {
        // Validate the request (optional but recommended)
        $request->validate([
            'status' => 'required|boolean', // Ensure it's a boolean (0 or 1)
        ]);
       
        DB::table('users')
        ->where('users.id', '=', $id)
        ->update(['status' => $request->input('status')]);
        
        return redirect()->back()->with('success', 'Status pegawai berjaya dikemaskini.');
    }

    public function damageComplaintLists(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date') ?? $start_date;

        // If start_date is null, set it to today's date
        if (is_null($start_date)) {
            // Set default to 3 days ago, start of the day
            $start_date = now()->subDays(3)->startOfDay()->format('Y-m-d'); 
            
            // Set end date to the current time (end of today)
            $end_date = now()->endOfDay()->format('Y-m-d');
        }

        $complaintLists = DB::query()
        ->fromSub(function ($query) use ($start_date, $end_date) { // Pass the dates into the closure
            $query->from(DB::table('damage_complaints')
                ->join('eduhub.students', 'damage_complaints.ic', '=', 'eduhub.students.ic')
                ->join('damage_types', 'damage_complaints.damage_type_id', '=', 'damage_types.id')
                ->leftJoin(DB::raw('(SELECT damage_complaint_logs.damage_complaint_id, status.name AS latest_status, status.id AS latest_status_id 
                                    FROM damage_complaint_logs
                                    JOIN status ON damage_complaint_logs.status_id = status.id
                                    WHERE damage_complaint_logs.id = 
                                    (SELECT MAX(id) FROM damage_complaint_logs AS logs WHERE logs.damage_complaint_id = damage_complaint_logs.damage_complaint_id)
                                    ) AS latest_log'), 'damage_complaints.id', '=', 'latest_log.damage_complaint_id')
                ->where('eduhub.students.status', '2')
                ->whereBetween(DB::raw("CAST(damage_complaints.date_of_complaint AS DATE)"), [$start_date, $end_date]) // Filter by date_of_complaint
                ->select(
                    'damage_complaints.id',
                    'eduhub.students.name AS complainant_name',
                    'damage_complaints.phone_number',
                    'damage_types.name AS damage_type',
                    'damage_complaints.block',
                    'damage_complaints.no_unit',
                    'damage_complaints.created_at',
                    DB::raw("DATE_FORMAT(damage_complaints.date_of_complaint, '%d-%m-%Y') as date_of_complaint"),
                    DB::raw("DATE_FORMAT(damage_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                    DB::raw("DATE_FORMAT(damage_complaints.date_of_completion, '%d-%m-%Y') as date_of_completion"),
                    DB::raw("DATEDIFF(NOW(), damage_complaints.date_of_complaint) as days_since_complaint"),
                    'latest_log.latest_status', 
                    'latest_log.latest_status_id'
                )
                ->union(
                    DB::table('damage_complaints')
                        ->join('eduhub.users', 'damage_complaints.ic', '=', 'eduhub.users.ic')
                        ->join('damage_types', 'damage_complaints.damage_type_id', '=', 'damage_types.id')
                        ->leftJoin(DB::raw('(SELECT damage_complaint_logs.damage_complaint_id, status.name AS latest_status, status.id AS latest_status_id  
                                            FROM damage_complaint_logs
                                            JOIN status ON damage_complaint_logs.status_id = status.id
                                            WHERE damage_complaint_logs.id = 
                                            (SELECT MAX(id) FROM damage_complaint_logs AS logs WHERE logs.damage_complaint_id = damage_complaint_logs.damage_complaint_id)
                                            ) AS latest_log'), 'damage_complaints.id', '=', 'latest_log.damage_complaint_id')
                        ->whereBetween(DB::raw("CAST(damage_complaints.date_of_complaint AS DATE)"), [$start_date, $end_date]) // Filter by date_of_complaint
                        ->select(
                            'damage_complaints.id',
                            'eduhub.users.name AS complainant_name',
                            'damage_complaints.phone_number',
                            'damage_types.name AS damage_type',
                            'damage_complaints.block',
                            'damage_complaints.no_unit',
                            'damage_complaints.created_at',
                            DB::raw("DATE_FORMAT(damage_complaints.date_of_complaint, '%d-%m-%Y') as date_of_complaint"),
                            DB::raw("DATE_FORMAT(damage_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                            DB::raw("DATE_FORMAT(damage_complaints.date_of_completion, '%d-%m-%Y') as date_of_completion"),
                            DB::raw("DATEDIFF(NOW(), damage_complaints.date_of_complaint) as days_since_complaint"),
                            'latest_log.latest_status', 
                            'latest_log.latest_status_id'
                        )
                ));
        }, 'combined_complaint_lists')
        ->orderBy('combined_complaint_lists.created_at', 'DESC')
        ->get();

        return view('admin.damagecomplaint', compact('complaintLists', 'start_date', 'end_date'));
    }

    public function damageComplaintDetails(Request $request)
    {
        $id = $request->input('id');

        $complaintLists = DB::query()
            ->fromSub(function ($query) {
                $query->from(DB::table('damage_complaints')
                    ->join('eduhub.students', 'damage_complaints.ic', '=', 'eduhub.students.ic')
                    ->join('damage_types', 'damage_complaints.damage_type_id', '=', 'damage_types.id')
                    ->join('damage_type_details', 'damage_complaints.damage_type_detail_id', '=', 'damage_type_details.id')
                    ->leftjoin('technician', 'damage_complaints.technician_id', '=', 'technician.id')
                    ->where('eduhub.students.status', '2')
                    ->select(
                        'damage_complaints.id AS id',
                        'eduhub.students.name AS complainant_name',
                        'damage_complaints.phone_number',
                        'damage_types.name AS damage_type',
                        'damage_type_details.name AS damage_type_detail',
                        'damage_complaints.notes',
                        'damage_complaints.block',
                        'damage_complaints.no_unit',
                        'damage_complaints.created_at',
                        DB::raw("DATE_FORMAT(damage_complaints.date_of_complaint, '%d-%m-%Y') as date_of_complaint"),
                        DB::raw("DATE_FORMAT(damage_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                        DB::raw("DATE_FORMAT(damage_complaints.date_of_completion, '%d-%m-%Y') as date_of_completion"),
                        'damage_complaints.technician_id',
                        'technician.name AS technician'
                    )
                    ->union(
                        DB::table('damage_complaints')
                            ->join('eduhub.users', 'damage_complaints.ic', '=', 'eduhub.users.ic')
                            ->join('damage_types', 'damage_complaints.damage_type_id', '=', 'damage_types.id')
                            ->join('damage_type_details', 'damage_complaints.damage_type_detail_id', '=', 'damage_type_details.id')
                            ->leftjoin('technician', 'damage_complaints.technician_id', '=', 'technician.id')
                            ->select(
                            'damage_complaints.id AS id',
                            'eduhub.users.name AS complainant_name',
                            'damage_complaints.phone_number',
                            'damage_types.name AS damage_type',
                            'damage_type_details.name AS damage_type_detail',
                            'damage_complaints.notes',
                            'damage_complaints.block',
                            'damage_complaints.no_unit',
                            'damage_complaints.created_at',
                            DB::raw("DATE_FORMAT(damage_complaints.date_of_complaint, '%d-%m-%Y') as date_of_complaint"),
                            DB::raw("DATE_FORMAT(damage_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                            DB::raw("DATE_FORMAT(damage_complaints.date_of_completion, '%d-%m-%Y') as date_of_completion"),
                            'damage_complaints.technician_id',
                            'technician.name AS technician'
                            )
                    ));
            }, 'combined_damage_lists')
            ->where('combined_damage_lists.id', '=', $id)
            ->orderBy('combined_damage_lists.created_at', 'DESC') // Order by the field in the combined result
            ->get();

            // Now, we will fetch logs for each complaint
            $complaintLogs = [];

            foreach ($complaintLists as $complaint) {
                $logs = DB::table('damage_complaint_logs')
                    ->join('status', 'damage_complaint_logs.status_id', '=', 'status.id')
                    ->select(DB::raw("DATE_FORMAT(damage_complaint_logs.created_at, '%d-%m-%Y') as date_of_log"),
                    'status.name AS log_status', 'damage_complaint_logs.notes AS log_notes')
                    ->where('damage_complaint_logs.damage_complaint_id', '=', $complaint->id)
                    ->get();

                $complaintLogs[$complaint->id] = $logs; // Store logs with complaint ID as key
            }

            //fetch technician data
            $technicians = DB::table('technician')->get();

            //fetch technician data
            $status = DB::table('status')->get();

            if ($request->ajax()) {
                return response()->json(['complaintLists' => $complaintLists, 'complaintLogs' => $complaintLogs, 'technicians' => $technicians, 'status' => $status]);
            }

        return view('admin.damagecomplaint', compact('complaintLists', 'complaintLogs', 'technicians', 'status'));
    }

    public function generalComplaintLists()
    {   
        $complaintLists = DB::table('general_complaints')
            ->join('eduhub.students', 'general_complaints.ic', '=', 'eduhub.students.ic')
            ->join('sections', 'general_complaints.section_id', '=', 'sections.id')
            ->join('complaint_types', 'general_complaints.complaint_type_id', '=', 'complaint_types.id')
            ->join('status', 'general_complaints.status_id', '=', 'status.id')
            ->select(DB::raw("DATE_FORMAT(general_complaints.date_of_complaint, '%d-%m-%Y') as date_of_complaint"),
                'general_complaints.id',
                'general_complaints.category',
                'eduhub.students.name AS complainant_name',
                'general_complaints.phone_number AS phone_number',
                'sections.name AS section',
                'complaint_types.name AS complaint_type',
                'general_complaints.location AS location',
                DB::raw("DATE_FORMAT(general_complaints.date_of_receipt, '%d-%m-%Y') as date_of_receipt"),
                DB::raw("DATE_FORMAT(general_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                'status.name AS status',
                'general_complaints.status_id AS status_id',
                'general_complaints.created_at'
            )
            ->union(
                DB::table('general_complaints')
                    ->join('eduhub.users', 'general_complaints.ic', '=', 'eduhub.users.ic')
                    ->join('sections', 'general_complaints.section_id', '=', 'sections.id')
                    ->join('complaint_types', 'general_complaints.complaint_type_id', '=', 'complaint_types.id')
                    ->join('status', 'general_complaints.status_id', '=', 'status.id')
                    ->select(DB::raw("DATE_FORMAT(general_complaints.date_of_complaint, '%d-%m-%Y') as date_of_complaint"),
                        'general_complaints.id',
                        'general_complaints.category',
                        'eduhub.users.name AS complainant_name', // Change to 'users' table name here
                        'general_complaints.phone_number AS phone_number',
                        'sections.name AS section',
                        'complaint_types.name AS complaint_type',
                        'general_complaints.location AS location',
                        DB::raw("DATE_FORMAT(general_complaints.date_of_receipt, '%d-%m-%Y') as date_of_receipt"),
                        DB::raw("DATE_FORMAT(general_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                        'status.name AS status',
                        'general_complaints.status_id AS status_id',
                        'general_complaints.created_at'
                    )
            )
            ->orderBy('created_at', 'DESC') // Order by the field in the combined result
            ->get();

        return view('admin.generalcomplaint', compact('complaintLists'));
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
            ->select(DB::raw("DATE_FORMAT(general_complaints.date_of_complaint, '%d-%m-%Y') as date_of_complaint"),
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
                    ->select(DB::raw("DATE_FORMAT(general_complaints.date_of_complaint, '%d-%m-%Y') as date_of_complaint"),
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

        $users = DB::table('users')->where('type',2)->orderBy('name')->get();

        if ($request->ajax()) {
            return response()->json(['complaintLists' => $complaintLists, 'users' => $users]);
        }

        return view('admin.generalcomplaint', compact('complaintLists', 'users'));
    }

    public function complaintUpdate(Request $request, $id) 
    {
        $date_of_receipt = $request->input('date_of_receipt');
        $user = $request->input('user');

        DB::table('general_complaints')
                ->where('general_complaints.id', $id)
                ->update([
                    'date_of_receipt' => $date_of_receipt, 
                    'user_id' => $user,
                    'status_id' => 2
                ]);

        return redirect()->route('admin.generalcomplaint')->with('success', 'Aduan telah dikemaskini dan diserahkan kepada pegawai yang terlibat.');
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

        return redirect()->route('admin.generalcomplaint')->with('danger', 'Aduan telah dibatalkan.');
    }

    public function damageReport(Request $request)
    {
        $month = $request->input('month');

        // Get the date range based on the provided month or the last 7 days
        if (is_null($month)) {
            $dates = collect(range(7, 0))->map(function($i) {
                return Carbon::now()->subDays($i)->format('Y-m-d');
            });
        } else {
            $startOfMonth = Carbon::parse($month)->startOfMonth();
            $endOfMonth = Carbon::parse($month)->endOfMonth();
            $dates = collect();

            for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
                $dates->push($date->format('Y-m-d'));
            }
        }

        $firstDate = $dates->first();  // First date in the collection
        $lastDate = $dates->last();    // Last date in the collection

        // Fetch damage types and statuses
        $damageTypes = DB::table('damage_types')->get();
        $status = DB::table('status')->whereIn('id', [1, 2, 3])->get(['id', 'name']);

        // Retrieve the latest logs for each complaint based on the latest `damage_complaint_log` entry (using MAX id)
        $latestLogs = DB::table('damage_complaint_logs as dcl')
            ->join('damage_complaints as dc', 'dcl.damage_complaint_id', '=', 'dc.id')
            ->select('dc.damage_type_id', 'dcl.status_id', DB::raw('COUNT(*) as total'), DB::raw('DATE_FORMAT(dc.date_of_complaint, "%Y-%m-%d") as complaint_date'))
            ->whereIn('dcl.status_id', $status->pluck('id'))
            ->whereIn(DB::raw('DATE_FORMAT(dc.date_of_complaint, "%Y-%m-%d")'), $dates)
            ->whereIn(DB::raw('dcl.id'), function($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('damage_complaint_logs')
                    ->groupBy('damage_complaint_id');
            })
            ->groupBy('dc.damage_type_id', 'dcl.status_id', 'complaint_date')
            ->get();

        // Structure data for output
        $totalByDamageStatus = [];
        $totalByStatus = [];

        foreach ($damageTypes as $damageType) {
            foreach ($dates as $date) {
                foreach ($status as $stat) {
                    $totalCountDamageStatus = $latestLogs->where('damage_type_id', $damageType->id)
                        ->where('complaint_date', $date)
                        ->where('status_id', $stat->id)
                        ->first();

                    $totalByDamageStatus[$damageType->id][$date][$stat->id] = [
                        'total' => $totalCountDamageStatus->total ?? 0,
                        'status_name' => $stat->name
                    ];

                    $totalCountStatus = $latestLogs->where('complaint_date', $date)
                        ->where('status_id', $stat->id)
                        ->sum('total');

                    $totalByStatus[$date][$stat->id] = [
                        'total' => $totalCountStatus ?? 0,
                        'status_name' => $stat->name
                    ];
                }
            }
        }

        // Pass data to the view
        return view('admin.damagereport', compact('dates', 'status', 'damageTypes', 'totalByDamageStatus', 'totalByStatus', 'firstDate', 'lastDate'));
    }

    public function generalReport(Request $request)
    {
        $month = $request->input('month');

        // Check if $date is null
        if (is_null($month)) {
            // Get the current date and the last 7 days
            $dates = [];
            for ($i = 7; $i >= 0; $i--) {
                $dates[] = Carbon::now()->subDays($i)->format('Y-m-d');
            }
        } else {
            // Parse the provided month and get the first and last days of the month
            $startOfMonth = Carbon::parse($month)->startOfMonth();
            $endOfMonth = Carbon::parse($month)->endOfMonth();

            // Loop through the whole month and get each day
            $dates = [];
            for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
                $dates[] = $date->format('Y-m-d');
            }
        }

        // Fetch status ids only once
        $status = DB::table('status')->whereIn('id', [1, 2, 3])->get(['id', 'name']);

        // Initialize arrays for totals
        $totalByCategoryTypeStatus = [];
        $totalByStatus = [];

        $categoryTypes = ['Pertanyaan', 'Aduan', 'Cadangan'];

        foreach ($categoryTypes as $categoryType) {

            // Get the total number of complaints for the current damage type up to the last date
            $lastDate = end($dates); // Last date
            $firstDate = reset($dates); // First date (today)\

            // Count the total number of complaints
            $totals = DB::table('general_complaints')
                ->whereBetween('general_complaints.date_of_complaint', [$firstDate, $lastDate]) // Filter between the first and last date
                ->count(); 

            foreach ($dates as $date) {
                foreach ($status as $stat) {
                    
                    $totalCountCategoryTypeStatus = DB::table('general_complaints')
                        ->where('general_complaints.category', '=', $categoryType)
                        ->whereRaw('DATE_FORMAT(general_complaints.date_of_complaint, "%Y-%m-%d") = ?', [$date])
                        ->where('general_complaints.status_id', '=', $stat->id)
                        ->count();

                    $totalByCategoryTypeStatus[$categoryType][$date][$stat->id] = [
                        'total' => $totalCountCategoryTypeStatus
                    ];

                    $totalCountStatus = DB::table('general_complaints')
                        ->whereRaw('DATE_FORMAT(general_complaints.date_of_complaint, "%Y-%m-%d") = ?', [$date])
                        ->where('general_complaints.status_id', '=', $stat->id)
                        ->count();

                    $totalByStatus[$date][$stat->id] = [
                        'total' => $totalCountStatus
                    ];

                }
            }

        }

        // Initialize arrays for totals
        $totalByComplaintTypeStatus = [];
        $totalByComplaintStatus = [];

        $complaintTypes = DB::table('complaint_types')->get();

        foreach ($complaintTypes as $complaintType) {

            // Get the total number of complaints for the current damage type up to the last date
            $lastDate = end($dates); // Last date
            $firstDate = reset($dates); // First date (today)

            foreach ($dates as $date) {
                foreach ($status as $stat) {
                    
                    $totalCountComplaintTypeStatus = DB::table('general_complaints')
                        ->where('general_complaints.complaint_type_id', '=', $complaintType->id)
                        ->whereRaw('DATE_FORMAT(general_complaints.date_of_complaint, "%Y-%m-%d") = ?', [$date])
                        ->where('general_complaints.status_id', '=', $stat->id)
                        ->count();

                    $totalByComplaintTypeStatus[$complaintType->id][$date][$stat->id] = [
                        'total' => $totalCountComplaintTypeStatus
                    ];

                    $totalCountComplaintStatus = DB::table('general_complaints')
                        ->whereRaw('DATE_FORMAT(general_complaints.date_of_complaint, "%Y-%m-%d") = ?', [$date])
                        ->where('general_complaints.status_id', '=', $stat->id)
                        ->count();

                    $totalByComplaintStatus[$date][$stat->id] = [
                        'total' => $totalCountComplaintStatus
                    ];

                }
            }

        }

        // Pass data to the view
        return view('admin.generalreport', compact('dates', 'status', 'categoryTypes', 'totalByCategoryTypeStatus', 'totalByStatus', 'complaintTypes', 'totalByComplaintTypeStatus', 'totalByComplaintStatus', 'firstDate', 'lastDate'));
    }
}
