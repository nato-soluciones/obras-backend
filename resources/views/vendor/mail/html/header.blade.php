@props(['url'])
<tr>
  <td class="header">
    <a href="{{ $url }}" style="display: inline-block;">
      @if (trim($slot) === 'Laravel')
        <img src="{{ env('APP_URL') }}/images/nato.png" class="logo" alt="Nato Inc.">
      @else
        {{ $slot }}
      @endif
    </a>
  </td>
</tr>
