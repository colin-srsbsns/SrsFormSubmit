<?php

namespace App\Dto;

class FormSubmissionInput
{
    /** @var array<string, mixed> */
    public array $payload = [];

    public string $siteKey = '';

    public ?string $recaptcha = null;   // optional, if you pass it
}
