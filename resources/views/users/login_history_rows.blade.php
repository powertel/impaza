@foreach($audits as $audit)
  <tr>
    <td class="text-nowrap">
      {{ \Illuminate\Support\Carbon::parse($audit->created_at)->format('d M Y, H:i') }}
    </td>
    <td>
      <div class="text-muted small">{{ $audit->notes }}</div>
    </td>
  </tr>
@endforeach

