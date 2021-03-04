<div>
    <p style="color:blue;"><b>This is a system-generated email. Please do not reply.</b></p>
    <table>
        <tbody>
            <tr style="height:14pt">
                <td width="707" colspan="3" valign="top" style="width:690pt;border-top:none;border:solid #d9d9d9 1.0pt;background:red;"></td>
            </tr>
            <tr style="height:50pt">
                <td width="707" colspan="3" valign="top" style="width:690pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p><img src="{{asset('idsi.png')}}" height="50"/></p>
                </td>
            </tr>
            <tr style="height:14pt">
                <td width="707" colspan="3" valign="top" style="width:690pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>{{ auth()->user()->name}}  {{ auth()->user()->lastname }} has updated the following user to Service center stock monitoring system.</p>
                </td>
            </tr>
            <tr style="height:14pt">
                <td width="165" style="width:143;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>Name:</p>
                </td>
                <td width="542" style="width:546pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>
                        {{ $olduser }}
                    </p>
                </td>
                <td width="542" style="width:546pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>
                        {{ $user }}
                    </p>
                </td>
            </tr>
            <tr style="height:14pt">
                <td width="165" style="width:143;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>Level:</p>
                </td>
                <td width="542" style="width:546pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>
                        <span style="color:black">
                           {{ $oldlevel }}
                        </span>
                    </p>
                </td><td width="542" style="width:546pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>
                        <span style="color:black">
                           {{ $level }}
                        </span>
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
                           {{ $oldbranch }}
                        </span>
                    </p>
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
                    <p>Date/Time:</p>
                </td>
                <td width="542" colspan="2" style="width:546pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>
                        <span style="color:black">
                          {{Carbon\Carbon::now()->toDayDateTimeString()}}
                        </span>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>