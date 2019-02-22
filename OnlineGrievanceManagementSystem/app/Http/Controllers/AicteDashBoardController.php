<?php

namespace App\Http\Controllers;

use App\Grievance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AicteDashBoardController extends Controller
{

    //Function for Statistics of Dashboard

    public function getStatistics($type){
        if($type=='total'){
            $count = Grievance::all()->count();
            return response(['message'=>$count],200);
        }elseif ( $type=='pending' ){
            $count = Grievance::whereIn('status',['addressed','raised'])->count();
            return response(['message'=>$count],200);
        }elseif ( $type=='escalated' ){
            $count = Grievance::whereIn('status',['delayed','reopened'])->count();
            return response(['message'=>$count],200);
        }elseif ( $type=='resolved' ){
            $count = Grievance::whereIn('status',['resolved'])->count();
            return response(['message'=>$count],200);
        }
        else{
            return response(['message'=>'Invalid type'],404);
        }
    }


    public function getStateStatistics(){
         $states = DB::select("select count(*) as count, table_college.state from table_grievance
                    INNER join user_student on table_grievance.student_id = user_student.id
                    INNER JOIN table_college on table_college.id = user_student.college_id
                      group by table_college.state order by count desc limit 5");
         $max =  count($states);
         $count=[];
         $state=[];
         $i=0;
        foreach ($states as $local_state){
            $count[$i]=$local_state->count;
            $state[$i++]=$local_state->state;
        }


         return response(['state'=>$state,'total'=>$count],200);
         }

         public function getCollegeStatistics(){

            $college_name = DB::select("select table_college.name from table_grievance
                            INNER join user_student on table_grievance.student_id = user_student.id
                            INNER JOIN table_college on table_college.id = user_student.college_id
                            group by table_college.name order by count(*) asc limit 5");
            $college=[];
            $i=0;
             $escalated=[];
             $resolved=[];
            $pending=[];
            foreach ($college_name as $name){
                $college[$i]=$name->name;
                $temp=DB::select("select count(*) as pending from table_grievance
                            INNER join user_student on table_grievance.student_id = user_student.id
                            INNER JOIN table_college on table_college.id = user_student.college_id
                            where table_college.name ='".$name->name."' and status in ('raised','addressed')");
                $pending[$i]=$temp[0]->pending;
                $temp=DB::select("select count(*) as escalated from table_grievance
                            INNER join user_student on table_grievance.student_id = user_student.id
                            INNER JOIN table_college on table_college.id = user_student.college_id
                            where table_college.name ='".$name->name."' and status in ('delayed','reopened')");
                $escalated[$i]=$temp[0]->escalated;
                $temp =DB::select("select count(*) as resolved from table_grievance
                            INNER join user_student on table_grievance.student_id = user_student.id
                            INNER JOIN table_college on table_college.id = user_student.college_id
                            where table_college.name ='".$name->name."' and status in ('resolved')");
                $resolved[$i++]=$temp[0]->resolved;
            }
            return response(['college'=>$college,'pending'=>$pending,'escalated'=>$escalated,'resolved'=>$resolved],200);
         }


         public function getGrievanceTypeStatistics(){
            $exam = DB::select("select count(*) as count,type from table_grievance group by type");

            $data=[];
            $i=0;
            foreach ($exam as $ex){
                $data[$i++]=[
                  $ex->type,$ex->count
                ];
            }
            return response([$data],200);
         }

         public function getYearStatistics(){
            $date = date('Y');
            $startyear = ($date).'-01-01 00:00:00';
            $endyear = ($date+1).'-01-01 00:00:00';
            $count1 = DB::select("SELECT count(*) as '".$date."' FROM `table_grievance` WHERE `created_at` < '$endyear' and created_at > '$startyear'");
             $startyear = ($date-1).'-01-01 00:00:00';
             $endyear = ($date).'-01-01 00:00:00';
             $count2 = DB::select("SELECT count(*) as '".($date-1)."' FROM `table_grievance` WHERE `created_at` < '$endyear' and created_at > '$startyear'");
             $startyear = ($date-2).'-01-01 00:00:00';
             $endyear = ($date-1).'-01-01 00:00:00';
             $count3 = DB::select("SELECT count(*) as '".($date-2)."' FROM `table_grievance` WHERE `created_at` < '$endyear' and created_at > '$startyear'");
             $startyear = ($date-3).'-01-01 00:00:00';
             $endyear = ($date-2).'-01-01 00:00:00';
             $count4 = DB::select("SELECT count(*) as '".($date-3)."' FROM `table_grievance` WHERE `created_at` < '$endyear' and created_at > '$startyear'");
             $startyear = ($date-4).'-01-01 00:00:00';
             $endyear = ($date-3).'-01-01 00:00:00';
             $count5 = DB::select("SELECT count(*) as '".($date-4)."' FROM `table_grievance` WHERE `created_at` < '$endyear' and created_at > '$startyear'");

            return response([$count1,$count2,$count3,$count4,$count5],200);
         }


}
