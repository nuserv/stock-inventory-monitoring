@php
    $isSlaOnlyCategory = auth()->user()->id == 326;
    $receivedStatuses = ['For repair', 'Conversion'];

    $returnsQuery = \App\Defective::query()
        ->select('return_no')
        ->where('status', 'For receiving');
    $receivedQuery = \App\Defective::query()
        ->whereIn('status', $receivedStatuses);
    $slaQuery = \App\Defective::query()
        ->whereIn('status', $receivedStatuses)
        ->whereNotNull('remarks')
        ->where('remarks', '!=', '');
    $repairedQuery = \App\Defective::query()
        ->where('status', 'Repaired');
    $unrepairableQuery = \App\Defective::query()
        ->whereIn('status', ['Unrepairable', 'Unrepairable approval']);

    if ($isSlaOnlyCategory) {
        $returnsQuery->where('category_id', 26);
        $receivedQuery->where('category_id', 26);
        $slaQuery->where('category_id', 26);
        $repairedQuery->where('category_id', 26);
        $unrepairableQuery->where('category_id', 26);
    } else {
        $returnsQuery->where('category_id', '!=', 26);
        $receivedQuery->where('category_id', '!=', 26);
        $slaQuery->where('category_id', '!=', 26);
        $repairedQuery->where('category_id', '!=', 26);
        $unrepairableQuery->where('category_id', '!=', 26);
    }

    $slaCount = $slaQuery->count();
    $receivedCount = $receivedQuery->count();
    $returnsCount = \App\Retno::query()
        ->whereIn('status', ['For receiving', 'Incomplete'])
        ->whereIn('return_no', $returnsQuery->pluck('return_no'))
        ->count();
    $repairedCount = $repairedQuery->count();
    $unrepairableCount = $unrepairableQuery->count();
@endphp

<div class="container-fluid pt-3">
    <div class="row justify-content-center" style="row-gap: 18px;">
        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
            <a href="{{ route('home.received.remarks') }}" style="text-decoration: none;">
                <center>
                    <div class="text-center">
                        <img style="height: 100px;" src="{{ asset('images/SLA.png') }}">
                    </div>
                    <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 38px; height: 38px; width: 120px; text-align: center; font-size: 20px; border-radius: 30px;">
                        {{ number_format($slaCount) }}
                    </div>
                    <strong style="color: #0d1a80; font-size: 16px;">SLA</strong>
                </center>
            </a>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
            <a href="{{ url('/') }}" style="text-decoration: none;">
                <center>
                    <div class="text-center">
                        <img style="height: 100px;" src="{{ asset('images/FOR-REPAIR.png') }}">
                    </div>
                    <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 38px; height: 38px; width: 120px; text-align: center; font-size: 20px; border-radius: 30px;">
                        {{ number_format($receivedCount) }}
                    </div>
                    <strong style="color: #0d1a80; font-size: 16px;">RECEIVED</strong>
                </center>
            </a>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
            <a href="{{ url('/returnview') }}" style="text-decoration: none;">
                <center>
                    <div class="text-center">
                        <img style="height: 100px;" src="{{ asset('images/RETURNS.png') }}">
                    </div>
                    <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 38px; height: 38px; width: 120px; text-align: center; font-size: 20px; border-radius: 30px;">
                        {{ number_format($returnsCount) }}
                    </div>
                    <strong style="color: #0d1a80; font-size: 16px;">RETURNS</strong>
                </center>
            </a>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
            <a href="{{ url('/repaired-ware') }}" style="text-decoration: none;">
                <center>
                    <div class="text-center">
                        <img style="height: 100px;" src="{{ asset('images/REPAIRED.png') }}">
                    </div>
                    <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 38px; height: 38px; width: 120px; text-align: center; font-size: 20px; border-radius: 30px;">
                        {{ number_format($repairedCount) }}
                    </div>
                    <strong style="color: #0d1a80; font-size: 16px;">REPAIRED</strong>
                </center>
            </a>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 col-6">
            <a href="{{ route('index.unrepair') }}" style="text-decoration: none;">
                <center>
                    <div class="text-center">
                        <img style="height: 100px;" src="{{ asset('images/UNREPAIRABLE.png') }}">
                    </div>
                    <div class="container" style="background-color: #0d1a80; color: white; margin-bottom: 5px; line-height: 38px; height: 38px; width: 120px; text-align: center; font-size: 20px; border-radius: 30px;">
                        {{ number_format($unrepairableCount) }}
                    </div>
                    <strong style="color: #0d1a80; font-size: 16px;">UNREPAIRABLE</strong>
                </center>
            </a>
        </div>
    </div>
</div>
