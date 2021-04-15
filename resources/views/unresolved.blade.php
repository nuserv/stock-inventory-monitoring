<div>
    <p style="color:blue;"><b>This is a system-generated email. Please do not reply.</b></p>
    <table>
        <tbody>
            <tr style="height:14pt">
                <td width="707" colspan="2" valign="top" style="width:690pt;border-top:none;border:solid #d9d9d9 1.0pt;background:red;"></td>
            </tr>
            <tr style="height:50pt">
                <td width="707" colspan="2" valign="top" style="width:690pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p><img src="{{asset('idsi.png')}}" height="50"/></p>
                </td>
            </tr>
            <tr style="height:14pt">
                <td width="707" colspan="2" valign="top" style="width:690pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>Please be informed that the current status is now UNRESOLVED after the five days given to resolve the issue.</p>
                </td>
            </tr>
            <tr style="height:14pt">
                <td width="165" style="width:143;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>Date Requested:</p>
                </td>
                <td width="542" style="width:546pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>
                        {{ Carbon\Carbon::parse($RDate)->toFormattedDateString().' '. Carbon\Carbon::parse($RDate)->toTimeString() }}
                    </p>
                </td>
            </tr>
            <tr style="height:14pt">
                <td width="165" style="width:143;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>Branch:</p>
                </td>
                <td width="542" style="width:546pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>
                        <span style="color:black">
                           {{ $branch }}
                        </span>
                    </p>
                </td>
            </tr>
            <tr style="height:14pt">
                <td width="165" style="width:143;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>Intransit Date:</p>
                </td>
                <td width="542" style="width:546pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>
                        <span style="color:black">
                           {{ Carbon\Carbon::parse($intransit)->toFormattedDateString().' '. Carbon\Carbon::parse($intransit)->toTimeString() }}
                        </span>
                    </p>
                </td>
            </tr>
            <tr style="height:14pt">
                <td width="165" style="width:143;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>Item Description:</p>
                </td>
                <td width="542" style="width:546pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>
                        <span style="color:black">
                          {{ $item }}
                        </span>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>