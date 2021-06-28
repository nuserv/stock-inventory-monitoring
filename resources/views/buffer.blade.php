<p>Hi {{ $RM }}</p>
@if ($role == 'rm')
    <p>Please find below the details of the Stock Request with Reference No. {{ $reference}} for your approval. Kindly click on the link below to access the system: </p>
    <p><b><a href="{{url('bufferviewlist')}}" target="_blank">{{url('bufferviewlist')}}</a></b>
@else
    <p>Please find below the details of the Stock Request with Reference No. {{ $reference}} for processing. Kindly click on the link below to process the request: </p>
    <p><b><a href="{{url('bufferviewlist')}}" target="_blank">{{url('bufferviewlist')}}</a></b></p>
@endif
<table style='border: 1px solid black'>
    <thead>
        <tr style='border: 1px solid black'>
            <th style='border-bottom: 1px solid black;font-size: 16px;'>CATEGORY</th>
            <th style='font-size: 16px;border-bottom: 1px solid black;border-left: 1px solid black;'>ITEM DESCRIPTION</th>
            <th style='font-size: 16px;border-bottom: 1px solid black;border-left: 1px solid black;'>QUANTITY</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($table as $data)
            <tr style='border: 1px solid black'>
                <td style='font-size: 12px;border-top: 1px solid black;'>{{ $data->category }}</td>
                <td style='font-size: 12px;border-top: 1px solid black;border-left: 1px solid black;'>{{ $data->item }}</td>
                <td style='font-size: 12px;border-top: 1px solid black;border-left: 1px solid black;'>{{ $data->qty }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<p><img src="{{asset('signature.png')}}" height="50"/></p>