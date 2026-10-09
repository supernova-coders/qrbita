<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVCardRequest;
use App\Http\Requests\UpdateVCardRequest;
use App\Http\Resources\VCardResource;
use App\Models\VCard;
use App\Services\VCardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class VCardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        Gate::authorize('create', VCard::class);

        return Inertia::render('vcards/editor', [
            'vCard' => null,
            'aiConfigured' => filled(config('services.openai.api_key')),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVCardRequest $request, VCardService $service): RedirectResponse
    {
        $vCard = $service->create($request->user(), $request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Tu vCard fue creada como borrador.',
        ]);

        return redirect()->route('vcards.edit', $vCard);
    }

    /**
     * Display the specified resource.
     */
    public function show(VCard $vCard): RedirectResponse
    {
        Gate::authorize('view', $vCard);

        return redirect()->route('vcards.edit', $vCard);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VCard $vCard): Response
    {
        Gate::authorize('update', $vCard);
        $vCard->load(['links', 'qrCode']);

        return Inertia::render('vcards/editor', [
            'vCard' => VCardResource::make($vCard)->resolve(request()),
            'aiConfigured' => filled(config('services.openai.api_key')),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateVCardRequest $request,
        VCard $vCard,
        VCardService $service,
    ): RedirectResponse {
        $service->update($vCard, $request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Los cambios de tu vCard fueron guardados.',
        ]);

        return back();
    }

    public function destroy(VCard $vCard): RedirectResponse
    {
        Gate::authorize('delete', $vCard);

        abort(405);
    }
}
