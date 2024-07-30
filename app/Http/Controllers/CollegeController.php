<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Inauguration;
use Illuminate\Http\Request;

class CollegeController extends Controller
{
    public function getCountries()
    {
        // Specify the database connection for the College model
        $countries = College::on('college_database')
            ->get()
            ->pluck('country')
            ->unique();
        return response()->json(['countries' => $countries]);
    }
    public function getStatesByCountry($country)
    {
        // Specify the database connection for the College model
        $states = College::on('college_database')
            ->where('country', $country)
            ->distinct()
            ->pluck('state');

        return response()->json(['states' => $states]);
    }
    public function updateElsi($id)
    {
        $college = College::on('college_database')
            ->where('id', $id)
            ->first();
        $college->IS_eLSI = 2;
        $college->save();
        return response()->json(['college' => $college]);
    }
    public function InaugurationStatusAvailable($id,$status)
    {
        $college = College::on('college_database')
            ->where('id', $id)
            ->first();
        $college->IS_eLSI = $status;
        $college->save();
        return response()->json(['college' => $college]);
        // ongoing inauguration status is 8 in the database
    }
    public function readyForInauguration($collegeName)
    {
        try{
        $inaugSlot = new Inauguration();
        $inaugSlot->college_name = $collegeName;
        $inaugSlot->save();
        }
        catch (ValidationException $e){
            return response()->json([
                'status' => 406,
                'message' => 'Ingauration Slot Not Registered',
                'errors' => $e->errors(),
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Ingauration Slot Registered',
            'data' => $inaugSlot,
        ]);
    }
    public function getAwaitingInaugurationColleges()
    {
        $colleges = College::on('college_database')
            ->where('IS_eLSI', 8)
            ->get();
        return response()->json(['colleges' => $colleges]);
    }
    public function getCollegesByState($country, $state)
    {
        // Specify the database connection for the College model
        $colleges = College::on('college_database')
            ->where('country', $country)
            ->where('state', $state)
            ->get();

        return response()->json(['colleges' => $colleges]);
    }

    public function getElsiColleges(){
        $colleges = College::on('college_database')
        ->orderby("college_name")->get();
        return response()->json(['colleges' => $colleges]);
    }
    public function getNonElsiColleges(){
        $colleges = College::on('college_database')
        ->where('IS_eLSI', 2)
        ->orderby("college_name")->get();
        return response()->json(['colleges' => $colleges]);
    }
    //function to update college data on database
    public function putCollegeData(Request $request, $id, $field){
        $college = College::on('college_database')->find($id);
        $updateableFields = ['IS_eLSI', 'inauguration_date', 'IS_eFSI', 'eYIC_allowed', 'college_name', 'country', 'state', 'district', 'city', 'pincode', 'address','website', 'Remarks', 'labrank', 'grade', 'reg_data', 'pay_proof', 'intent_letter'];
        if(in_array($field, $updateableFields)){
            $college->$field = $request->input($field);
            $college->save();
            return response()->json(['college' => $college], 200);
        }
        else{
            return response()->json(['message' => 'Updation Error'], 400);
        }
    }
}
