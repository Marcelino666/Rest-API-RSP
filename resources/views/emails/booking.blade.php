@component('mail::message')
# Terima kasih telah melakukan pemesanan ruangan

Dengan detail pemesanan sebagai berikut :
- Email Anda        : {{ $user->email }}<br>
- ID Ruangan        : {{ $booking->room_id }}<br>
- Nama Ruangan      : {{ $room->room_name }}<br>
- Total orang       : {{ $booking->total_person }}<br>
- Tanggal Booking   : {{ date('d/m/Y h:i:s a', strtotime($booking->booking_time)) }}<br>
- Catatan           : {{ $booking->noted }}<br>

{{-- @component('mail::button', ['url' => ''])
Button Text
@endcomponent --}}

Jangan lupa ya, terima kasih<br>
{{-- {{ config('app.name') }} --}}
@endcomponent
