@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
<img src="{{ asset('images/logo-mark.png') }}" width="56" height="56" alt="Jaya Intero" style="display: block; margin: 0 auto 8px; border-radius: 12px;">
<span style="color: #4070b3; font-size: 16px; font-weight: 800; letter-spacing: 0.5px;">{{ $slot }}</span>
</a>
</td>
</tr>
