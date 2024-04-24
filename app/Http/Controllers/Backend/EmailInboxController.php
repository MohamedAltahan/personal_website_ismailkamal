<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\DataTables\EmailInboxDataTable;
use App\Models\EmailInbox;
use Illuminate\Http\Request;

class EmailInboxController extends Controller
{
    public function index(EmailInboxDataTable $dataTable)
    {
        return $dataTable->render('admin.email-inbox.index');
    }

    public function show($id)
    {
        $message = EmailInbox::findOrFail($id);
        return view('admin.email-inbox.show', compact('message'));
    }
}
