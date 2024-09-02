<?php

namespace App\Message;

class ProcessUploadedFiles
{
    private $fileUploadId;

    public function __construct(int $fileUploadId)
    {
        $this->fileUploadId = $fileUploadId;
    }

    public function getFileUploadId(): int
    {
        return $this->fileUploadId;
    }
}
