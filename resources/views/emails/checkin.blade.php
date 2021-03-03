@component('mail::message')
# Terima kasih telah melakukan check in 

Berikut detail pemesanan sebelumnya :
- Email Anda        : {{ $user->email }}<br>
- ID Ruangan        : {{ $booking->room_id }}<br>
- Nama Ruangan      : {{ $room->room_name }}<br>
- Total orang       : {{ $booking->total_person }}<br>
- Tanggal Booking   : {{ date('d/m/Y h:i:s a', strtotime($booking->booking_time)) }}<br>
- Catatan           : {{ $booking->noted }}<br>
- Check In          : {{ date('d/m/Y h:i:s a', strtotime($booking->check_in_time)) }}<br>

Jangan lupa check out jika sudah selesai menggunakan ruangan, terima kasih<br>
@endcomponent
