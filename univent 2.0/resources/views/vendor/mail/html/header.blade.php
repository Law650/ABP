@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
    {{-- Menggunakan url() agar menghasilkan alamat lengkap http://domain.com/.. --}}
    <img src="{{ url('images/univent-logo2.png') }}" alt="Univent Logo" style="height: 50px; width: auto; max-width: 100%;">
</a>
</td>
</tr>