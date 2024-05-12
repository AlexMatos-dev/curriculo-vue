<?php

namespace App\Helpers;

use Gumlet\ImageResize;

class FileHandler{
    const ALLOWED_EXTENSIONS = [
        'png',
        'jpeg',
        'gif',
        'pdf'
    ];
    const MAX_FILE_SIZE_MB = 2;

    private $file;
    private $name;
    private $size;
    private $extension;
    private $isValid;
    private $validSize;
    private $validExtension;
    private $tmpPath;
    public function __construct($file = null){
        if(is_string($file) && strlen($file) > 0)
            $file = $this->loadImageWithBase64($file);
        $this->loadFile($file);
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file = null)
    {
        $this->loadFile($file);
    }

    public function setFileByPath($path = '')
    {
        if(!file_exists($path))
            return false;
        $this->loadFile(file_get_contents($path));
    }

    public function loadFile($file = null)
    {
        if(!$file)
            return false;
        try {
            $this->file           = $file;
            $this->name           = $file->getClientOriginalName();
            $this->size           = $file->getSize();
            $this->extension      = str_replace('image/', '', $file->getMimeType());
            $this->isValid        = $this->isFileValid();
            $this->validSize      = $this->isFileSizeValid();
            $this->validExtension = $this->isExtensionValid();
            $this->tmpPath        = $file->getPathName();
        } catch (\Throwable $th) {
            return false;
        }
        return $file;
    }

    public function isFileValid()
    {
        return $this->isFileSizeValid() && $this->isExtensionValid();
    }

    public function isFileSizeValid()
    {
        return $this->parseFileSizeInMb() <= $this::MAX_FILE_SIZE_MB;
    }

    public function isExtensionValid($fileExtension = null)
    {
        $extension = $fileExtension ? $fileExtension : $this->extension;
        return in_array($extension, FileHandler::ALLOWED_EXTENSIONS);
    }

    public function parseFileSizeInMb($fileSize = null)
    {
        $bytes = $fileSize ? $fileSize : $this->size;
        return number_format($bytes / 1048576, 2);
    }

    public function getFileSize($size = null)
    {
        $bytes = $size ? $size : $this->size;
        if ($bytes >= 1073741824){
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }elseif ($bytes >= 1048576){
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }elseif ($bytes >= 1024){
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }elseif ($bytes > 1){
            $bytes = $bytes . ' bytes';
        }elseif ($bytes == 1){
            $bytes = $bytes . ' byte';
        }else{
            $bytes = '0 bytes';
        }
        return $bytes;
    }

    public function getBinary()
    {
        $tmpPath = $this->file->getPathName();
        $binary  = file_get_contents($tmpPath);
        return $binary;
    }

    public function getAsBase64()
    {
        return base64_encode($this->getBinary());
    }

    public function loadImageWithBase64($base64 = '')
    {
        $file = $this->saveFileAtTMP(base64_decode($base64));
        if(!$file)
            return false;
        $this->file = $file['file'];
        $this->size = $file['size'];
        $this->extension = $file['extension'];
        return $this->file;
    }

    public function saveFileAtTMP($source = null, $extension = 'png', $destroyTmp = false)
    {
        $path = storage_path('app/tmp');
        if(!is_dir($path))
            mkdir($path);
        if(!$source)
            return false;
        $fileName = str_replace(['.', '-', '_'], '', microtime(true));
        $filePath = "$path/$fileName.$extension";
        try {
            file_put_contents($filePath, $source);
        } catch (\Throwable $th) {
            return false;
        }
        $file = file_get_contents($filePath);
        $size = filesize($filePath);
        if($destroyTmp){
            unlink($filePath);
        }else{
            $this->tmpPath = $filePath;
        }
        return [
            'file' => $file,
            'size' => $size,
            'extension' => $extension
        ];
    }

    public function getTmpPath()
    {
        return $this->tmpPath; 
    }

    public function destroyFile()
    {
        if(!file_exists($this->getTmpPath()))
            return false;
        unlink($this->getTmpPath());
        return true;
    }

    public function getPhysicalFile()
    {
        if(!$this->getTmpPath() || !file_exists($this->getTmpPath()))
            return '';
        return file_get_contents($this->getTmpPath());
    }

    public function generateImageThumbanil($save = false, $asBase64 = false)
    {
        $image = new ImageResize($this->getTmpPath());
        $image->quality_jpg = 70;
        $image->resize(150, 150, true);
        $image->save($this->getTmpPath());
        return $asBase64 ? base64_encode(file_get_contents($this->tmpPath)) : file_get_contents($this->tmpPath);
    }
}