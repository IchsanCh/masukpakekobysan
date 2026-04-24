<?php

namespace App\Providers;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;

class GoogleDriveServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Storage::extend('google', function ($app, $config) {
            $client = new Client();
            $client->setApplicationName('MasukPakEko');

            // Use Service Account credentials
            $client->setAuthConfig($config['service_account_json']);
            $client->addScope(Drive::DRIVE);

            $service = new Drive($client);

            $options = [];

            if (! empty($config['shared_folder_id'])) {
                $options['sharedFolderId'] = $config['shared_folder_id'];
            }

            $adapter = new GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
            $driver = new Filesystem($adapter);

            return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
        });
    }
}
