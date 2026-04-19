<?php

/**
 * Class FileUploader
 */
class FileUploader
{
    /**
     * @return mixed
     */
    public static function uploadFile()
    {
        $target_dir = "uploads/email-attachments/";
        $filename = rand() . $_FILES["attachment"]["name"];
        $target_file = $target_dir . basename($filename);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["attachment"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                $uploadOk = 0;
            }
        }

        if (file_exists($target_file)) {
            $uploadOk = 0;
        }

        if ($_FILES["attachment"]["size"] > 500000) {
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            return false;
        } else {
            if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
                return $filename;
            }
        }

        return false;
    }
}
