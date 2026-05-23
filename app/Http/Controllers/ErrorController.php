<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public function index(Request $request)
    {
        $query = ErrorLog::query()->latest();

        if ($level = $request->input('level')) {
            $query->where('level', $level);
        }
        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $errors = $query->paginate(40)->withQueryString();
        return view('admin.errors.index', compact('errors'));
    }

    public function resolve(ErrorLog $errorLog)
    {
        $errorLog->delete();
        return back()->with('status', 'Registo removido.');
    }
}
