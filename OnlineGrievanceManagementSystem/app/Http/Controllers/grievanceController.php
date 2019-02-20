<?php

namespace App\Http\Controllers;

use App\Http\Middleware\BasicAuth;
use Dotenv\Parser;
use Illuminate\Http\Request;
use App\User;
use App\Grievance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class grievanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $type = $request->get('grievance_against');
        $description = $request->get('details');
        $file = $request->get('selected_file');
        $student_id = DB::table('user_student')->where('user_id',Auth::user()->getAuthIdentifier())->get(['id','college_id']);
        $department_id = DB::table('table_department')->where('name','LIKE',$type)->where('college_id',$student_id[0]->college_id)->get(['id']);

        $grievance = new Grievance;
        $grievance->type = $type;
        $grievance->description = $description;
        $grievance->student_id = $student_id->id;
        $grievance->department_id = $department_id->id;
        $grievance->documents = $file==null?'':$file->store();
        $grievance->save();

        $data = [];
        $new_grievance = DB::table('table_grievance')->where('student_id',$student_id[0]->id)->orderBy('id','desc')->get(['id'])->first();

        DB::table('table_grievance_status')->insert([
            'grievance_id' => $new_grievance->id,
            'status' => 'raised',
            'eta' => 7,
            'level' => 1
        ]);

        $data = [
          'id' => $new_grievance->id,
          'message' => 'Your grievance is registered'
        ];
        return ($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $gid = Auth::user()->id;
         // $user_id = DB::table('users')->where('email', $email)->get(['id']);
         // return  $user_id->id;
        $student_id = DB::table('user_student')->where('user_id', $gid)->get(['id'])->pluck('id');
        $grievances = DB::table('table_grievance')->where(['id'=>$id,
            'student_id'=>$student_id])->get(['id','type','student_id','created_at','documents','department_id']);
        if($grievances->isEmpty())
            return response(['message'=>'No such grievance'],404);
        $grievance_status = DB::table('table_grievance_status')->where('grievance_id',$id)->get(['status','eta']);
        $department_name = DB::table('table_department')->where('id',$grievances[0]->department_id)->get(['name']);
        $data = [
            'grievance_id' => $grievances[0]->id,
            'grievance_type' => $grievances[0]->type,
            'data_of_issue'=> $grievances[0]->created_at,
            'attachment' => $grievances[0]->documents,
            'assigned_committee' => $department_name[0]->name,
            'status' => $grievance_status[0]->status,
            'eta' => $grievance_status[0]->eta
        ];
       
        return response(['message'=>$data],200);
    }



    public function statistics($type){
        $student_id = DB::table('user_student')->where('user_id',Auth::user()->id)->get(['id'])[0]->id;
        if($type == 'total'){
            $count = Grievance::all()->where('student_id',$student_id)->count();
            return ['type' => $type,'value'=>$count];
        }
        elseif ($type == 'satisfied'){
            $grievance_id = DB::table('table_grievance')->where('student_id',$student_id)->get(['id']);
            $data = [];
            $i = 0;
            foreach ($grievance_id as $id){
                $data[$i] = $id->id;
                $i++;
            }
            $count = DB::table('table_grievance_status')->where('status','resolved')->whereIn('grievance_id',$data)->count();
            return ['type' => $type,'value'=>$count];
        }
        elseif ($type == 'pending'){
            $array = ['raised','assigned'];
            $grievance_id = DB::table('table_grievance')->where('student_id',$student_id)->get(['id']);
            $data = [];
            $i = 0;
            foreach ($grievance_id as $id){
                $data[$i] = $id->id;
                $i++;
            }
            $count = DB::table('table_grievance_status')->whereIn('status',$array)->whereIn('grievance_id',$data)->count();
            return ['type' => $type,'value'=>$count];
        }

        elseif ($type == 'escalated'){
            $array = ['delayed','reopened'];
            $grievance_id = DB::table('table_grievance')->where('student_id',$student_id)->get(['id']);
            $data = [];
            $i = 0;
            foreach ($grievance_id as $id){
                $data[$i] = $id->id;
                $i++;
            }
            $count = DB::table('table_grievance_status')->whereIn('status',$array)->whereIn('grievance_id',$data)->count();
            return ['type' => $type,'value'=>$count];
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function download($path){
        if($path != null){
            return Storage::download($path);
        }
    }
}
