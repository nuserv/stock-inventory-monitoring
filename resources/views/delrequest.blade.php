<div>
    <p>Your approval is required to delete the following request:</p>
    <table>
        <thead>
            <tr style="height:14pt">
                <td colspan="4" valign="top" style="border-top:none;border:solid black 1.0pt;background:red;"></td>
            </tr>
            <tr style="height:14pt">
                <td style="border-top:none;border:solid black 1.0pt;background:white;">
                    <p>Requested By: </p>
                </td>
                <td colspan="3" style="border-top:none;border:solid black 1.0pt;background:white;">
                    <p>{{auth()->user()->name}} {{auth()->user()->lastname}}</p>
                </td>
            </tr>
            <tr style="height:14pt">
                <td colspan="4" valign="top" style="border-top:none;border:solid black 1.0pt;background:white;">
                    <p>Stock Request No. {{$stockreq->request_no}}</p>
                </td>
            </tr>
            <tr style="height:14pt">
                <td style="border-top:none;border:solid black 1.0pt;background:white;">
                    <p>Branch : </p>
                </td>
                <td colspan="3" style="border-top:none;border:solid black 1.0pt;background:white;">
                    <p>{{$branch}}</p>
                </td>
            </tr>
            <tr style="height:14pt">
                <td style="border-top:none;border:solid black 1.0pt;background:white;">
                    <p>Branch Date Request:</p>
                </td>
                <td colspan="3" style="border-top:none;border:solid black 1.0pt;background:white;">
                    <p>{{Carbon\Carbon::parse($stockreq->created_at->toFormattedDateString().' '.$stockreq->created_at->toTimeString())->isoFormat('lll')}}</p>
                </td>
            </tr>
            <tr>
                <th style="border-top:none;border:solid black 1.0pt;background:white;">Category</th>
                <th style="border-top:none;border:solid black 1.0pt;background:white;">Description</th>
                <th style="border-top:none;border:solid black 1.0pt;background:white;">Qty-UOM</th>
                <th style="border-top:none;border:solid black 1.0pt;background:white;">Stock Available</th>
            </tr>
        </thead>
        <tbody>
            @foreach ( $req as  $data)
                <tr>
                    <td style="border-top:none;border:solid black 1.0pt;background:white;">{{$data->category}}</td>
                    <td style="border-top:none;border:solid black 1.0pt;background:white;">{{$data->description}}</td>
                    <td style="border-top:none;border:solid black 1.0pt;background:white;">{{$data->pending}} {{$data->UOM}}</td>
                    <td style="border-top:none;border:solid black 1.0pt;background:white;">
                        {{App\Warehouse::select(\DB::raw('SUM(CASE WHEN status = \'in\' THEN 1 ELSE 0 END) as stock'))
                        ->where('status', 'in')
                        ->where('items_id',$data->items_id)
                        ->groupBy('items_id')
                        ->first()->stock ?? '0'}} {{$data->UOM}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p style="font-size: 18px;"><b>Reason :</b> {{$reason}}</p>
    <hr>
    <a href="{{ url('/') }}/delete-approval?action=approve&code={{$key}}" style="cursor: pointer;"><button style="background-color:#0d1a80;color:white;cursor: pointer;">APPROVE</button></a>
    <a href="{{ url('/') }}/delete-approval?action=decline&code={{$key}}" style="cursor: pointer;"><button style="background-color:red;color:white;cursor: pointer;">DECLINE</button></a>
    <hr>
    <p style="color:blue;"><b>This is a system-generated email. Please do not reply.</b></p>
</div>