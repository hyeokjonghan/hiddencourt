<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function reservationList(Request $request) {
        // Join 처리 없이 일단
        $query = Reservation::select('*');

        // 검색 조건 추가
        if($request->reservation_date) {
            $query->where('reservation_date', $request->reservation_date);
        }

        if($request->reservation_name) {
            $query->where('reservation_name', $request->reservation_name);
        }

        if($request->has('state')) {
            $query->where('state', $request->state);
        }

        return $query->paginate(10);
    }

    public function appendReservation(Request $request) {
        $validator = [
            'reservation_date'=>'required|date',
            'reservation_start_time'=>'required', 
            'reservation_end_time'=>'required',
            'reservation_name'=>'required|string'
        ];

        $validatorCheck = Validator::make($request->all(), $validator);
        if($validatorCheck->fails()) {
            return response($validatorCheck->errors()->all(), Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $strStartTime = strtotime($request->reservation_date.' '.$request->reservation_start_time);
        $strEndTime = strtotime($request->reservation_date.' '.$request->reservation_end_time);

        if($strEndTime <= $strStartTime) {
            return response()->caps('The end time must be later than the start time. ', Response::HTTP_METHOD_NOT_ALLOWED);
        }

        
        $checkReservation = Reservation::where('reservation_date', $request->reservation_date)
        ->where(function($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $q->whereTime('reservation_end_time','>=',$request->reservation_start_time)
                ->whereTime('reservation_start_time','<=',$request->reservation_start_time);
            })->orWhere(function ($q) use ($request) {
                $q->whereTime('reservation_start_time','<=',$request->reservation_end_time)
                ->whereTime('reservation_end_time','>=',$request->reservation_end_time);
            });
        })->get();

        if(count($checkReservation) > 0) {
            return response()->caps('this time is already reservation.', Response::HTTP_CONFLICT);
        }

        $reservation = new Reservation($request->all());
        $reservation->save();
        
        return $reservation;
    }


    // TEST 대기
    public function modifyReservationState(Request $request, Reservation $reservation) {
        $validator = [
            'state'=>'required|integer'
        ];

        $validatorCheck = Validator::make($request->all(), $validator);
        if($validatorCheck->fails()) {
            return response($validatorCheck->errors()->all(), Response::HTTP_METHOD_NOT_ALLOWED);
        }

        // 실상 그건 체크 할 필요 없지 않나... 0 || 1 일 때 영상 요청 가능하게 처리
        switch($request->state) {
            case 1:
                $now = date("Y-m-d H:i:s");
                $reservationEndDatetime = $reservation->reservation_date.' '.$reservation->reservation_end_time;
                $strNow = strtotime($now);
                $strTarget = strtotime($reservationEndDatetime);
                if($strNow > $strTarget) {
                    return response()->caps('The reservation deadline has not yet passed.', Response::HTTP_METHOD_NOT_ALLOWED);
                }
                break;
        }

        
        if($request->state > 2 || $request->state < 0) {
            return response()->caps('now allowed state.', Response::HTTP_METHOD_NOT_ALLOWED);
        } else {
            Reservation::where('reservation_no', $reservation->reservation_no)->update(['state'=>$request->state]);
            return Reservation::select('*')->where('reservation_no', $reservation->reservation_no)->first();
        }

    }

    // TEST 대기
    public function modifyReservation(Request $request, Reservation $reservation) {
        $modifyData = [
            'reservation_date'=>$reservation->reservation_date,
            'reservation_start_time'=>$reservation->reservation_start_time,
            'reservation_end_time'=>$reservation->reservation_end_time
        ];
        if($request->reservation_date) {
            $modifyData['reservation_date'] = $request->reservation_date;
        }
        if($request->reservation_start_time) {
            $modifyData['reservation_start_time'] = $request->reservation_start_time;
        }
        if($request->reservation_end_time) {
            $modifyData['reservation_end_time'] = $request->reservation_end_time;
        }
        if($request->reservation_name) {
            $modifyData['reservation_name'] = $request->reservation_name;
        }

        // check Reservation
        if($request->reservation_date || $request->reservation_start_time || $request->reservation_end_time) {

            $strStartTime = strtotime($modifyData['reservation_date'].' '.$modifyData['reservation_start_time']);
            $strEndTime = strtotime($modifyData['reservation_date'].' '.$modifyData['reservation_end_time']);
    
            
            if($strEndTime <= $strStartTime) {
                return response()->caps('The end time must be later than the start time. ', Response::HTTP_METHOD_NOT_ALLOWED);
            }

            $checkReservation = Reservation::where('reservation_date', $modifyData['reservation_date'])
            ->where('reservation_no','<>', $reservation->reservation_no)
            ->where(function($query) use ($modifyData) {
                $query->where(function ($q) use ($modifyData) {
                    $q->whereTime('reservation_end_time','>=',$modifyData['reservation_start_time'])
                    ->whereTime('reservation_start_time','<=',$modifyData['reservation_start_time']);
                })->orWhere(function ($q) use ($modifyData) {
                    $q->whereTime('reservation_start_time','<=',$modifyData['reservation_end_time'])
                    ->whereTime('reservation_end_time','>=',$modifyData['reservation_end_time']);
                });
            })->get();
    
            if(count($checkReservation) > 0) {
                return response()->caps('this time is already reservation.', Response::HTTP_CONFLICT);
            }
        }

        Reservation::where('reservation_no', $reservation->reservation_no)
        ->update($modifyData);

        return Reservation::select('*')->where('reservation_no', $reservation->reservation_no)->first();
    
    }

    // TEST 대기
    public function deleteReservation(Reservation $reservation) {
        Reservation::where('reservation_no', $reservation->reservation_no)
        ->delete();

        return response()->caps('reservation delete success.', Response::HTTP_OK);
    }

    // 그.. 영상 요청시 필요.. (실제 서버 쪽 테스트 이후 함수 만들어야 함)
    
}
