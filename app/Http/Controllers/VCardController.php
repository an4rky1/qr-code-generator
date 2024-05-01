<?php

namespace App\Http\Controllers;

use App\Models\VCard;
use App\Services\QrCodeService;
use Illuminate\Http\Request;

class VCardController extends Controller
{
    public function __construct(
        private readonly QrCodeService $qrCodeService
    ) {}

    public function create()
    {
        return view('vcard.create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $slug)
    {
        $vCard = VCard::where('slug', $slug)->firstOrFail();

        return view('vcard.show', compact('vCard'));
    }

    public function downloadQr(string $slug)
    {
        $vCard = VCard::where('slug', $slug)->firstOrFail();

        return $this->qrCodeService->generateStream($vCard);
    }
}
