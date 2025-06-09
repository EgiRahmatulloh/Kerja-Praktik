<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceSchedule;
use App\Models\LetterQueue;
use App\Services\HolidayService;
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

        // Aktifkan jadwal alternatif jika ada
        $alternativeSchedule = ServiceSchedule::where('is_active', false)
            ->where('id', '!=', $schedule->id)
            ->first();
            
        if ($alternativeSchedule) {
            $alternativeSchedule->update(['is_active' => true]);
        }

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
        // Ambil semua antrian dengan status waiting dan urutkan berdasarkan ID
        // untuk memastikan urutan yang konsisten
        $waitingQueues = LetterQueue::where('status', 'waiting')
            ->orderBy('id', 'asc')
            ->get();

        if ($waitingQueues->isEmpty()) {
            return; // Tidak ada antrian yang perlu disesuaikan
        }

        // Cari jadwal alternatif yang aktif dan tidak dijeda
        $alternativeSchedule = ServiceSchedule::where('is_active', true)
            ->where('is_paused', false)
            ->where('id', '!=', $schedule->id)
            ->first();

        // Dapatkan waktu mulai dan selesai jadwal yang dijeda
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $pauseEndTime = Carbon::parse($schedule->pause_end_time);
        $processingTime = $schedule->processing_time;

        // Cek apakah ada antrian yang dijadwalkan selama waktu jeda
        $hasPausedQueue = false;
        $firstPausedQueueIndex = -1;

        foreach ($waitingQueues as $index => $queue) {
            $queueDateTime = Carbon::parse($queue->scheduled_date);
            $queueTime = Carbon::parse($queueDateTime->format('H:i:s'));

            // Jika waktu antrian berada dalam rentang jadwal yang dijeda
            if ($queueTime->between($startTime, $pauseEndTime)) {
                $hasPausedQueue = true;
                $firstPausedQueueIndex = $index;
                break;
            }
        }

        // Jika tidak ada antrian yang terjeda, tidak perlu menyesuaikan
        if (!$hasPausedQueue) {
            return;
        }

        // Tentukan jadwal pengganti
        if ($alternativeSchedule) {
            // Gunakan jadwal alternatif yang aktif
            $queueDate = Carbon::parse($waitingQueues[$firstPausedQueueIndex]->scheduled_date->format('Y-m-d'));
            $nextScheduledDate = Carbon::parse($alternativeSchedule->start_time)->setDateFrom($queueDate);
            $endDateTime = Carbon::parse($alternativeSchedule->end_time)->setDateFrom($queueDate);
            $processingTime = $alternativeSchedule->processing_time;
            
            // Cari antrian terakhir di jadwal alternatif untuk hari yang sama
            $lastQueueInAlternative = LetterQueue::where('status', 'waiting')
                ->whereDate('scheduled_date', $queueDate)
                ->where('scheduled_date', '>=', $nextScheduledDate)
                ->where('scheduled_date', '<=', $endDateTime)
                ->orderBy('scheduled_date', 'desc')
                ->first();
                
            if ($lastQueueInAlternative) {
                $nextScheduledDate = Carbon::parse($lastQueueInAlternative->scheduled_date)
                    ->addMinutes($processingTime);
            }
        } else {
            // Jika tidak ada jadwal alternatif, gunakan waktu setelah jeda selesai
            $queueDate = Carbon::parse($waitingQueues[$firstPausedQueueIndex]->scheduled_date->format('Y-m-d'));
            $nextScheduledDate = Carbon::parse($pauseEndTime->format('H:i:s'))->setDateFrom($queueDate);
            $endDateTime = Carbon::parse($endTime->format('H:i:s'))->setDateFrom($queueDate);
        }

        // Jadwalkan ulang semua antrian mulai dari antrian yang terjeda
        for ($i = $firstPausedQueueIndex; $i < count($waitingQueues); $i++) {
            $queue = $waitingQueues[$i];

            // Jika jadwal melebihi jam selesai pelayanan, pindahkan ke hari kerja berikutnya
            if ($nextScheduledDate->gt($endDateTime)) {
                $holidayService = new HolidayService();
                $nextWorkingDay = $holidayService->getNextWorkingDay($queueDate);
                $queueDate = $nextWorkingDay; // Update tanggal antrian untuk hari kerja berikutnya
                $nextScheduledDate = Carbon::parse($startTime->format('H:i:s'))->setDateFrom($nextWorkingDay);
                $endDateTime = Carbon::parse($endTime->format('H:i:s'))->setDateFrom($nextWorkingDay);
            }
            
            // Pastikan tanggal yang dijadwalkan bukan hari libur
            $holidayService = new HolidayService();
            if ($holidayService->isHoliday($nextScheduledDate)) {
                $nextWorkingDay = $holidayService->getNextWorkingDay($nextScheduledDate);
                $queueDate = $nextWorkingDay;
                $nextScheduledDate = Carbon::parse($startTime->format('H:i:s'))->setDateFrom($nextWorkingDay);
                $endDateTime = Carbon::parse($endTime->format('H:i:s'))->setDateFrom($nextWorkingDay);
            }

            // Update jadwal antrian
            $queue->update([
                'scheduled_date' => $nextScheduledDate->copy(),
            ]);

            // Siapkan jadwal untuk antrian berikutnya
            $nextScheduledDate = $nextScheduledDate->copy()->addMinutes($processingTime);
        }
    }
}
