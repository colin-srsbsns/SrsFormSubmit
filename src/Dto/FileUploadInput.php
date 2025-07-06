<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO used by the PUT /api/form-submissions/{id}/file endpoint
 * or the POST /api/form-submission_files endpoint, depending on how
 * the APIResource is configured.
 */
class FileUploadInput
{
    /**
     * The file being uploaded.
     */
    #[Assert\NotNull]
    #[Assert\File(maxSize: '50M')]
    public UploadedFile $file;
}
