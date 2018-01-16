<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

use App\Http\Requests;
use App\Teachers;
use App\Users;
use Response;
use Validator;
use DB;
use Hash;

class TeachersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json(Teachers::all(), 200);
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
            'firstname'     => 'required',
            'lastname'      => 'required',
            'address'       => 'required',
            'cellphone'     => 'required'
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
                    DB::beginTransaction();
                    $newObject = new Teachers();
                    $newObject->firstname       = $request->get('firstname');
                    $newObject->lastname        = $request->get('lastname');
                    $newObject->address         = $request->get('address');
                    $newObject->cellphone       = $request->get('cellphone');
                    $newObject->phone           = $request->get('phone');
                    $newObject->save();
                    if($request->get('username') && $request->get('email')){
                        $email = $request->get('email');
                        $email_exists  = Users::whereRaw("email = ?", $email)->count();
                        $user = $request->get('username');
                        $user_exists  = Users::whereRaw("username = ?", $user)->count();
                        if($email_exists == 0 && $user_exists == 0){
                            try {
                                $newObjectT = new Users();
                                $newObjectT->username         = $request->get('username');
                                $newObjectT->password         = Hash::make($request->get('password'));
                                $newObjectT->email            = $request->get('email');
                                $newObjectT->firstname        = $request->get('firstname');
                                $newObjectT->lastname         = $request->get('lastname');
                                $newObjectT->type             = 3;
                                $newObjectT->tutor            = $newObject->id;
                                Mail::send('emails.confirm', ['empresa' => 'FoxyLabs', 'url' => 'https://foxylabs.gt', 'app' => 'http://erpfoxy.foxylabs.xyz', 'password' => $request->get('password'), 'username' => $newObjectT->username, 'email' => $newObjectT->email, 'name' => $newObjectT->firstname.' '.$newObjectT->lastname,], function (Message $message) use ($newObjectT){
                                    $message->from('info@foxylabs.gt', 'Info FoxyLabs')
                                            ->sender('info@foxylabs.gt', 'Info FoxyLabs')
                                            ->to("".$newObjectT->email, $newObjectT->firstname.' '.$newObjectT->lastname)
                                            ->replyTo('info@foxylabs.gt', 'Info FoxyLabs')
                                            ->subject('Usuario Creado');
                                
                                });
                                $newObjectT->save();
                                DB::commit();
                            } catch (Exception $e) {
                                DB::rollback();
                                $returnData = array (
                                    'status' => 500,
                                    'message' => $e->getMessage()
                                );
                                return Response::json($returnData, 500);
                            }
                        } else {
                            DB::rollback();
                            $returnData = array(
                                'status' => 400,
                                'message' => 'User already exists',
                                'validator' => $validator->messages()->toJson()
                            );
                            return Response::json($returnData, 400);
                        }
                    }
                    return Response::json($newObject, 200);
                
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $objectSee = Teachers::find($id);
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
        $objectUpdate = Teachers::find($id);
        if ($objectUpdate) {
            try {
                
                $objectUpdate->firstname       = $request->get('firstname', $objectUpdate->firstname);    
                $objectUpdate->lastname        = $request->get('lastname', $objectUpdate->lastname);
                $objectUpdate->address         = $request->get('address', $objectUpdate->address);
                $objectUpdate->cellphone       = $request->get('cellphone', $objectUpdate->cellphone);
                $objectUpdate->phone           = $request->get('phone', $objectUpdate->phone); 
                $objectUpdate->qualification   = $request->get('qualification', $objectUpdate->qualification); 
                $objectUpdate->state           = $request->get('state', $objectUpdate->state);    

                $objectUpdate->save();
                return Response::json($objectUpdate, 200);
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
        $objectDelete = Teachers::find($id);
        if ($objectDelete) {
            try {
                Teachers::destroy($id);
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
