<?php

function getFileUrl($file,$directory)
{
    $extendion = $file->getClientOriginalExtension();
    $imageName = rand(100000,5000000).'.'.$extendion;
    $file->move($directory,$imageName);
    $fileUrl  = $directory.$imageName;
    return $fileUrl;
}
function deleteFile($imageUrl)
{
    if (file_exists($imageUrl))
    {
        unlink($imageUrl);
    }
}

