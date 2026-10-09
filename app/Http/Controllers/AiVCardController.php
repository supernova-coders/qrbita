<?php

namespace App\Http\Controllers;

use App\AiGenerationStatus;
use App\Http\Requests\GenerateVCardWithAiRequest;
use App\Models\AiGeneration;
use App\Services\OpenAiVCardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiVCardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(
        GenerateVCardWithAiRequest $request,
        OpenAiVCardService $service,
    ): JsonResponse {
        $model = (string) config('services.openai.model');
        $generation = AiGeneration::query()->create([
            'user_id' => $request->user()->id,
            'v_card_id' => $request->user()->vCard?->id,
            'prompt' => $request->string('prompt')->toString(),
            'model' => $model,
            'status' => AiGenerationStatus::Processing,
        ]);

        try {
            $proposal = $service->generate(
                $request->string('prompt')->toString(),
                $request->array('current'),
                $request->user()->id,
            );

            $generation->update([
                'result' => $proposal,
                'status' => AiGenerationStatus::Completed,
            ]);

            return response()->json(['proposal' => $proposal]);
        } catch (Throwable $exception) {
            $generation->update([
                'status' => AiGenerationStatus::Failed,
                'error_message' => 'No fue posible generar la propuesta.',
            ]);

            Log::warning('OpenAI vCard generation failed.', [
                'generation_id' => $generation->id,
                'exception' => $exception::class,
            ]);

            return response()->json([
                'message' => 'La asistencia de IA no está disponible en este momento. Puedes continuar con el formulario manual.',
            ], 503);
        }
    }
}
