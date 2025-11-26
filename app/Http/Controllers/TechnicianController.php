<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

class TechnicianController extends Controller
{
    public function dashboard()
    {
        $statusTypes = DB::table('status')->whereIn('id', [1, 2])->get(['id', 'name']);
        
        $complaintLists = [];

        // Pre-compute the latest log ID for each complaint using GROUP BY
        // This is much more efficient than correlated subqueries
        $latestLogIds = DB::table('damage_complaint_logs')
            ->select('damage_complaint_id', DB::raw('MAX(id) as max_log_id'))
            ->groupBy('damage_complaint_id');

        // Join with the actual log data to get the status
        $latestStatusSubquery = DB::table('damage_complaint_logs as dcl')
            ->joinSub($latestLogIds, 'latest', function($join) {
                $join->on('dcl.damage_complaint_id', '=', 'latest.damage_complaint_id')
                     ->on('dcl.id', '=', 'latest.max_log_id');
            })
            ->whereIn('dcl.status_id', [1, 2])
            ->select('dcl.damage_complaint_id', 'dcl.status_id as latest_status_id');

        foreach ($statusTypes as $statusType) {
            // Query for students
            $firstQuery = DB::table('damage_complaints')
                ->join('eduhub.students', 'damage_complaints.ic', '=', 'eduhub.students.ic')
                ->join('damage_types', 'damage_complaints.damage_type_id', '=', 'damage_types.id')
                ->joinSub($latestStatusSubquery, 'latest_log', function($join) use ($statusType) {
                    $join->on('damage_complaints.id', '=', 'latest_log.damage_complaint_id')
                         ->where('latest_log.latest_status_id', '=', $statusType->id);
                })
                ->where('eduhub.students.status', '2')
                ->select(
                    'damage_complaints.id',
                    'eduhub.students.name AS complainant_name',
                    'damage_complaints.phone_number',
                    'damage_types.name AS damage_type',
                    'damage_complaints.block',
                    'damage_complaints.no_unit',
                    'damage_complaints.created_at',
                    DB::raw("DATE_FORMAT(damage_complaints.created_at, '%d-%m-%Y %H:%i:%s') as date_of_complaint"),
                    'latest_log.latest_status_id'
                );

            // Query for users (staff)
            $secondQuery = DB::table('damage_complaints')
                ->join('eduhub.users', 'damage_complaints.ic', '=', 'eduhub.users.ic')
                ->join('damage_types', 'damage_complaints.damage_type_id', '=', 'damage_types.id')
                ->joinSub($latestStatusSubquery, 'latest_log', function($join) use ($statusType) {
                    $join->on('damage_complaints.id', '=', 'latest_log.damage_complaint_id')
                         ->where('latest_log.latest_status_id', '=', $statusType->id);
                })
                ->select(
                    'damage_complaints.id',
                    'eduhub.users.name AS complainant_name',
                    'damage_complaints.phone_number',
                    'damage_types.name AS damage_type',
                    'damage_complaints.block',
                    'damage_complaints.no_unit',
                    'damage_complaints.created_at',
                    DB::raw("DATE_FORMAT(damage_complaints.created_at, '%d-%m-%Y %H:%i:%s') as date_of_complaint"),
                    'latest_log.latest_status_id'
                );

            $complaintLists[$statusType->id] = $firstQuery->union($secondQuery)
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get(); 
        }

        return view('technician.dashboard', compact('statusTypes', 'complaintLists'));
    }

    public function complaintLists(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date') ?? $start_date;
        $withStatus = $request->input('status');

        $status = DB::table('status')->get();

        // If start_date is null, set it to today's date
        if (is_null($start_date)) {
            // Set default to 3 days ago, start of the day
            $start_date = now()->subDays(3)->startOfDay()->format('Y-m-d'); 
            
            // Set end date to the current time (end of today)
            $end_date = now()->endOfDay()->format('Y-m-d');
        }

        // Pre-compute the latest log ID for each complaint using GROUP BY
        // This eliminates the correlated subquery performance issue
        $latestLogIds = DB::table('damage_complaint_logs')
            ->select('damage_complaint_id', DB::raw('MAX(id) as max_log_id'))
            ->groupBy('damage_complaint_id');

        // Join with the actual log data to get the status
        $latestStatusSubquery = DB::table('damage_complaint_logs as dcl')
            ->joinSub($latestLogIds, 'latest', function($join) {
                $join->on('dcl.damage_complaint_id', '=', 'latest.damage_complaint_id')
                     ->on('dcl.id', '=', 'latest.max_log_id');
            })
            ->join('status', 'dcl.status_id', '=', 'status.id')
            ->select(
                'dcl.damage_complaint_id', 
                'status.name as latest_status',
                'status.id as latest_status_id'
            );

        // Query for students
        $firstQuery = DB::table('damage_complaints')
            ->join('eduhub.students', 'damage_complaints.ic', '=', 'eduhub.students.ic')
            ->join('damage_types', 'damage_complaints.damage_type_id', '=', 'damage_types.id')
            ->leftJoinSub($latestStatusSubquery, 'latest_log', 'damage_complaints.id', '=', 'latest_log.damage_complaint_id')
            ->where('eduhub.students.status', '2')
            ->whereBetween(DB::raw("CAST(damage_complaints.date_of_complaint AS DATE)"), [$start_date, $end_date])
            ->when($withStatus, function ($query) use ($withStatus) {
                return $query->where('latest_log.latest_status_id', '=', $withStatus);
            })
            ->select(
                'damage_complaints.id',
                'eduhub.students.name AS complainant_name',
                'damage_complaints.phone_number',
                'damage_types.name AS damage_type',
                'damage_complaints.block',
                'damage_complaints.no_unit',
                'damage_complaints.created_at',
                DB::raw("DATE_FORMAT(damage_complaints.created_at, '%d-%m-%Y %H:%i:%s') as date_of_complaint"),
                DB::raw("DATE_FORMAT(damage_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                DB::raw("DATE_FORMAT(damage_complaints.date_of_completion, '%d-%m-%Y') as date_of_completion"),
                DB::raw("DATEDIFF(NOW(), damage_complaints.date_of_complaint) as days_since_complaint"),
                'latest_log.latest_status', 
                'latest_log.latest_status_id'
            );

        // Query for users (staff)
        $secondQuery = DB::table('damage_complaints')
            ->join('eduhub.users', 'damage_complaints.ic', '=', 'eduhub.users.ic')
            ->join('damage_types', 'damage_complaints.damage_type_id', '=', 'damage_types.id')
            ->leftJoinSub($latestStatusSubquery, 'latest_log', 'damage_complaints.id', '=', 'latest_log.damage_complaint_id')
            ->whereBetween(DB::raw("CAST(damage_complaints.date_of_complaint AS DATE)"), [$start_date, $end_date])
            ->when($withStatus, function ($query) use ($withStatus) {
                return $query->where('latest_log.latest_status_id', '=', $withStatus);
            })
            ->select(
                'damage_complaints.id',
                'eduhub.users.name AS complainant_name',
                'damage_complaints.phone_number',
                'damage_types.name AS damage_type',
                'damage_complaints.block',
                'damage_complaints.no_unit',
                'damage_complaints.created_at',
                DB::raw("DATE_FORMAT(damage_complaints.created_at, '%d-%m-%Y %H:%i:%s') as date_of_complaint"),
                DB::raw("DATE_FORMAT(damage_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                DB::raw("DATE_FORMAT(damage_complaints.date_of_completion, '%d-%m-%Y') as date_of_completion"),
                DB::raw("DATEDIFF(NOW(), damage_complaints.date_of_complaint) as days_since_complaint"),
                'latest_log.latest_status', 
                'latest_log.latest_status_id'
            );

        $complaintLists = $firstQuery->union($secondQuery)
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('technician.damagecomplaint', compact('complaintLists', 'start_date', 'end_date', 'status'));
    }

    public function complaintListDetails(Request $request)
    {
        $id = $request->input('id');

        $complaintLists = DB::query()
            ->fromSub(function ($query) {
                $query->from(DB::table('damage_complaints')
                    ->join('eduhub.students', 'damage_complaints.ic', '=', 'eduhub.students.ic')
                    ->join('damage_types', 'damage_complaints.damage_type_id', '=', 'damage_types.id')
                    ->join('damage_type_details', 'damage_complaints.damage_type_detail_id', '=', 'damage_type_details.id')
                    ->leftjoin('technician', 'damage_complaints.technician_id', '=', 'technician.id')
                    ->leftJoin(DB::raw('(SELECT damage_complaint_logs.damage_complaint_id, status.name AS latest_status, status.id AS latest_status_id 
                                        FROM damage_complaint_logs
                                        JOIN status ON damage_complaint_logs.status_id = status.id
                                        WHERE damage_complaint_logs.id = 
                                        (SELECT MAX(id) FROM damage_complaint_logs AS logs WHERE logs.damage_complaint_id = damage_complaint_logs.damage_complaint_id)
                                        ) AS latest_log'), 'damage_complaints.id', '=', 'latest_log.damage_complaint_id')
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
                        DB::raw("DATE_FORMAT(damage_complaints.created_at, '%d-%m-%Y %H:%i:%s') as date_of_complaint"),
                        DB::raw("DATE_FORMAT(damage_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                        DB::raw("DATE_FORMAT(damage_complaints.date_of_completion, '%d-%m-%Y') as date_of_completion"),
                        'damage_complaints.technician_id',
                        'technician.name AS technician',
                        'latest_log.latest_status_id' // Add latest status
                    )
                    ->union(
                        DB::table('damage_complaints')
                            ->join('eduhub.users', 'damage_complaints.ic', '=', 'eduhub.users.ic')
                            ->join('damage_types', 'damage_complaints.damage_type_id', '=', 'damage_types.id')
                            ->join('damage_type_details', 'damage_complaints.damage_type_detail_id', '=', 'damage_type_details.id')
                            ->leftjoin('technician', 'damage_complaints.technician_id', '=', 'technician.id')
                            ->leftJoin(DB::raw('(SELECT damage_complaint_logs.damage_complaint_id, status.name AS latest_status, status.id AS latest_status_id 
                                                FROM damage_complaint_logs
                                                JOIN status ON damage_complaint_logs.status_id = status.id
                                                WHERE damage_complaint_logs.id = 
                                                (SELECT MAX(id) FROM damage_complaint_logs AS logs WHERE logs.damage_complaint_id = damage_complaint_logs.damage_complaint_id)
                                                ) AS latest_log'), 'damage_complaints.id', '=', 'latest_log.damage_complaint_id')
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
                            DB::raw("DATE_FORMAT(damage_complaints.created_at, '%d-%m-%Y %H:%i:%s') as date_of_complaint"),
                            DB::raw("DATE_FORMAT(damage_complaints.date_of_action, '%d-%m-%Y') as date_of_action"),
                            DB::raw("DATE_FORMAT(damage_complaints.date_of_completion, '%d-%m-%Y') as date_of_completion"),
                            'damage_complaints.technician_id',
                            'technician.name AS technician',
                            'latest_log.latest_status_id' // Add latest status
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

            //fetch status data
            $status = DB::table('status')->get();

            if ($request->ajax()) {
                return response()->json(['complaintLists' => $complaintLists, 'complaintLogs' => $complaintLogs, 'technicians' => $technicians, 'status' => $status]);
            }

        return view('technician.damagecomplaint', compact('complaintLists', 'complaintLogs', 'technicians', 'status'));
    }

    public function complaintUpdate(Request $request, $id) 
    {
        $date_of_action = $request->input('date_of_action');
        $technician = $request->input('technician');
        $status = $request->input('status');
        $notes = $request->input('notes');

        // Retrieve the damage complaint to check if date_of_action is already set
        $damageComplaint = DB::table('damage_complaints')
            ->where('id', $id)
            ->select('date_of_action')
            ->first();

        // Check if date_of_action is null, if yes then update both date_of_action and technician_id
        if (is_null($damageComplaint->date_of_action)) {
                
            // update date_of_action and technician based on damage_complaints id
            DB::table('damage_complaints')
                ->where('damage_complaints.id', $id)
                ->update(['date_of_action'=>$date_of_action, 'technician_id'=>$technician]);

            // Insert new log into damage_complaint_logs using the damage_complaints id
            DB::table('damage_complaint_logs')->insert([
                'damage_complaint_id' => $id,
                'notes' => 'Aduan telah diserahkan kepada juruteknik untuk tindakan',
                'status_id' => 2
            ]);

            return redirect()->route('technician.damagecomplaint')->with('success', 'Aduan telah dikemaskini dan diserahkan kepada juruteknik.');

        } else {
            if ($status == 2) { // belum selesai
                // Insert new log into damage_complaint_logs using the damage_complaints id
                DB::table('damage_complaint_logs')->insert([
                    'damage_complaint_id' => $id,
                    'notes' => ucfirst($notes),
                    'status_id' => 2
                ]);

                return redirect()->route('technician.damagecomplaint')->with('warning', 'Aduan telah dikemaskini.');

            } elseif ($status == 3) { // selesai
                // update date_of_completion based on damage_complaints id
                DB::table('damage_complaints')
                    ->where('damage_complaints.id', $id)
                    ->update(['date_of_completion'=>now()]);

                // Insert new log into damage_complaint_logs using the damage_complaints id
                DB::table('damage_complaint_logs')->insert([
                    'damage_complaint_id' => $id,
                    'notes' => ucfirst($notes),
                    'status_id' => 3
                ]);

                return redirect()->route('technician.damagecomplaint')->with('success', 'Aduan telah berjaya diselesaikan.');

            } elseif ($status == 4) { // batal
                // Insert new log into damage_complaint_logs using the damage_complaints id
                DB::table('damage_complaint_logs')->insert([
                    'damage_complaint_id' => $id,
                    'notes' => ucfirst($notes),
                    'status_id' => 4
                ]);

                return redirect()->route('technician.damagecomplaint')->with('danger', 'Aduan telah dibatalkan.');
            }
        }

        return redirect()->route('technician.damagecomplaint')->with('success', 'Aduan telah dikemaskini dan diserahkan kepada juruteknik.');
    }

    public function complaintCancel(Request $request, $id) 
    {
        $request->validate([
            'notes' => 'required|string'
        ]);

        $notes = $request->input('notes'); // Get the notes from the request

        DB::table('damage_complaint_logs')->insert([
            'damage_complaint_id' => $id,
            'notes' => ucfirst($notes),
            'status_id' => 4
        ]);

        return redirect()->route('technician.damagecomplaint')->with('danger', 'Aduan telah dibatalkan.');
    }

    public function damageReport(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date') ?? $start_date;
        $withStatus = $request->input('status');

        $status = DB::table('status')->get(['id', 'name']);

        if (is_null($start_date)) {
            // Set default to 3 days ago, start of the day
            $start_date = now()->subDays(3)->startOfDay()->format('Y-m-d'); 
            
            // Set end date to the current time (end of today)
            $end_date = now()->endOfDay()->format('Y-m-d');
        }

        // Pre-compute the latest log ID for each complaint using GROUP BY
        $latestLogIds = DB::table('damage_complaint_logs')
            ->select('damage_complaint_id', DB::raw('MAX(id) as max_log_id'))
            ->groupBy('damage_complaint_id');

        // Join with the actual log data to get the status
        $latestStatusSubquery = DB::table('damage_complaint_logs as dcl')
            ->joinSub($latestLogIds, 'latest', function($join) {
                $join->on('dcl.damage_complaint_id', '=', 'latest.damage_complaint_id')
                     ->on('dcl.id', '=', 'latest.max_log_id');
            })
            ->join('status', 'dcl.status_id', '=', 'status.id')
            ->select(
                'dcl.damage_complaint_id', 
                'status.name as latest_status',
                'status.id as latest_status_id'
            );

        // Query for students
        $firstQuery = DB::table('damage_complaints')
            ->join('eduhub.students', 'damage_complaints.ic', '=', 'eduhub.students.ic')
            ->join('damage_types', 'damage_complaints.damage_type_id', '=', 'damage_types.id')
            ->join('damage_type_details', 'damage_complaints.damage_type_detail_id', '=', 'damage_type_details.id')
            ->leftJoinSub($latestStatusSubquery, 'latest_log', 'damage_complaints.id', '=', 'latest_log.damage_complaint_id')
            ->where('eduhub.students.status', '2')
            ->whereBetween(DB::raw("CAST(damage_complaints.date_of_complaint AS DATE)"), [$start_date, $end_date])
            ->when($withStatus, function ($query) use ($withStatus) {
                return $query->where('latest_log.latest_status_id', '=', $withStatus);
            })
            ->select(
                'damage_complaints.id',
                DB::raw("DATE_FORMAT(damage_complaints.created_at, '%d-%m-%Y %H:%i:%s') as date_of_complaint"),
                'damage_types.name AS damage_type',
                'damage_type_details.name AS damage_type_detail',
                'damage_complaints.notes',
                'eduhub.students.name AS complainant_name',
                'damage_complaints.phone_number',
                'damage_complaints.block',
                'damage_complaints.no_unit',
                'latest_log.latest_status',
                'latest_log.latest_status_id',
                'damage_complaints.created_at'
            );

        // Query for users (staff)
        $secondQuery = DB::table('damage_complaints')
            ->join('eduhub.users', 'damage_complaints.ic', '=', 'eduhub.users.ic')
            ->join('damage_types', 'damage_complaints.damage_type_id', '=', 'damage_types.id')
            ->join('damage_type_details', 'damage_complaints.damage_type_detail_id', '=', 'damage_type_details.id')
            ->leftJoinSub($latestStatusSubquery, 'latest_log', 'damage_complaints.id', '=', 'latest_log.damage_complaint_id')
            ->whereBetween(DB::raw("CAST(damage_complaints.date_of_complaint AS DATE)"), [$start_date, $end_date])
            ->when($withStatus, function ($query) use ($withStatus) {
                return $query->where('latest_log.latest_status_id', '=', $withStatus);
            })
            ->select(
                'damage_complaints.id',
                DB::raw("DATE_FORMAT(damage_complaints.created_at, '%d-%m-%Y %H:%i:%s') as date_of_complaint"),
                'damage_types.name AS damage_type',
                'damage_type_details.name AS damage_type_detail',
                'damage_complaints.notes',
                'eduhub.users.name AS complainant_name',
                'damage_complaints.phone_number',
                'damage_complaints.block',
                'damage_complaints.no_unit',
                'latest_log.latest_status',
                'latest_log.latest_status_id',
                'damage_complaints.created_at'
            );

        $complaintLists = $firstQuery->union($secondQuery)
            ->orderBy('created_at', 'DESC')
            ->get(); 

        return view('technician.damagereportlist', compact('start_date', 'end_date', 'complaintLists', 'status'));
    }

    public function damageStatistic(Request $request)
    {
        $damagetype = $request->input('damagetype');
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
        if ($damagetype === null) {
            $damageTypes = DB::table('damage_types')->get();
        } else {
            $damageTypes = DB::table('damage_types')->where('damage_types.id', $damagetype)->get();
        }

        $status = DB::table('status')->whereIn('id', [1, 2, 3])->get(['id', 'name']);

        // Retrieve the latest logs for each complaint based on the latest `damage_complaint_log` entry (using MAX id)
        $latestLogs = DB::table('damage_complaint_logs as dcl')
            ->join('damage_complaints as dc', 'dcl.damage_complaint_id', '=', 'dc.id')
            ->select('dc.damage_type_id', 'dcl.status_id', DB::raw('COUNT(*) as total'), 
            DB::raw('DATE_FORMAT(dc.date_of_complaint, "%Y-%m-%d") as complaint_date'))
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
        return view('technician.damagereport', compact('dates', 'status', 'damageTypes', 'totalByDamageStatus', 'totalByStatus', 'firstDate', 'lastDate', 'damagetype'));
    }

}
