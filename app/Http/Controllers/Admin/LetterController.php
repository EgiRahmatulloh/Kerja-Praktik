<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LetterType;
use App\Models\FilledLetter;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

class LetterController extends Controller
{
    public function index()
    {
        $letterTypes = LetterType::all();
        return View::make('admin.letters.index', compact('letterTypes'));
    }

    public function create(LetterType $letterType)
    {
        return View::make('admin.letters.create', compact('letterType'));
    }

    public function store(Request $request, LetterType $letterType)
    {
        $rules = [];
        foreach ($letterType->dataItems as $item) {
            $rule = '';
            if ($item->required) {
                $rule .= 'required|';
            }
            if ($item->tipe_input == 'text' || $item->tipe_input == 'textarea') {
                $rule .= 'string|max:255';
            } elseif ($item->tipe_input == 'date') {
                $rule .= 'date';
            } elseif ($item->tipe_input == 'select') {
                $rule .= 'in:' . implode(',', json_decode($item->opsi));
            }
            $rules[$item->key] = trim($rule, '|');
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $filledData = [];
        foreach ($letterType->dataItems as $item) {
            $value = $request->input($item->key);
            if (is_string($value) && strlen($value) > 0) {
                $value = ucfirst($value);
            }
            $filledData[$item->key] = $value;
        }

        // Generate nomor surat global
        $currentNumber = SystemSetting::get('global_letter_number', 0);
        $nextNumber = intval($currentNumber) + 1;
        
        // Update nomor global di system settings
        SystemSetting::set('global_letter_number', $nextNumber, 'Nomor surat global untuk semua jenis surat');

        // Set nomor surat dengan format 3 digit
        $noSurat = sprintf('%03d', $nextNumber);

        FilledLetter::create([
            'user_id' => Auth::id(),
            'letter_type_id' => $letterType->id,
            'filled_data' => $filledData,
            'status' => 'pending',
            'no_surat' => $noSurat,
        ]);

        return Redirect::route('admin.letters.history')->with('success', 'Surat berhasil diajukan!');
    }

    public function history()
    {
        $letters = FilledLetter::with('letterType')->orderBy('created_at', 'desc')->get();
        return View::make('admin.letters.history', compact('letters'));
    }

    public function show(FilledLetter $letter)
    {
        return View::make('admin.letters.show', compact('letter'));
    }

    public function edit(FilledLetter $letter)
    {
        return View::make('admin.letters.edit', compact('letter'));
    }

    public function update(Request $request, FilledLetter $letter)
    {
        $rules = [];
        foreach ($letter->letterType->dataItems as $item) {
            $rule = '';
            if ($item->required) {
                $rule .= 'required|';
            }
            if ($item->tipe_input == 'text' || $item->tipe_input == 'textarea') {
                $rule .= 'string|max:255';
            } elseif ($item->tipe_input == 'date') {
                $rule .= 'date';
            } elseif ($item->tipe_input == 'select') {
                $rule .= 'in:' . implode(',', json_decode($item->opsi));
            }
            $rules[$item->key] = trim($rule, '|');
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }

        $filledData = [];
        foreach ($letter->letterType->dataItems as $item) {
            $value = $request->input($item->key);
            if (is_string($value) && strlen($value) > 0) {
                $value = ucfirst($value);
            }
            $filledData[$item->key] = $value;
        }

        $letter->update([
            'filled_data' => $filledData,
            'status' => 'pending',
            'catatan_admin' => null,
        ]);

        return Redirect::route('admin.letters.history')->with('success', 'Surat berhasil diperbarui dan diajukan ulang!');
    }
}
