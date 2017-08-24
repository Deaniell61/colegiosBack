<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Charges;
use Response;
use Validator;
use DB;

class ChargesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(Charges::all(), 200);
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
            'tuition'          => 'required',
            'inscription'      => 'required',
            'charge_limit'     => 'required',
            'quantity'         => 'required',
            'increase'         => 'required',
            'idinscription'    => 'required'
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
                $newObject = new Charges();
                $newObject->tuition            = $request->get('tuition');
                $newObject->inscription        = $request->get('inscription');
                $newObject->charge_limit       = $request->get('charge_limit');
                $newObject->quantity           = $request->get('quantity');
                $newObject->increase           = $request->get('increase');
                $newObject->idinscription      = $request->get('idinscription');
                $newObject->save();
                return Response::json($newObject, 200);
            
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
    }
    public function setCharges(Request $request)
    {
        try
        {
            if ( $request->get('charges') )
            {
                DB::beginTransaction();
                $chargesArray = $request->get('charges');

               foreach ($chargesArray as $value)
                {
                    $registro = new Charges();
                    $registro->tuition       = $value['tuition'];
                    $registro->inscription   = $value['inscription'];
                    $registro->charge_limit  = $value['charge_limit'];
                    $registro->quantity      = $value['quantity'];
                    $registro->increase      = $value['increase'];
                    $registro->idinscription = $value['idinscription'];
                    $registro->save();
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

    public function setChargesUpdate(Request $request)
    {
        try
        {
            if ( $request->get('charges') )
            {
                DB::beginTransaction();
                $chargesArray = $request->get('charges');

               foreach ($chargesArray as $value)
                {
                    $objectUpdate = Charges::find($value['id']);
                    if ($objectUpdate) {
                        try {

                            $objectUpdate->state = $value['state'];
                    
                            $objectUpdate->save();
                           
                        } catch (Exception $e) {
                            DB::rollback();
                            $returnData = array (
                                'status' => 500,
                                'message' => $e->getMessage()
                            );
                            return Response::json($returnData, 500);
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
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $objectSee = Charges::find($id);
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

    public function getInscriptionsCharges(Request $request,$id)
    {
        if($request->get('option')=='pagados'){
        $objectSee = Charges::whereRaw('idinscription=? and state=0',$id)->get();}
        else
        if($request->get('option')=='porpagar'){
        $objectSee = Charges::whereRaw('idinscription=? and state=1',$id)->get();}
        else {
        $objectSee = Charges::whereRaw('idinscription=?',$id)->get();}

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
        $objectUpdate = Charges::find($id);
        if ($objectUpdate) {
            try {
                $objectUpdate->state              = $request->get('state', $objectUpdate->state);
                $objectUpdate->tuition            = $request->get('tuition', $objectUpdate->tuition);
                $objectUpdate->inscription        = $request->get('inscription', $objectUpdate->inscription);
                $objectUpdate->charge_limit       = $request->get('charge_limit', $objectUpdate->charge_limit);
                $objectUpdate->quantity           = $request->get('quantity', $objectUpdate->quantity);
                $objectUpdate->increase           = $request->get('increase', $objectUpdate->increase);
                $objectUpdate->idinscription      = $request->get('idinscription', $objectUpdate->idinscription);

                $objectUpdate->save();
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
        $objectDelete = Charges::find($id);
        if ($objectDelete) {
            try {
                Charges::destroy($id);
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
