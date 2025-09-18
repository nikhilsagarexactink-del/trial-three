@if (!empty($cards) && count($cards) > 0)
    @foreach ($cards as $card)
        <tr>
            <td>{{ ucfirst($card['brand']) }}</td>
            <td>{{ $card['cardholder_name'] ?? 'N/A' }}</td>
            <td>{{"**********".$card['last4']}}</td>
            <td>{{ sprintf('%02d', $card['exp_month']) . '-' . $card['exp_year'] }}</td>
            <td>
                @if ($card['is_default'])
                    <span>Yes</span>
                @else
                    <span>No</span>
                @endif
            </td>
            <td>
                @if(!$card['is_default'])
                    <div class="dropdown">
                        <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="iconmoon-ellipse"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        
                            <a class="dropdown-item" onClick="setDefaultCard('{{ $card['id'] }}')"
                            href="javascript:void(0);">Set as default</a>
                            <a class="dropdown-item" onClick="deleteUserCard('{{ $card['id'] }}')"
                            href="javascript:void(0);" >Remove</a>
                        </div>
                    </div>
                @endif
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="12">
            <div class="alert alert-danger" role="alert">
                No Record Found.
            </div>
        </td>
    </tr>
@endif
