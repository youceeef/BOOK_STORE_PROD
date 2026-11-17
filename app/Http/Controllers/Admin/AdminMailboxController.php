<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminMailboxController extends Controller
{
    /**
     * Display a live log of transactional emails sent by the application.
     */
    public function index(): View
    {
        $emails = cache()->get('dev_mailbox_log', []);

        return view('admin.mailbox.index', compact('emails'));
    }

    /**
     * Clear the in-memory mailbox log.
     */
    public function clear(): RedirectResponse
    {
        cache()->forget('dev_mailbox_log');

        return redirect()->route('admin.mailbox.index')->with('success', 'Mailbox activity log cleared.');
    }
}
