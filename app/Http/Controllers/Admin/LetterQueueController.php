<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LetterQueue;
use App\Models\FilledLetter;
use App\Models\ServiceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LetterQueueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Menampilkan daftar antrian surat
     */
    public function index(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $query = LetterQueue::with(['filledLetter.user', 'filledLetter.letterType']);

        // Filter antrian berdasarkan jadwal pelayanan milik admin yang sedang login
        $query->whereHas('serviceSchedule', function($q) {
            $q->where('user_id', auth()->id());
        });

        // Filter berdasarkan kepemilikan template atau pengaturan berbagi
        if ($user->sub_role) {
            $query->whereHas('filledLetter.letterType.templateSurat', function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhere('share_setting', 'public')
                  ->orWhere(function ($q2) use ($user) {
                      $q2->where('share_setting', 'limited')
                         ->whereHas('sharedWithUsers', function ($q3) use ($user) {
                             $q3->where('users.id', $user->id);
                         });
                  });
            });
        } else {
            // Admin utama melihat semua antrian, tetapi tetap kecualikan antrian surat yang dibuat oleh admin lain
            $adminUserIds = \App\Models\User::where('role', 'admin')->pluck('id');
            $query->whereHas('filledLetter', function ($q) use ($adminUserIds) {
                $q->whereNotIn('user_id', $adminUserIds);
            });
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('date')) {
            $date = $request->date;
            $query->whereDate('scheduled_date', $date);
        }

        // Filter berdasarkan nama pemohon
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('filledLetter.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $queues = $query->latest()->paginate(10);
        $serviceSchedule = ServiceSchedule::where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        return view('admin.letter-queues.index', compact('queues', 'serviceSchedule'));
    }

    /**
     * Menampilkan detail antrian surat
     */
    public function show($id)
    {
        $queue = LetterQueue::with(['filledLetter.user', 'filledLetter.letterType'])->findOrFail($id);
        return view('admin.letter-queues.show', compact('queue'));
    }

    /**
     * Menampilkan form untuk mengedit antrian surat
     */
    public function edit($id)
    {
        $queue = LetterQueue::with(['filledLetter.user', 'filledLetter.letterType'])->findOrFail($id);
        return view('admin.letter-queues.edit', compact('queue'));
    }

    /**
     * Memperbarui data antrian surat
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'scheduled_date' => 'required|date',
            'status' => 'required|in:waiting,processing,completed',
            'notes' => 'nullable|string',
        ]);

        $queue = LetterQueue::findOrFail($id);
        $queue->update([
            'scheduled_date' => $request->scheduled_date,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.letter-queues.show', $queue->id)
            ->with('success', 'Antrian surat berhasil diperbarui');
    }

    /**
     * Memperbarui status antrian surat
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:waiting,processing,completed',
            'notes' => 'nullable|string',
        ]);

        $queue = LetterQueue::findOrFail($id);
        $oldStatus = $queue->status;
        $newStatus = $request->status;

        // Update status antrian saat ini
        $queue->update([
            'status' => $newStatus,
            'notes' => $request->notes,
        ]);

        // Jika status diubah menjadi completed, atur antrian berikutnya
        if ($oldStatus != 'completed' && $newStatus == 'completed') {
            $this->advanceNextQueue($queue);
        }

        return redirect()->route('admin.letter-queues.show', $queue->id)
            ->with('success', 'Status antrian surat berhasil diperbarui');
    }

    /**
     * Memajukan antrian berikutnya setelah antrian saat ini selesai
     */
    private function advanceNextQueue($completedQueue)
    {
        // Cari jadwal pelayanan yang aktif
        $serviceSchedule = ServiceSchedule::where('is_active', true)->first();
        if (!$serviceSchedule) {
            return; // Tidak ada jadwal pelayanan aktif
        }

        // Bersihkan antrian duplikat terlebih dahulu
        $this->cleanupDuplicateQueues();

        // Cari antrian berikutnya berdasarkan ID dari jadwal pelayanan yang sama
        // Ambil semua antrian waiting dengan ID lebih besar
        $waitingQueues = LetterQueue::where('status', 'waiting')
            ->where('id', '>', $completedQueue->id) // Gunakan ID untuk memastikan urutan sesuai dengan urutan antrian
            ->where('service_schedule_id', $completedQueue->service_schedule_id) // Hanya antrian dari jadwal yang sama
            ->orderBy('id', 'asc')
            ->get();

        // Hapus antrian duplikat berdasarkan filled_letter_id
        $uniqueQueues = collect();
        $seenLetterIds = [];

        foreach ($waitingQueues as $queue) {
            if (!in_array($queue->filled_letter_id, $seenLetterIds)) {
                $uniqueQueues->push($queue);
                $seenLetterIds[] = $queue->filled_letter_id;
            }
        }

        // Ambil antrian pertama yang unik
        $nextQueue = $uniqueQueues->first();

        if (!$nextQueue) {
            return; // Tidak ada antrian berikutnya
        }

        // Hitung waktu sekarang
        $now = now();

        // Pastikan masih dalam jam pelayanan
        $today = $now->format('Y-m-d');
        $startTime = \Carbon\Carbon::parse($serviceSchedule->start_time)->setDateFrom($today);
        $endTime = \Carbon\Carbon::parse($serviceSchedule->end_time)->setDateFrom($today);

        // Jika sekarang di luar jam pelayanan, jangan ubah jadwal
        if ($now->lt($startTime) || $now->gt($endTime)) {
            return;
        }

        // Majukan jadwal antrian berikutnya ke sekarang
        $nextQueue->update([
            'scheduled_date' => $now,
        ]);

        // Sesuaikan jadwal untuk semua antrian setelahnya
        $this->adjustFollowingQueues($nextQueue, $serviceSchedule);
    }

    /**
     * Menyesuaikan jadwal untuk antrian-antrian berikutnya
     */
    private function adjustFollowingQueues($startQueue, $serviceSchedule)
    {
        // Ambil semua antrian setelah antrian yang dimajukan dari jadwal pelayanan yang sama
        $followingQueues = LetterQueue::where('status', 'waiting')
            ->where('id', '>', $startQueue->id) // Gunakan ID untuk memastikan urutan sesuai dengan urutan antrian
            ->where('service_schedule_id', $serviceSchedule->id) // Hanya antrian dari jadwal yang sama
            ->orderBy('id', 'asc')
            ->get();

        if ($followingQueues->isEmpty()) {
            return; // Tidak ada antrian berikutnya
        }

        // Hapus antrian duplikat berdasarkan filled_letter_id
        $uniqueQueues = collect();
        $seenLetterIds = [];

        foreach ($followingQueues as $queue) {
            if (!in_array($queue->filled_letter_id, $seenLetterIds)) {
                $uniqueQueues->push($queue);
                $seenLetterIds[] = $queue->filled_letter_id;
            }
        }

        $followingQueues = $uniqueQueues;

        // Gunakan waktu proses dari jadwal pelayanan
        $processingTime = $serviceSchedule->processing_time;

        // Mulai dari jadwal antrian yang baru saja diperbarui
        $nextScheduledDate = \Carbon\Carbon::parse($startQueue->scheduled_date)->copy()->addMinutes($processingTime);
        $currentDate = $nextScheduledDate->format('Y-m-d');
        $startTime = \Carbon\Carbon::parse($serviceSchedule->start_time)->setDateFrom($currentDate);
        $endTime = \Carbon\Carbon::parse($serviceSchedule->end_time)->setDateFrom($currentDate);

        // Periksa apakah jadwal pelayanan sedang dijeda
        $isPaused = $serviceSchedule->is_paused;
        $pauseEndTime = $isPaused ? \Carbon\Carbon::parse($serviceSchedule->pause_end_time) : null;

        foreach ($followingQueues as $queue) {
            // Pastikan masih dalam jam pelayanan
            $scheduleDate = $nextScheduledDate->format('Y-m-d');

            // Jika tanggal berubah, perbarui waktu mulai dan selesai
            if ($scheduleDate !== $currentDate) {
                $currentDate = $scheduleDate;
                $startTime = \Carbon\Carbon::parse($serviceSchedule->start_time)->setDateFrom($currentDate);
                $endTime = \Carbon\Carbon::parse($serviceSchedule->end_time)->setDateFrom($currentDate);
            }

            // Periksa apakah jadwal pelayanan sedang dijeda
            if ($isPaused) {
                $pauseEndTimeForDay = \Carbon\Carbon::parse($pauseEndTime->format('H:i:s'))->setDateFrom($scheduleDate);

                // Jika waktu terjadwal berada dalam rentang jeda
                if (
                    $nextScheduledDate->format('H:i:s') >= $startTime->format('H:i:s') &&
                    $nextScheduledDate->format('H:i:s') < $pauseEndTimeForDay->format('H:i:s')
                ) {
                    // Jadwalkan setelah waktu jeda selesai
                    $nextScheduledDate = $pauseEndTimeForDay->copy();
                }
            }

            // Jika jadwal melebihi jam selesai pelayanan, pindahkan ke hari kerja berikutnya
            if ($nextScheduledDate->gt($endTime)) {
                $nextDay = \Carbon\Carbon::parse($scheduleDate)->addDay();
                $currentDate = $nextDay->format('Y-m-d');
                $nextScheduledDate = \Carbon\Carbon::parse($serviceSchedule->start_time)->setDateFrom($nextDay);
                $startTime = \Carbon\Carbon::parse($serviceSchedule->start_time)->setDateFrom($currentDate);
                $endTime = \Carbon\Carbon::parse($serviceSchedule->end_time)->setDateFrom($currentDate);

                // Periksa lagi apakah jadwal pelayanan sedang dijeda untuk hari berikutnya
                if ($isPaused) {
                    $pauseEndTimeForDay = \Carbon\Carbon::parse($pauseEndTime->format('H:i:s'))->setDateFrom($currentDate);

                    // Jika waktu terjadwal berada dalam rentang jeda
                    if (
                        $nextScheduledDate->format('H:i:s') >= $startTime->format('H:i:s') &&
                        $nextScheduledDate->format('H:i:s') < $pauseEndTimeForDay->format('H:i:s')
                    ) {
                        // Jadwalkan setelah waktu jeda selesai
                        $nextScheduledDate = $pauseEndTimeForDay->copy();
                    }
                }
            }

            // Jika jadwal sebelum jam mulai pelayanan, pindahkan ke jam mulai pelayanan
            if ($nextScheduledDate->lt($startTime)) {
                $nextScheduledDate = $startTime->copy();
            }

            // Update jadwal antrian
            $queue->update([
                'scheduled_date' => $nextScheduledDate->copy(),
            ]);

            // Siapkan jadwal untuk antrian berikutnya
            $nextScheduledDate = $nextScheduledDate->copy()->addMinutes($processingTime);
        }
    }

    /**
     * Membersihkan antrian duplikat dari database
     * Hanya menyimpan satu antrian untuk setiap surat yang diisi
     */
    private function cleanupDuplicateQueues()
    {
        // Ambil semua antrian dengan status waiting dari jadwal pelayanan milik admin yang sedang login
        $waitingQueues = LetterQueue::where('status', 'waiting')
            ->whereHas('serviceSchedule', function($q) {
                $q->where('user_id', auth()->id());
            })
            ->get();

        // Kelompokkan berdasarkan filled_letter_id
        $groupedQueues = $waitingQueues->groupBy('filled_letter_id');

        $deletedCount = 0;

        // Untuk setiap kelompok, simpan hanya antrian dengan ID terkecil
        foreach ($groupedQueues as $filledLetterId => $queues) {
            if ($queues->count() > 1) {
                // Urutkan berdasarkan ID
                $sortedQueues = $queues->sortBy('id');

                // Simpan yang pertama (ID terkecil)
                $keepQueue = $sortedQueues->first();

                // Hapus sisanya
                foreach ($sortedQueues as $queue) {
                    if ($queue->id != $keepQueue->id) {
                        $queue->delete();
                        $deletedCount++;
                    }
                }
            }
        }

        return $deletedCount;
    }
}
