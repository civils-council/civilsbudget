<?php

namespace AppBundle\Helper;

use AppBundle\Exception\FileUploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    const MB100 = 104857600;
    const MB5 = 9242880;
    
    private $pathUploadPhoto = 'uploads/photo/';

    /**
     * @var ImageWorker
     */
    private $imageWorker;

    /**
     * @var
     */
    private $rootDir;

    /**
     * FileUploader constructor.
     * @param ImageWorker $imageWorker
     * @param $kernelRootDir
     */
    public function __construct(
        ImageWorker $imageWorker, 
        $kernelRootDir
    ) {
        $this->imageWorker = $imageWorker;
        $this->rootDir = $kernelRootDir;
    }

    /**
     * @param string $text
     * @return string
     */
    public static function ukr_rusToTranslit($text)
    {
        $trans_arr = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'yo', 'ж' => 'j', 'з' => 'z', 'и' => 'y',
            'й' => 'i', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'y', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sh', 'ы' => 'i', 'э' => 'e', 'ю' => 'u',
            'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
            'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'J', 'З' => 'Z', 'И' => 'Y',
            'Й' => 'I', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
            'У' => 'Y', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'Ch',
            'Ш' => 'Sh', 'Щ' => 'Sh', 'Ы' => 'I', 'Э' => 'E', 'Ю' => 'U',
            'Я' => 'Ya',
            'ь' => '', 'Ь' => '', 'ъ' => '', 'Ъ' => '',
            'ї' => 'i', 'і' => 'i', 'ґ' => 'g', 'є' => 'ye',
            'Ї' => 'I', 'І' => 'I', 'Ґ' => 'G', 'Є' => 'YE',
            ' ' => '_', '—' => '-', '+' => '_',
        );

        return strtr($text, $trans_arr);
    }

    /**
     * @param UploadedFile $file
     * @return void|FileUploadException
     */
    public function validFile($file)
    {
        if ($file->getClientSize() > $this::MB5) {
            throw new FileUploadException('You select a file that exceeds the size limit.');
        }
    }

    private function guessMimeType($extension)
    {
        $mimeTypes = array(
            // images
            'png' => 'image/png',            
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
        );

        if (!in_array($extension, $mimeTypes)) {
            throw new FileUploadException('access denied for this ' . $extension);
        }
    }

    /**
     * @param UploadedFile $file
     * @return array|mixed
     */
    public function uploadImage($file)
    {
        $this->validFile($file);
        $this->guessMimeType($file->getClientMimeType());
        $path = $this->pathUploadPhoto;
        $name = date('Ymdhis').'_'.$this::ukr_rusToTranslit($file->getClientOriginalName());
        $pathUrl = $path.$name;
        $fullPathFile = $this->rootDir.'/../web/'.$pathUrl;
        $fullPathDir = $this->rootDir.'/../web/'.$path;

        if ($file->move($fullPathDir, $name)) {
            $image = $this->imageWorker;
            $image->load($fullPathFile);
            $image->cropMiddle(273, 273);

            $image->save(
                $fullPathFile,
                $image->getType($fullPathFile),
                95,
                0777
            );

            return $fullPathFile;
        } else {
            throw new FileUploadException('Could not file files for uploading.');
        }
    }
}
