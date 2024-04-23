<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\DesignsDataTable;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ShowDesignController extends Controller
{
    public function index(DesignsDataTable $dataTable)
    {
        return $dataTable->render('admin.show-design.index');
    }
}
