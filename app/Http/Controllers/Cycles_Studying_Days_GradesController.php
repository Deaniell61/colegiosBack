<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Cycles_Studying_Days_Grades;
use App\Cycles_Studying_Days;
use Response;
use Validator;
use DB;

class Cycles_Studying_Days_GradesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(Cycles_Studying_Days_Grades::all(), 200);
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

    public function setGrades(Request $request)
    {
        try
        {
            if ( $request->get('grades') )
            {
                DB::beginTransaction();
                $Array = $request->get('grades');
                $master = $request->get('master');
                foreach ($Array as $value)
                {
                    $existe = Cycles_Studying_Days_Grades::whereRaw('grade=? and cycle_study_day=?',[$value['id'],$master])->first();
                    if(sizeof($existe)<=0){    
                        $registro = new Cycles_Studying_Days_Grades();
                        $registro->grade       = $value['id'];
                        $registro->cycle_study_day         = $master;
                        
                        $registro->save();
                    }
                }
        
                DB::commit();
                $returnData = array (
                    'status' => 200,
                    'message' => "success"
                );
                return Response::json($returnData, 200);
            }
            else
            {
                DB::rollback();
                $returnData = array (
                    'status' => 400,
                    'message' => 'Invalid Parameters'
                );
                return Response::json($returnData, 200);
            }    
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            if($e->errorInfo[0] == '01000'){
                $errorMessage = "Error Constraint";
            }  else {
                $errorMessage = $e->getMessage();
            }
            $returnData = array (
                'status' => 505,
                'SQLState' => $e->errorInfo[0],
                'message' => $errorMessage
            );
            return Response::json($returnData, 500);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $returnData = array (
                'status' => 500,
                'message' => $e->getMessage()
            );
            return Response::json($returnData, 500);
        }
    }
    public function removeGrades(Request $request)
    {
        try
        {
            if ( $request->get('grades') )
            {
                DB::beginTransaction();
                $Array = $request->get('grades');
                $master = $request->get('master');
                $studentsId = collect();
                foreach ($Array as $value)
                {
                    $objectDelete = Cycles_Studying_Days_Grades::whereRaw('grade=? and cycle_study_day=?',[$value['id'],$master])->first();
                    if(sizeof($objectDelete)>0){    
                        $studentsId->push($objectDelete->id); 
                        Cycles_Studying_Days_Grades::destroy($objectDelete->id);      
                    } 
                }

                DB::commit();
                $returnData = array (
                    'status' => 200,
                    'message' => "success"
                );
                return Response::json($returnData, 200);
            }
            else
            {
                DB::rollback();
                $returnData = array (
                    'status' => 400,
                    'message' => 'Invalid Parameters'
                );
                return Response::json($returnData, 400);
            }    
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            if($e->errorInfo[0] == '01000'){
                $errorMessage = "Error Constraint";
            }  else {
                $errorMessage = $e->getMessage();
            }
            $returnData = array (
                'status' => 505,
                'SQLState' => $e->errorInfo[0],
                'message' => $errorMessage
            );
            return Response::json($returnData, 500);
        }
        catch (Exception $e)
        {
            DB::rollback();
            $returnData = array (
                'status' => 500,
                'message' => $e->getMessage()
            );
            return Response::json($returnData, 500);
        }
    }

    public function getBussyCycles_Studying_Days_Grades()
    {
        $objectSee = Cycles_Studying_Days_Grades::select('cycle_study_day')->get();
        if ($objectSee) {

            $objectRet = Cycles_Studying_Days::whereIn('id',$objectSee)->with('cycles')->with('studying_days')->get();

            return Response::json($objectRet, 200);
        
        }
        else {
            $returnData = array (
                'status' => 404,
                'message' => 'No record found'
            );
            return Response::json($returnData, 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
}
