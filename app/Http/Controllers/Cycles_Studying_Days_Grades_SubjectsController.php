<?php

namespace App\Http\Controllers;
use App\Http\Requests;
use App\Cycles_Studying_Days_Grades_Subjects;
use App\Cycles_Studying_Days_Grades;
use App\Subjects_Students;
use App\Subjects;
use App\Inscriptions_Cycles_Studying_Days;
use App\Inscriptions;
use Response;
use Validator;
use Illuminate\Http\Request;
use DB;

class Cycles_Studying_Days_Grades_SubjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index()
     {
         return Response::json(Cycles_Studying_Days_Grades_Subjects::all(), 200);
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
         $validator = Validator::make($request->all(), [
             'subject'          => 'required',
             'grade'            => 'required'
         ]);
         if ( $validator->fails() ) {
             $returnData = array (
                 'status' => 400,
                 'message' => 'Invalid Parameters',
                 'validator' => $validator
             );
             return Response::json($returnData, 400);
         }
         else {
            try {
                $newObject = new Cycles_Studying_Days_Grades_Subjects();
                $newObject->subject          = $request->get('subject');
                $newObject->csdg             = $request->get('grade');
                $newObject->save();
                return Response::json($newObject, 200);
            
            } catch (\Illuminate\Database\QueryException $e) {
                if($e->errorInfo[0] == '01000'){
                    $errorMessage = "Error Constraint";
                } else {
                    $errorMessage = $e->getMessage();
                }
                $returnData = array (
                    'status' => 505,
                    'SQLState' => $e->errorInfo[0],
                    'message' => $errorMessage
                );
                return Response::json($returnData, 500);
            } catch (Exception $e) {
                $returnData = array (
                    'status' => 500,
                    'message' => $e->errorInfo()
                );
                return Response::json($returnData, 500);
            } 
         }
     }
 
     public function setSubjects(Request $request)
     {
         try
         {
             if ( $request->get('subjects') )
             {
                 DB::beginTransaction();
                 $Array = $request->get('subjects');
                 $master = $request->get('grade');
                 $saved = collect();
                 foreach ($Array as $value)
                 {
                     $existe = Cycles_Studying_Days_Grades_Subjects::whereRaw('subject=? and csdg=?',[$value['id'],$master])->first();
                     if(count($existe)<=0){    
                         $registro = new Cycles_Studying_Days_Grades_Subjects();
                         $registro->subject       = $value['id'];
                         $registro->csdg         = $master;
                         
                         $registro->save();
                         $saved->push($registro->id);
                        
                     }
                 }
                 foreach ($saved as $ids) {
                    $materiasId = Inscriptions_Cycles_Studying_Days::select('inscription')->whereRaw('csdg=?',$master)->get();
                    foreach ($materiasId as $value1) {
                        $registro = new Subjects_Students();
                        $registro->year = date('Y-m-d');
                        $registro->cycle_study_day_grade_subject = $ids;
                        $idStudent=Inscriptions::select('student')->where('id',$value1['inscription'])->first();
                        $registro->student = $idStudent->student;
                        $registro->save();
                        
                    }
                 }
                  
         
                 DB::commit();
                 $returnData = array (
                     'status' => 200,
                     'message' => 'Success'
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
     public function removeSubjects(Request $request)
     {
         try
         {
             if ( $request->get('subjects') )
             {
                 DB::beginTransaction();
                 $Array = $request->get('subjects');
                 $master = $request->get('grade');
                 $studentsId = collect();
                 foreach ($Array as $value)
                 {
                     $objectDelete = Cycles_Studying_Days_Grades_Subjects::whereRaw('subject=? and csdg=?',[$value['id'],$master])->first();
                     if(count($objectDelete)>0){    
                         $studentsId->push($objectDelete->id); 
                         Cycles_Studying_Days_Grades_Subjects::destroy($objectDelete->id);    
                         $materiasId = Inscriptions_Cycles_Studying_Days::select('inscription')->whereRaw('csdg=?',$master)->get();
                         foreach ($materiasId as $value1) {
                             
                             $idStudent=Inscriptions::select('student')->where('id',$value1['inscription'])->first();
                             $objectDeleteId = Subjects_Students::select('id')->whereRaw('student=? and cycle_study_day_grade_subject=?',[$idStudent->student,$objectDelete->id])->first();
                             Subjects_Students::destroy($objectDeleteId->id);
                             
                         }
                         
                         
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

     public function getBussyCycles_Studying_Days_Grades_Subjects()
     {
         $objectSee = Cycles_Studying_Days_Grades_Subjects::select('csdg')->get();
         if ($objectSee) {
 
             $objectRet = Cycles_Studying_Days_Grades::whereIn('id',$objectSee)->with('cycles_studying_days')->with('grades')->with('subjects')->get();
 
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
      * Display the specified resource.
      *
      * @param  int  $id
      * @return \Illuminate\Http\Response
      */
     public function show($id)
     {
         $objectSee = Cycles_Studying_Days_Grades_Subjects::find($id);
         if ($objectSee) {
             
             return Response::json($objectSee, 200);
         
         }
         else {
             $returnData = array (
                 'status' => 404,
                 'message' => 'No record found'
             );
             return Response::json($returnData, 404);
         }
     }
     public function getGradesSubjects($id)
     {
         $objectSee = Cycles_Studying_Days_Grades_Subjects::select('subject')->where('csdg',$id)->get();
         if ($objectSee) {
             $subjects = Subjects::whereIn('id',$objectSee)->orderby('name')->get();
             return Response::json($subjects, 200);
         
         }
         else {
             $returnData = array (
                 'status' => 404,
                 'message' => 'No record found'
             );
             return Response::json($returnData, 404);
         }
     }

     public function getGradesSubjectsId($id,$id2)
     {
         $objectSee = Cycles_Studying_Days_Grades_Subjects::whereRaw('csdg=? and subject=?',[$id,$id2])->first();
         if ($objectSee) {
             
             return Response::json($objectSee, 200);
         
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
         $objectUpdate = Cycles_Studying_Days_Grades_Subjects::find($id);
         if ($objectUpdate) {
             try {
                 
                 $objectUpdate->subject          = $request->get('subject', $objectUpdate->subject);
                 $objectUpdate->csdg             = $request->get('grade', $objectUpdate->csdg);
 
                 $objectUpdate->save();
                 $objectUpdate->function;
                 return Response::json($objectUpdate, 200);
             } catch (\Illuminate\Database\QueryException $e) {
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
             } catch (Exception $e) {
                 $returnData = array (
                     'status' => 500,
                     'message' => $e->getMessage()
                 );
                 return Response::json($returnData, 500);
             }
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
      * Remove the specified resource from storage.
      *
      * @param  int  $id
      * @return \Illuminate\Http\Response
      */
     public function destroy($id)
     {
         $objectDelete = Cycles_Studying_Days_Grades_Subjects::find($id);
         if ($objectDelete) {
             try {
                 Cycles_Studying_Days_Grades_Subjects::destroy($id);
                 return Response::json($objectDelete, 200);
             } catch (Exception $e) {
                 $returnData = array (
                     'status' => 500,
                     'message' => $e->getMessage()
                 );
                 return Response::json($returnData, 500);
             }
         }
         else {
             $returnData = array (
                 'status' => 404,
                 'message' => 'No record found'
             );
             return Response::json($returnData, 404);
         }
     }
}
