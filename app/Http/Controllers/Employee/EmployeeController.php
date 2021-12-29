<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendence;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Null_;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $user_id=Auth::user()->id;
        $c_date=date('Y-m-d');
        $user_atten['start_time']=Attendence::where('user_id', $user_id)->where('date', $c_date)->first();


        return view('Employee.dashboard', $user_atten);
    }
    public function attendance_history()
    {
        $user_id=Auth::user()->id;
        $atten_emp['emp_atten']=Attendence::where('user_id', $user_id)->orderBy('date', 'DESC')->get();
        return view('Employee.attendance_history', $atten_emp);
    }

    public function endtime(Request $request)
    {
        // $request->validate([
        //     'start_time' => 'required',
        //     'end_time' => 'required'

        // ]);
        // $user_id=Auth::user()->id;
        $user_id=$request->user_id;
        $atten_id=$request->atten_id;
//    $startTime = Carbon::parse('01:34:23');
//     $endTime = Carbon::parse('10:14:00');

//     $totalDuration =  $startTime->diff($endTime)->format('%H:%I:%S')." Minutes";
//     dd($totalDuration);
        $In_time_update=Attendence::find($atten_id);
        $todayDate = Carbon::now()->format('d-m-Y');
        $c_time='05:14:00 PM';
        //  $c_time=date('h:i:s A');
        $c_date=date('Y-m-d');
        $end_timee = date('h:i:s A', strtotime($c_time));

    $startTime = Carbon::parse($In_time_update->start_time);
    $endTime = Carbon::parse($c_time);

    $totalDuration =  $startTime->diff($endTime)->format('%H:%I:%S');
// dd($totalDuration);
// $sd=explode(':',$totalDuration);
// $h=$sd[0];
// $m=$sd[1];
// $s=$sd[2];

        $end_time = date('h:i:s A', strtotime($c_time));

                $total_time_seconds= Carbon::parse($In_time_update->start_time)->diffInSeconds($end_time);



$total_seconds =$total_time_seconds-28800;
$add_overtime_after_approve=$total_time_seconds-$total_seconds;
$after =gmdate("H:i:s", $add_overtime_after_approve);

$overtime =gmdate("H:i:s", $total_seconds);
// dd($total_hours,$total_minutes,$total_seconds);
        $check_atten_one_time=Attendence::where('user_id', $user_id)->where('date', $c_date)->first();
        if (isset($check_atten_one_time)) {
            if ($total_time_seconds >= 28800 ) {
                $In_time_update->user_id=$user_id;
                $In_time_update->end_time=$end_time;
                $In_time_update->date=$c_date;
                $In_time_update->work_time=$after;
                $In_time_update->overtime=$overtime;
                $In_time_update->status=0;
                $In_time_update->save();
            } else {
                $In_time_update->user_id=$user_id;
                $In_time_update->end_time=$end_time;
                $In_time_update->date=$c_date;
                $In_time_update->work_time=$totalDuration;
                $In_time_update->overtime=0;
                $In_time_update->status=0;
                $In_time_update->save();
            }
            return redirect()->back()->with('success', 'Your attendance successfully!');
        } else {
            return redirect()->back()->with('error', 'Your attendance Already Done!');
        }

        // dd( $total_time_hours,$end_time);
    }
    public function starttime(Request $request)
    {
        $user_id=$request->user_id;
        $c_date=date('Y-m-d');
        $c_time=date('h:i:s A');
        $start_time = date('h:i:s A', strtotime($c_time));

        $check_atten_one_time=Attendence::where('user_id', $user_id)->where('date', $c_date)->first();
        if (!isset($check_atten_one_time)) {
            $atten=new Attendence();
            $atten->user_id=$user_id;
            $atten->start_time='05:14:00 AM';
            $atten->date=$c_date;
            $atten->work_time=0;
            $atten->overtime=0;
            $atten->status=0;
            $atten->save();

            return redirect()->back()->with('success', 'Your attendance successfully!');
        } else {
            return redirect()->back()->with('error', 'Your attendance Already Done!');
        }
    }
}
