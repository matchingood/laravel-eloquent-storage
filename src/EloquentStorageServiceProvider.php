<?php

namespace MatchinGood\EloquentStorage;

use Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class EloquentStorageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Response::macro('downloadEloquentStorage', function (EloquentStorage $model, $status = 200) {
            try {
                $content = $model->getContent();
            } catch (FileNotFoundException $e) {
                abort(404);
            }
            $fileName = $model->file_name;
            $headers = [
                'Content-Type' => 'application/octet-stream',
                'Content-disposition' => "attachment; filename={$fileName}"
            ];
            return Response::make($content, $status, $headers);
        });

        $this->publishes([
            __DIR__ . '/../config/eloquentstorage.php' => config_path('eloquentstorage.php')
        ]);
    }

    public function register()
    {
        //
    }
}
