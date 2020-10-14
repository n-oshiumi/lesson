<?php

class FileController
{
    public static function fileUpload($file, $path, $savedPath)
    {
        $randomStr = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 16);
        $extension = '.' . substr(strrchr($file['name'], '.'), 1);
        $fileName = $randomStr . $extension;

        move_uploaded_file($file['tmp_name'], $path . $fileName);//imagesディレクトリにファイル保存

        return $savedPath . $fileName;
    }
}
