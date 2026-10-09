<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class OpenAiVCardService
{
    /**
     * @param  array<string, mixed>  $current
     * @return array<string, mixed>
     */
    public function generate(string $prompt, array $current, int $userId): array
    {
        $apiKey = (string) config('services.openai.api_key');

        if ($apiKey === '') {
            throw new RuntimeException('La asistencia de IA todavía no está configurada.');
        }

        $response = Http::baseUrl((string) config('services.openai.base_url'))
            ->withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->connectTimeout(5)
            ->timeout(30)
            ->post('/responses', [
                'model' => config('services.openai.model'),
                'store' => false,
                'safety_identifier' => hash_hmac('sha256', (string) $userId, (string) config('app.key')),
                'instructions' => $this->instructions(),
                'input' => $this->input($prompt, $current),
                'max_output_tokens' => 1200,
                'text' => [
                    'format' => [
                        'type' => 'json_schema',
                        'name' => 'qrbita_vcard_proposal',
                        'strict' => true,
                        'schema' => $this->schema(),
                    ],
                ],
            ]);

        $response->throw();

        $decoded = json_decode($this->extractText($response->json()), true);

        if (! is_array($decoded)) {
            throw new RuntimeException('La IA devolvió una propuesta que no se pudo interpretar.');
        }

        return Validator::make($decoded, [
            'title' => ['required', 'string', 'max:120'],
            'public_name' => ['required', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:120'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'public_email' => ['nullable', 'email:rfc', 'max:255'],
            'website' => ['nullable', 'url:http,https', 'max:2048'],
            'description' => ['nullable', 'string', 'max:280'],
            'links' => ['array', 'max:8'],
            'links.*.type' => ['required', 'in:facebook,instagram,linkedin,whatsapp,tiktok,youtube,x,github'],
            'links.*.label' => ['required', 'string', 'max:80'],
            'links.*.url' => ['required', 'url:http,https', 'max:2048'],
        ])->validate();
    }

    private function instructions(): string
    {
        return <<<'PROMPT'
Eres el asistente de contenido de Qrbita. Convierte la información proporcionada en una sola vCard profesional y breve. Devuelve únicamente datos que quepan en el esquema solicitado. Nunca generes HTML, CSS, JavaScript, Markdown ni instrucciones técnicas. No inventes teléfonos, correos, sitios web, empresas, puestos o redes sociales; utiliza null o un arreglo vacío cuando el usuario no proporcione el dato. Mantén la descripción en un máximo de 280 caracteres. La propuesta será revisada por el usuario antes de guardarse.
PROMPT;
    }

    /**
     * @param  array<string, mixed>  $current
     */
    private function input(string $prompt, array $current): string
    {
        $currentJson = json_encode(
            $current,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
        );

        return "Solicitud del usuario:\n{$prompt}\n\nDatos actuales que deben conservarse salvo que el usuario pida cambiarlos:\n{$currentJson}";
    }

    /**
     * @return array<string, mixed>
     */
    private function schema(): array
    {
        $optionalString = ['type' => ['string', 'null']];

        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => [
                'title',
                'public_name',
                'organization',
                'job_title',
                'phone',
                'public_email',
                'website',
                'description',
                'links',
            ],
            'properties' => [
                'title' => ['type' => 'string', 'maxLength' => 120],
                'public_name' => ['type' => 'string', 'maxLength' => 120],
                'organization' => [...$optionalString, 'maxLength' => 120],
                'job_title' => [...$optionalString, 'maxLength' => 120],
                'phone' => [...$optionalString, 'maxLength' => 40],
                'public_email' => [...$optionalString, 'maxLength' => 255],
                'website' => [...$optionalString, 'maxLength' => 2048],
                'description' => [...$optionalString, 'maxLength' => 280],
                'links' => [
                    'type' => 'array',
                    'maxItems' => 8,
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => ['type', 'label', 'url'],
                        'properties' => [
                            'type' => [
                                'type' => 'string',
                                'enum' => ['facebook', 'instagram', 'linkedin', 'whatsapp', 'tiktok', 'youtube', 'x', 'github'],
                            ],
                            'label' => ['type' => 'string', 'maxLength' => 80],
                            'url' => ['type' => 'string', 'maxLength' => 2048],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function extractText(array $payload): string
    {
        foreach ($payload['output'] ?? [] as $item) {
            foreach ($item['content'] ?? [] as $content) {
                if (($content['type'] ?? null) === 'output_text' && isset($content['text'])) {
                    return (string) $content['text'];
                }
            }
        }

        throw new RuntimeException('La IA no devolvió contenido utilizable.');
    }
}
