<?php

namespace App\Services;

use App\Models\VCard;

class VCardFileService
{
    public function make(VCard $vCard): string
    {
        $lines = [
            'BEGIN:VCARD',
            'VERSION:3.0',
            'FN:'.$this->escape($vCard->public_name),
        ];

        $this->append($lines, 'ORG', $vCard->organization);
        $this->append($lines, 'TITLE', $vCard->job_title);
        $this->append($lines, 'TEL;TYPE=CELL', $vCard->phone);
        $this->append($lines, 'EMAIL;TYPE=INTERNET', $vCard->public_email);
        $this->append($lines, 'URL', $vCard->website);
        $this->append($lines, 'NOTE', $vCard->description);

        $lines[] = 'REV:'.now()->utc()->format('Ymd\THis\Z');
        $lines[] = 'END:VCARD';

        return implode("\r\n", $lines)."\r\n";
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function append(array &$lines, string $property, ?string $value): void
    {
        if (filled($value)) {
            $lines[] = $property.':'.$this->escape((string) $value);
        }
    }

    private function escape(string $value): string
    {
        return str_replace(
            ['\\', "\r\n", "\n", ';', ','],
            ['\\\\', '\\n', '\\n', '\\;', '\\,'],
            $value,
        );
    }
}
