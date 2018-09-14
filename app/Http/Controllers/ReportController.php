<?php

namespace App\Http\Controllers;
use App;
use DB;
use PDF;
use Validator;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    function total(Request $request){
        $validator = Validator::make($request->all(), [	
            'initialDate' => 'required|date_format:d-m-Y|before_or_equal:finalDate',
            'finalDate' => 'nullable|date_format:d-m-Y',
        ]);
        if ($validator->fails()) {
           return response([
                    'status' => 'error',
                    'msg' => $validator->messages()->all()[0]
                ]);
        }
       
        $begin = date('Y-m-d',strtotime($request->initialDate));
        if(array_key_exists('finalDate', $request )){
            $end = date('Y-m-d',strtotime($request->finalDate));
        }else{
            $end = $begin;
        }
        $query = DB::select(
            DB::raw("SELECT u.firstName AS FirstName, u.lastName AS LastName, SUM(h.total) AS Total
            FROM  users u, hours h
            WHERE u.`id` = h.`user_id` && (DATE(h.`initialDate`) BETWEEN '".$begin."' AND '".$end."')
            GROUP BY u.`id`, u.`firstName`, u.`lastName`
            "));
        $total = 0;
        foreach($query as $row){
            $total += $row->Total; 
        }
        $pdf = PDF::loadView('totalReportPdf', ['query' => $query,
        'total' => $total, 'begin' => $begin, 'end' => $end]);
        
        return  $pdf->download('Report.pdf');   
    }
/*
    public function detailed(Request $request){
        $validator = Validator::make($request->all(), [	
            'initialDate' => 'required|date_format:d-m-Y|before_or_equal:finalDate',
            'finalDate' => 'nullable|date_format:d-m-Y',
        ]);
        if ($validator->fails()) {
           return response([
                    'status' => 'error',
                    'msg' => $validator->messages()->all()[0]
                ]);
        }
       
        $begin = date('Y-m-d',strtotime($request->initialDate));
        if(!array_key_exists('finalDate', $request)){
            $end = $begin;
        }else{
            $end = date('Y-m-d',strtotime($request->finalDate));
        }
        $query = DB::select(
            DB::raw("SELECT u.firstName AS FirstName, u.lastName AS LastName, SUM(h.total) AS Total
            FROM  users u, hours h
            WHERE u.`id` = h.`user_id` && (DATE(h.`initialDate`) BETWEEN '".$begin."' AND '".$end."')
            GROUP BY u.`id`, u.`firstName`, u.`lastName`
            "));
        $total = 0;
        foreach($query as $row){
            $total += $row->Total; 
        }
        $pdf = PDF::loadView('totalReportPdf', ['query' => $query,
        'total' => $total, 'begin' => $begin, 'end' => $end]);
        
        return  $pdf->download('Report.pdf');  
    }*/
}
