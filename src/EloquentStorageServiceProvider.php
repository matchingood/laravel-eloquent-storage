<?php

namespace MatchinGood\EloquentStorage;

use Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class EloquentStorageServiceProvider extends ServiceProvider
{
    const DISPOSITION_TYPE_ATTACHMENT = 'attachment';
    const DISPOSITION_TYPE_INLINE = 'inline';

    public function boot()
    {
        Response::macro('downloadEloquentStorage', function (EloquentStorage $model, $status = 200) {
            return EloquentStorageServiceProvider::createFileDownloadRequest($model, $status);
        });

        Response::macro('openEloquentStorage', function (EloquentStorage $model, $status = 200, $mimeType = null) {
            return EloquentStorageServiceProvider::createFileOpenRequest($model, $status, $mimeType);
        });

        $this->publishes([
            __DIR__ . '/../config/eloquentstorage.php' => config_path('eloquentstorage.php')
        ]);
    }

    public static function createFileDownloadRequest(EloquentStorage $model, $status = 200)
    {
        try {
            $content = $model->getContent();
        } catch (FileNotFoundException $e) {
            abort(404);
        }
        $fileName = $model->file_name;
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => self::formatContentDisposition(self::DISPOSITION_TYPE_ATTACHMENT, $fileName),
        ];
        return Response::make($content, $status, $headers);
    }

    public static function createFileOpenRequest(EloquentStorage $model, $status = 200, $mimeType = null)
    {
        $content = $model->getContent();
        $headers = [
            'Content-Type' => self::identifyFileType($content, $mimeType),
        ];

        return Response::make($content, $status, $headers);
    }

    private static function identifyFileType($content, $mimeType = null): string
    {
        $default = 'application/octet-stream';
        if ($mimeType == null) {
            $fInfo = new \finfo();
            $fInfoResult = $fInfo->buffer($content, FILEINFO_MIME_TYPE);
            return $fInfoResult !== false ? $fInfoResult : $default;
        }

        if (is_callable($mimeType)) {
            return $mimeType($content);
        }

        return $mimeType;
    }

    private static function formatContentDisposition($dispositionType, $filename)
    {
        $filenameSjisWin = mb_convert_encoding($filename, 'SJIS-win', 'UTF-8');
        return "{$dispositionType}; filename={$filenameSjisWin}";
    }
}
