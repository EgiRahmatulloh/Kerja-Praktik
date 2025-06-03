<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceSchedule;
use App\Models\LetterQueue;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ServiceScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Menampilkan daftar jadwal pelayanan
     */
    public function index()
    {
        $schedules = ServiceSchedule::latest()->get();
        return view('admin.service-schedules.index', compact('schedules'));
    }

    /**
     * Menampilkan form untuk membuat jadwal pelayanan baru
     */
    public function create()
    {
        return view('admin.service-schedules.create');
    }

    /**
     * Menyimpan jadwal pelayanan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'processing_time' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        ServiceSchedule::create([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'processing_time' => $request->processing_time,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.service-schedules.index')
            ->with('success', 'Jadwal pelayanan berhasil ditambahkan');
    }

    /**
     * Menampilkan form untuk mengedit jadwal pelayanan
     */
    public function edit($id)
    {
        $schedule = ServiceSchedule::findOrFail($id);
        return view('admin.service-schedules.edit', compact('schedule'));
    }

    /**
     * Memperbarui jadwal pelayanan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'processing_time' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $schedule = ServiceSchedule::findOrFail($id);
        $schedule->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'processing_time' => $request->processing_time,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.service-schedules.index')
            ->with('success', 'Jadwal pelayanan berhasil diperbarui');
    }

    /**
     * Menghapus jadwal pelayanan
     */
    public function destroy($id)
    {
        $schedule = ServiceSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('admin.service-schedules.index')
            ->with('success', 'Jadwal pelayanan berhasil dihapus');
    }

    /**
     * Menjeda jadwal pelayanan
     */
    public function pause(Request $request, $id)
    {
        $request->validate([
            'pause_message' => 'required|string|max:255',
            'pause_end_time' => 'required',
        ]);

        $schedule = ServiceSchedule::findOrFail($id);
        $schedule->update([
            'is_paused' => true,
            'pause_message' => $request->pause_message,
            'pause_end_time' => $request->pause_end_time
        ]);

        // Sesuaikan antrian yang terjeda ke jam yang tidak dijeda
        $this->adjustPausedQueues($schedule);

        return redirect()->route('admin.service-schedules.index')
            ->with('success', 'Jadwal pelayanan berhasil dijeda dan antrian telah disesuaikan');
    }

    /**
     * Membatalkan jeda jadwal pelayanan
     */
    public function unpause($id)
    {
        $schedule = ServiceSchedule::findOrFail($id);
        $schedule->update([
            'is_paused' => false,
            'pause_message' => null,
            'pause_end_time' => null
        ]);

        return redirect()->route('admin.service-schedules.index')
            ->with('success', 'Jadwal pelayanan berhasil diaktifkan kembali');
    }

    /**
     * Menyesuaikan antrian yang terjeda ke jam yang tidak dijeda
     */
    private function adjustPausedQueues($schedule)
    {
        // Ambil semua antrian dengan status waiting
        $waitingQueues = LetterQueue::where('status', 'waiting')
            ->orderBy('scheduled_date', 'asc')
            ->get();

        if ($waitingQueues->isEmpty()) {
            return; // Tidak ada antrian yang perlu disesuaikan
        }

        // Dapatkan waktu mulai dan selesai jadwal
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $pauseEndTime = Carbon::parse($schedule->pause_end_time);
        $processingTime = $schedule->processing_time;

        // Kelompokkan antrian berdasarkan tanggal
        $queuesByDate = $waitingQueues->groupBy(function ($queue) {
            return $queue->scheduled_date->format('Y-m-d');
        });

        foreach ($queuesByDate as $date => $queues) {
            // Atur jadwal untuk setiap antrian pada tanggal ini
            $nextScheduledDate = null;
            $queueDate = Carbon::parse($date);

            // Waktu akhir jeda pada tanggal ini
            $pauseEndDateTime = Carbon::parse($pauseEndTime->format('H:i:s'))->setDateFrom($queueDate);

            // Waktu selesai pelayanan pada tanggal ini
            $endDateTime = Carbon::parse($endTime->format('H:i:s'))->setDateFrom($queueDate);

            foreach ($queues as $queue) {
                $queueDateTime = Carbon::parse($queue->scheduled_date);
                $queueTime = Carbon::parse($queueDateTime->format('H:i:s'));

                // Jika waktu antrian berada dalam rentang jadwal yang dijeda
                if ($queueTime->between($startTime, $pauseEndTime)) {
                    // Jika ini adalah antrian pertama yang dijadwalkan ulang pada hari ini
                    if (!$nextScheduledDate) {
                        // Jadwalkan antrian setelah waktu selesai jeda
                        $nextScheduledDate = $pauseEndDateTime->copy();
                    }

                    // Jika jadwal melebihi jam selesai pelayanan, pindahkan ke hari berikutnya
                    if ($nextScheduledDate->gt($endDateTime)) {
                        $nextDay = $queueDate->copy()->addDay();
                        $nextScheduledDate = Carbon::parse($startTime->format('H:i:s'))->setDateFrom($nextDay);
                    }

                    // Update jadwal antrian
                    $queue->update([
                        'scheduled_date' => $nextScheduledDate,
                    ]);

                    // Siapkan jadwal untuk antrian berikutnya
                    $nextScheduledDate = $nextScheduledDate->copy()->addMinutes($processingTime);
                }
            }
        }
    }
}
