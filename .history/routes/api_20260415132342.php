// الفعاليات المنشورة (للتطبيق — بدون تسجيل دخول)
Route::get('/events', function () {
    $publishedStatus = \App\Models\Status::where('name', 'published')->first();
    if (!$publishedStatus) return response()->json([]);

    $events = \App\Models\Event::where('status_id', $publishedStatus->id)
        ->orderBy('event_date', 'asc')
        ->get()
        ->map(function ($event) {
            $totalSeats = \App\Models\Seat::where('is_vip_reserved', false)->count();
            $booked = \App\Models\Reservation::where('event_id', $event->id)
                ->where('status', '!=', 'cancelled')
                ->where('type', '!=', 'vip_guest')
                ->count();

            return [
                'id'          => $event->id,
                'title'       => $event->title,
                'description' => $event->description,
                'event_date'  => $event->event_date->format('Y-m-d'),
                'event_time'  => $event->event_time,
                'total_seats' => $totalSeats,
                'booked'      => $booked,
                'available'   => $totalSeats - $booked,
            ];
        });

    return response()->json($events);
});
