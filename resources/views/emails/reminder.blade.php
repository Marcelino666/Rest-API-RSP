@component('mail::message')
# Reminder untuk pemesanan ruangan hari ini

Berikut detail pemesanan sebelumnya :
- ID Ruangan        : {{ $booking->room_id }}<br>
- Total orang       : {{ $booking->total_person }}<br>
- Tanggal Booking   : {{ date('d/m/Y h:i:s a', strtotime($booking->booking_time)) }}<br>
- Catatan           : {{ $booking->noted }}<br>

Jangan lupa melakukan check in untuk hari ini, terima kasih<br>
@endcomponent
