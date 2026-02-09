@extends('backend.master')

@section('page_title', 'Balance Sheet')

@section('page_heading', 'Balance Sheet')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Balance Sheet</h4>

                    @php
                        $sections = [
                            'Assets' => $assets,
                            'Liabilities' => $liabilities,
                            'Equity' => $equity,
                        ];
                        $totals = ['Assets' => 0, 'Liabilities' => 0, 'Equity' => 0];

                        function renderChildren($children, $depth = 1)
                        {
                            foreach ($children as $child) {
                                if ($child->balance != 0) {
                                    echo '<tr>';
                                    echo "<td class='pl-" . $depth * 3 . "'>- " . $child->account_name . '</td>';
                                    echo "<td class='text-right'>" . number_format($child->balance, 2) . '</td>';
                                    echo '</tr>';

                                    // Recursive call for nested children
                                    renderChildren($child->children, $depth + 1);
                                }
                            }
                        }
                    @endphp

                    @foreach ($sections as $sectionName => $sectionData)
                        <h5>{{ $sectionName }}</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th class="text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sectionData as $item)
                                    @php
                                        $totalBalance = $item->balance;
                                    @endphp

                                    @if ($totalBalance != 0)
                                        <tr>
                                            <td><strong>{{ $item->account_name }}</strong></td>
                                            <td class="text-right">{{ number_format($totalBalance, 2) }}</td>
                                        </tr>
                                        @php $totals[$sectionName] += $totalBalance; @endphp

                                        {{-- Render Child Accounts --}}
                                        {!! renderChildren($item->children) !!}
                                    @endif
                                @endforeach
                                <tr class="table-secondary">
                                    <th>Total {{ $sectionName }}</th>
                                    <th class="text-right">{{ number_format($totals[$sectionName], 2) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    @endforeach

                    @php
                        $totalLiabilitiesEquity = $totals['Liabilities'] + $totals['Equity'];
                        $difference = $totals['Assets'] - $totalLiabilitiesEquity;
                    @endphp

                    <h5>Total Liabilities and Equity</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Total Liabilities and Equity</th>
                            <td class="text-right">{{ number_format($totalLiabilitiesEquity, 2) }}</td>
                        </tr>
                    </table>

                    <h5>Balance Check</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Difference</th>
                            <td class="text-right">
                                @if ($difference == 0)
                                    <span class="text-success font-weight-bold">Balanced</span>
                                @else
                                    <span class="text-danger font-weight-bold">Unbalanced:
                                        {{ number_format($difference, 2) }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
