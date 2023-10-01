@php
    use Carbon\Carbon;
    $currentBalance = 0;
@endphp
<table id="statementDataTable" class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>DATE TIME</th>
            <th>AMOUNT</th>
            <th>TYPE</th>
            <th>DETAILS</th>
            <th>BALANCE</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($statementData as $key => $item)
            @php
                $currentBalance = $item->transaction_type == 'credit' ? $currentBalance + $item->amount : $currentBalance - $item->amount;
            @endphp
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ Carbon::parse($item->created_at)->format('d-m-Y h:i A') }}</td>
                <td>{{ number_format($item->amount, 2) }}</td>
                <td>{{ ucfirst($item->transaction_type) }}</td>
                <td>
                    @if ($item->details == 'transfer')
                        {{ 'Transfer ' . ($item->transaction_type == 'credit' ? 'from ' : 'to ') . $item->secondPartyDetails->email ?? 'NA' }}
                    @else
                        {{ ucfirst($item->details) }}
                    @endif
                </td>
                <td>{{ $currentBalance }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
