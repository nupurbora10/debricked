<?php

namespace App\MessageHandler;

use App\Entity\FileUpload;
use App\Entity\ScanResult;
use App\Message\ProcessUploadedFiles;
use App\Repository\FileUploadRepository;
use App\Service\DebrickedApiService;
use App\Service\RuleEngine;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ProcessUploadedFilesHandler implements MessageHandlerInterface
{
    private $fileUploadRepository;
    private $debrickedApiService;
    private $entityManager;
    private $ruleEngine;
    private $logger;

    public function __construct(
        FileUploadRepository $fileUploadRepository,
        DebrickedApiService $debrickedApiService,
        EntityManagerInterface $entityManager,
        RuleEngine $ruleEngine,
        LoggerInterface $logger
    ) {
        $this->fileUploadRepository = $fileUploadRepository;
        $this->debrickedApiService = $debrickedApiService;
        $this->entityManager = $entityManager;
        $this->ruleEngine = $ruleEngine;
        $this->logger = $logger;
    }

    public function __invoke(ProcessUploadedFiles $message)
    {
        $this->logger->info('Processing file upload with ID: ' . $message->getFileUploadId());

        $fileUpload = $this->fileUploadRepository->find($message->getFileUploadId());

        if (!$fileUpload) {
            $this->logger->error('FileUpload not found for ID: ' . $message->getFileUploadId());
            return;
        }

        try {
            $scanResultData = $this->debrickedApiService->uploadAndScanFile($fileUpload->getFilePath());

            $this->logger->info('Scan result data: ' . print_r($scanResultData, true));

            if (empty($scanResultData)) {
                throw new \RuntimeException('Empty scan result data');
            }

            $fileUpload->setStatus('completed');

            $scanResultEntity = new ScanResult();
            $scanResultEntity->setFileUpload($fileUpload);
            $scanResultEntity->setVulnerabilitiesCount(0);
            $scanResultEntity->setStatus('completed');
            $scanResultEntity->setScannedAt(new \DateTime());

            $this->entityManager->persist($scanResultEntity);
            $this->entityManager->flush();

            $this->ruleEngine->evaluateRules($scanResultEntity);

            $this->logger->info('File processing completed successfully.');
        } catch (\Exception $e) {
            $fileUpload->setStatus('failed');
            $this->entityManager->flush();
            $this->logger->error('Error processing file: ' . $e->getMessage());
        }
    }
}
