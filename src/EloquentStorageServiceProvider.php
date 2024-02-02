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
            $fileNameSjisWin = mb_convert_encoding($fileName, 'SJIS-win', 'UTF-8');
            $headers = [
                'Content-Type' => 'application/octet-stream',
                'Content-disposition' => "attachment; filename={$fileNameSjisWin}",
            ];
            return Response::make($content, $status, $headers);
        });

        Response::macro('downloadEloquentStorage', function (EloquentStorage $model, $status = 200, $mimetype = null) {
            $content = $model->getContent();

            $headers = [
                'Content-Type' => $this->identifyFileType(),
            ];

            return Response::make($content, $status, $headers);
        });

        $this->publishes([
            __DIR__ . '/../config/eloquentstorage.php' => config_path('eloquentstorage.php')
        ]);
    }

    private function identifyFileType(string ?$content, $mimetype = null): string
    {
        $default = 'application/octet-stream';
        if ($mimetype == null) {
            $fInfo = new finfo();
            $fInfoResult = $fInfo::buffer($content, FILEINFO_MIME_TYPE);
            return $fInfoResult !== false ? $fInfoResult : $default;
        }

        if (is_callable($mimetype)) {
            return $mimetype($content);
        }

        return $mimetype;
    }

    public function register()
    {
        //
    }
}
