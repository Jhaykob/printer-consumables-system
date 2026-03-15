<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Gate;

class AuditLogController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-users'); // Only Super Admins should see the logs
        $logs = ActivityLog::with('user')->latest()->paginate(50);
        return view('admin.logs.index', compact('logs'));
    }
}
