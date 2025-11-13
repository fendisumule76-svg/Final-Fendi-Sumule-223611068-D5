<?php
namespace Src\Controllers;

use Src\Helpers\Response;

class UploadController extends BaseController
{
    public function store()
    {
        // Pastikan bukan JSON
        if ((($_SERVER['CONTENT_TYPE'] ?? '') && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'))) {
            return $this->error(415, 'Use multipart/form-data for upload');
        }

        if (empty($_FILES['file'])) return $this->error(422, 'file is required');

        $f = $_FILES['file'];
        if ($f['error'] !== UPLOAD_ERR_OK) return $this->error(400, 'Upload error');
        if ($f['size'] > 2 * 1024 * 1024) return $this->error(422, 'Max 2MB');

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($f['tmp_name']);

        $allowed = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'application/pdf' => 'pdf'
        ];

        if (!isset($allowed[$mime])) return $this->error(422, 'Invalid mime');

        // Buat nama unik
        $name = bin2hex(random_bytes(8)) . '.' . $allowed[$mime];

        // Folder tujuan: public/uploads
        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // buat folder kalau belum ada
        }

        $dest = $uploadDir . $name;

        // Pindahkan file
        if (!move_uploaded_file($f['tmp_name'], $dest)) {
            return $this->error(500, 'Save failed');
        }

        // Kirim respon sukses
        return $this->ok([
            'message' => 'Upload success',
            'path' => "/uploads/$name"
        ], 201);
    }
}