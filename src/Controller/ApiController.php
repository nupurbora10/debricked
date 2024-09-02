<?php

// src/Controller/ApiController.php
namespace App\Controller;

use App\Entity\FileUpload;
use App\Message\ProcessUploadedFiles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private $entityManager;
    private $messageBus;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus)
    {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
    }

    #[Route('/api/upload', name: 'api_upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $uploadedFiles = $request->files->get('files');

        // If single file uploaded, wrap it in an array
        if ($uploadedFiles instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
            $uploadedFiles = [$uploadedFiles];
        }

        if (!$uploadedFiles || !is_array($uploadedFiles)) {
            return new JsonResponse(['error' => 'No files uploaded'], Response::HTTP_BAD_REQUEST);
        }

        foreach ($uploadedFiles as $uploadedFile) {
            if (!$uploadedFile instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                continue; // Skip if it's not a valid file
            }

            $fileName = $uploadedFile->getClientOriginalName();
            $filePath = $this->getParameter('kernel.project_dir') . '/uploads/' . $fileName;

            try {
                $uploadedFile->move($this->getParameter('kernel.project_dir') . '/uploads/', $fileName);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'File upload failed: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $fileUpload = new FileUpload();
            $fileUpload->setFileName($fileName);
            $fileUpload->setFilePath($filePath);
            $fileUpload->setStatus('pending');
            $fileUpload->setUploadedAt(new \DateTime());

            $this->entityManager->persist($fileUpload);
            $this->entityManager->flush();

            // Dispatch a message to process the file
            $this->messageBus->dispatch(new ProcessUploadedFiles($fileUpload->getId()));
        }

        return new JsonResponse(['status' => 'Files uploaded successfully']);
    }
}
