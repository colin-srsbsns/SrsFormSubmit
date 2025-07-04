<?php

// src/Message/ContactSubmissionMessage.php
namespace App\Message;

readonly class FormSubmissionMessage
{
    public function __construct(
        public int    $submissionId,
        public string $siteKey
    ) {}
}
