<?php

namespace App\Http\Controllers;
use App;
use DB;
use PDF;
use Validator;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    //Funcion que genera el reporte total en un rango de fechas y lo devuelve como un pdf.
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
       //Si solo se tiene la fecha de inicio se asume que se quiere nada mas el reporte de ese dia
        $begin = date('Y-m-d',strtotime($request->initialDate));
        if($request['finalDate'] != ''){
            $end = date('Y-m-d',strtotime($request->finalDate));
        }else{
            $end = $begin;
        }
        //Este query regresa el nombre y apellido de cada usuario junto con la suma de sus horas extra
        //En el rango de fecha dado.
        $query = DB::select(
            DB::raw("SELECT u.firstName AS FirstName, u.lastName AS LastName, SUM(h.total) AS Total
            FROM  users u, hours h
            WHERE u.`id` = h.`user_id` && (DATE(h.`initialDate`) BETWEEN '".$begin."' AND '".$end."')
            GROUP BY u.`id`, u.`firstName`, u.`lastName`
            "));

        //Luego se calcula el total sumando cada total individual
        $total = 0;
        foreach($query as $row){
            $total += $row->Total; 
        }
        //Se genera un pdf con la fecha de inicio, final y la lista de usuarios con sus totales
        $pdf = PDF::loadView('totalReportPdf', ['query' => $query,
        'total' => $total, 'begin' => $begin, 'end' => $end]);
        //Se regresa el PDF
        return  $pdf->download('Report.pdf');   
    }

}
