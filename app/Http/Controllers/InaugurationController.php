<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inauguration;

class InaugurationController extends Controller
{
    public function RegisterIngaurationSlot($slotName,$slotDate,$slotCollege,$slotCreatedAt){
        try{
            $inaugSlot = new Inauguration();
            $inaugSlot->slotname = $slotName;
            $inaugSlot->schedule_date = $slotDate;
            $inaugSlot->collegeName = $slotCollege;
            $inaugSlot->created_at = $slotCreatedAt;
            $inaugSlot->save();
        }catch (ValidationException $e){
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
    public function getInguarationSlots(){
        $slots = Inauguration::all();
        return response()->json(['slots' => $slots]);
    }
}
