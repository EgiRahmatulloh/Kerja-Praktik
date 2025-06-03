<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'title' => 'Dashboard',
            'domisilis' => Entity::where('type', 'domisili')->latest()->paginate(5),
            'usahas' => Entity::where('type', 'usaha')->latest()->paginate(5),
            'totalDomisili' => Entity::where('type', 'domisili')->count(),
            'totalUsaha' => Entity::where('type', 'usaha')->count(),
        ]);
    }
}
