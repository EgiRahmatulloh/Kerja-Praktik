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
        $query = LetterQueue::with(['filledLetter.user', 'filledLetter.letterType']);

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
        $serviceSchedule = ServiceSchedule::where('is_active', true)->first();

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

        // Cari antrian berikutnya berdasarkan ID
        // Ambil semua antrian waiting dengan ID lebih besar
        $waitingQueues = LetterQueue::where('status', 'waiting')
            ->where('id', '>', $completedQueue->id) // Gunakan ID untuk memastikan urutan sesuai dengan urutan antrian
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
        // Ambil semua antrian setelah antrian yang dimajukan
        $followingQueues = LetterQueue::where('status', 'waiting')
            ->where('id', '>', $startQueue->id) // Gunakan ID untuk memastikan urutan sesuai dengan urutan antrian
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

        foreach ($followingQueues as $queue) {
            // Pastikan masih dalam jam pelayanan
            $scheduleDate = $nextScheduledDate->format('Y-m-d');
            $startTime = \Carbon\Carbon::parse($serviceSchedule->start_time)->setDateFrom($scheduleDate);
            $endTime = \Carbon\Carbon::parse($serviceSchedule->end_time)->setDateFrom($scheduleDate);

            // Jika jadwal melebihi jam selesai pelayanan, pindahkan ke hari kerja berikutnya
            if ($nextScheduledDate->gt($endTime)) {
                $nextDay = \Carbon\Carbon::parse($scheduleDate)->addDay();
                $nextScheduledDate = \Carbon\Carbon::parse($serviceSchedule->start_time)->setDateFrom($nextDay);
            }

            // Jika jadwal sebelum jam mulai pelayanan, pindahkan ke jam mulai pelayanan
            if ($nextScheduledDate->lt($startTime)) {
                $nextScheduledDate = $startTime;
            }

            // Update jadwal antrian
            $queue->update([
                'scheduled_date' => $nextScheduledDate,
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
        // Ambil semua antrian dengan status waiting
        $waitingQueues = LetterQueue::where('status', 'waiting')->get();

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
