
<table style="width: 100%;">
    <!--<tr><th align="center"><img src="{{asset('Logo.jpg')}}" style="width: 10%;" ></th></tr>-->
    <tr><th align="center" style="font-size: 20px;">Reporte de horas Extras Acumuladas <br>desde {{$begin}} al {{$end}}</th></tr>
    <tr><th style="background-color: black; height: 1px;"></th></tr>
    <tr>
        <td>
            <table align="center">
                <tr>
                    <th align="left">Nombre</th>
                    <th align="center">Total</th>
                </tr>
                @foreach ($query as $row)
                <tr >
                    <td>{{$row->FirstName}} {{$row->LastName}}</td>
                    <td align="center">{{$row->Total}}</td>
                </tr>
                @endforeach
                <tr>
                    <td>Total</td>
                    <td align="center">{{$total}}</td>
                </tr>
            </table>
        </td>
    </tr>

</table>