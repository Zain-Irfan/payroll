<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendence;
use App\Mail\TestMail;
use App\Models\Threshold;

use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class PayrollController extends Controller
{

    public function payroll(Request $request)
    {
        $threshold=Threshold::select('cycle','days')->distinct()->get();
        $department=Department::select('department','id')->get();
        $users=User::where('user_role','user')->select('first_name','id')->get();
                return view('Admin/payroll',get_defined_vars());
    }

    public function search(Request $request)
    {



        //  dd($request->input());
        // "cycle" => "7"
        // "start_date" => "2022-01-03"
        // "end_date" => "2022-01-07"
        // "Dept" => "2"
        // "Emp" => "12"
        $threshold=Threshold::select('cycle','days')->distinct()->get();
        $department=Department::select('department','id')->get();
        $users=User::where('user_role','user')->select('first_name','id')->get();
 if($request->cycle && $request->start_date && $request->DEPARTMENT && $request->Employee)
        {

// Add days to date and display it
        $end_date= date('Y-m-d', strtotime($request->start_date. ' + '.$request->cycle.' days'));
    //  $data = Attendence::select('user_id')->whereBetween('date', [$request->start_date, $end_date])->distinct()->get();
     $user= Attendence::with('user')->whereBetween('date', [$request->start_date, $end_date])->whereHas('user', function ($query) use ($request) {
            return $query->where('department',$request->DEPARTMENT);
           })
            ->whereHas('user', function ($query) use ($request) {
            return $query->where('id', $request->Employee);
           })
        ->distinct()->get('user_id');

                return view('Admin/newsearch',get_defined_vars());


        //dd($search['date_bw_data']);
        // dd($request->cycle,$request->start_date);
        }
  if($request->cycle && $request->start_date && $request->Dept )
        {


// Add days to date and display it
        $end_date= date('Y-m-d', strtotime($request->start_date. ' + '.$request->cycle.' days'));
    //  $data = Attendence::select('user_id')->whereBetween('date', [$request->start_date, $end_date])->distinct()->get();
     $user= Attendence::with('user')->whereBetween('date', [$request->start_date, $end_date])->whereHas('user', function ($query) use ($request) {
            return $query->where('department', $request->Dept);
           })
        ->distinct()->get('user_id');

                return view('Admin/newsearch',get_defined_vars());


        //dd($search['date_bw_data']);
        // dd($request->cycle,$request->start_date);
        }

        if($request->cycle && $request->start_date)
        {


// Add days to date and display it
        $end_date= date('Y-m-d', strtotime($request->start_date. ' + '.$request->cycle.' days'));
    //  $data = Attendence::select('user_id')->whereBetween('date', [$request->start_date, $end_date])->distinct()->get();
     $user= Attendence::with('user')->whereBetween('date', [$request->start_date, $end_date]) ->distinct()->get('user_id');

         return view('Admin/newsearch',get_defined_vars());


        //dd($search['date_bw_data']);
        // dd($request->cycle,$request->start_date);
        }






           return view('Admin/newsearch',compact('threshold','department','users'));
        // if($request->Emp !='')
        // {

        //     $post = User::find($request->Emp)->attendance;
        //     $user_id=$post[0]->user_id;
        //     $search['attendence_id']=$post[0]->id;

        //     $search['users_name']=User::where('id',$user_id)->select('first_name','id')->first();
        //     $hours = Attendence::where('user_id', $user_id)->sum('total_hours');
        // $overtime= Attendence::where('user_id', $user_id)->sum(DB::raw("TIME_TO_SEC(overtime)"));
        //      $search['over'] =gmdate("H:i", $overtime);

        //      $search['hours'] =gmdate("H:i", $hours);

        // }
        // if($request->Dept !='')
        // {
        //     $search['users_name_dep']=User::where('department',$request->Dept)->select('first_name','id')->get();
        // }


    }
    public function atten_get(Request $request)
    {
                $user_id= $request->atten_id;

        $atten_get=User::where('id',$user_id)->first();
 $hourly_rate=$atten_get->hourly_rate;
 $overtimr=$atten_get->ot_rate;

        $hT=$request->total_hourse;
              $oT=  $request->overtime;
$basichourstime=$hT-$oT;
           $basichourss=gmdate("H:i", $basichourstime);
           $totalhors=gmdate("H:i", $hT);
           $h=gmdate("H", $basichourstime);
                      $m=gmdate("i", $basichourstime);
                      $overtime=gmdate("h:i", $oT);
                      $oh=gmdate("h", $oT);
                      $om=gmdate("i", $oT);
           $divover=($overtimr/60)*$om;

$hormultple=$oh*$hourly_rate;
$totalovertime=$hormultple+$divover;
           $divh=($hourly_rate/60)*$m;
$hormultple=$h*$hourly_rate;
$totalbasichourspay=$hormultple+$divh;
$sumtotalbasicandovertinme=$totalbasichourspay+$totalovertime;
// dd($basichourstime,$hT);
$sum=ceil($sumtotalbasicandovertinme);

        $user_id= $request->atten_id;
        // $get_signle_atten=User::where('id',$user_id)->first();
 $Nis=$atten_get->nis;

$rate=$hourly_rate+$overtimr;


        $first_name=$atten_get->first_name;
        $dep_id=$atten_get->department;
        $ORP=$atten_get->ot_rate;
 $hourly_rate=$atten_get->hourly_rate;
  $trn=$atten_get->trn;

        $department_get=Department::where('id',$dep_id)->select('department')->first();
        $department_name= $department_get->department;
        //echo $first_name;
        //return $department_get->department;
       return response()->json(['department'=>$department_name,
       'first_name'=>$first_name,'orver_time_pay'=> $ORP,'hourly_rate'=>$hourly_rate,'nis'=>$Nis, 'totalbasichourspay'=>$totalbasichourspay
       ,'trn'=>$trn,'totalhors'=>$totalhors,'basic_pay'=>$basichourss,'overtimrate'=>$overtimr,'overtime'=>$overtime ,'totalovertime'=>$totalovertime,'sumtotalbasicandovertinme'=>$sum,'rate'=>$rate]);
        //dd($atten_get['department'],$dep_id,$atten_get->first_name);
    }
}
