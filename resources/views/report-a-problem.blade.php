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
                    <p>{{ auth()->user()->name.' '. auth()->user()->middlename.' '.auth()->user()->lastname}} has reported a problem.</p>
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
                    <p>Module:</p>
                </td>
                <td width="542" style="width:546pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>
                        <span style="color:black">
                           {{ $module }}
                        </span>
                    </p>
                </td>
            </tr>
            <tr style="height:14pt">
                <td width="165" style="width:143;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>Email:</p>
                </td>
                <td width="542" style="width:546pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>
                        <span style="color:black">
                          {{ $email }}
                        </span>
                    </p>
                </td>
            </tr>
            <tr style="height:14pt">
                <td width="165" style="width:143;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>Remarks:</p>
                </td>
                <td width="542" style="width:546pt;border-top:none;border:solid #d9d9d9 1.0pt;background:white;">
                    <p>
                        <span style="color:black">
                          {{ $remarks }}
                        </span>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>