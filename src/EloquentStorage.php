<?php

namespace MatchinGood\EloquentStorage;

use Storage;
use File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class EloquentStorage extends Model
{
    /**
     * get content. when FileNotFoundException
     * is catched, it will return null
     * @return string|null
     */
    public function getContent()
    {
        return $this->getDisk()->get($this->getFilePath());
    }

    /**
     * save content with saving database
     * and return true when this procudure succeeds
     * @param string $name
     * @param string $content
     * @return boolean
     */
    public function saveContent($name, $content, $dir = null)
    {
        if (!$this->exists) {
            $this->unique_file_name = uniqid(rand());
        }
        $this->file_name = $name;
        $this->directory = isset($dir) ? $dir : '';
        if ($this->getDisk()->put($this->getFilePath(), $content) === false) {
            return false;
        }
        return $this->save();
    }

    /**
     * get file size
     * @return int
     */
    public function getFileSize()
    {
        return $this->getDisk()->size($this->getFilePath());
    }

    /*
     * save model by using uploaded file
     * @param UploadedFile $file
     * @param string $dir
     * @return boolean
     */
    public function saveFromUploadedFile(UploadedFile $file, $dir = null)
    {
        $fileName = $file->getClientOriginalName();
        $content = File::get($file->getPathname());
        return $this->saveContent($fileName, $content, $dir);
    }

    /**
     * override delete method
     * @return bool|null
     */
    public function delete()
    {
        $isDeleted = parent::delete();
        if ($isDeleted) {
            $this->deleteFile();
        }
        return $isDeleted;
    }

    /**
     * delete file and return true when this procedure succeeds
     * @return boolean
     */
    public function deleteFile()
    {
        return $this->getDisk()->delete($this->getFilePath());
    }

    private function getDisk()
    {
        return Storage::disk(config('eloquentstorage.driver'));
    }

    protected function getFilePath()
    {
        $directory = $this->getTable();
        $rootDir = $this->directory;
        if (isset($rootDir) && $rootDir != '') {
            $directory = "{$rootDir}/{$directory}";
        }
        return "{$directory}/{$this->unique_file_name}";
    }
}

