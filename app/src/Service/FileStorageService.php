<?php declare(strict_types = 1);

namespace App\Service;

class FileStorageService
{
    public function read($file): string
    {
        $request = file_get_contents('request.json');

        $storageService = new StorageService($request);

        return $storageService->getRequest();
    }
}
