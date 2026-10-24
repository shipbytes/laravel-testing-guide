<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $invoices = $request->user()->company->invoices()
            ->with('client')
            ->latest('id')
            ->paginate(25);

        return view('invoices.index', ['invoices' => $invoices]);
    }
}
