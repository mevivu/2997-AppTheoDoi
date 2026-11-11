<?php

namespace App\Admin\Services\File;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class FileService
{
    private string $disk = 'uploads';

    private string $folder = '/';

    private string $folderPrefix = 'public/uploads/';

    private $file;

    private $instance;

    private bool $status = true;

    public function setDisk($disk): static
    {
        $this->disk = $disk;
        return $this;
    }

    public function setFolder($folder): static
    {
        $this->folder = Str::finish($folder, '/');
        return $this;
    }

    public function setFolderForUser($path = '/'): FileService|static
    {
        $path = $path == '/' ? '/' : '/' . Str::finish($path, '/');
        return $this->setFolder('users/' . auth()->user()->id . $path);
    }

    public function setFolderPrefix($folderPrefix): static
    {
        $this->folderPrefix = Str::finish($folderPrefix, '/');
        return $this;
    }

    public function setFile($file): static
    {
        $this->file = $file;
        return $this;
    }

    public function upload(): static
    {
        $path = $this->file->storeAs($this->folder, $this->file->hashName(), $this->disk);
        $this->instance = preg_replace('#/+#', '/', Str::finish($this->folderPrefix, '/') . ltrim($path, '/'));
        return $this;
    }

    /**
     * @throws Exception
     */
    public function uploadFilepondEncode(): FileService|static
    {
        $file = json_decode($this->file, true);

        return $this->uploadFileBase64($file);
    }

    public function uploadCheckFilepondEncode($fileExists): FileService|static
    {
        $file = json_decode($this->file, true);
        if (array_key_exists($file['id'], $fileExists)) {
            $this->instance = Str::after($fileExists[$file['id']], url('/'));
            return $this;
        }
        return $this->uploadFileBase64($file);

    }

    /**
     * @throws Exception
     */
    private function uploadFileBase64($file): static
    {
        $fileContent = base64_decode($file['data']);

        $pathFile = $this->folder . uniqid_real() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);

        Storage::disk($this->disk)->put($pathFile, $fileContent);

        $this->instance = $this->folderPrefix . $pathFile;
        return $this;
    }

    public function move($pathFile, $newPath): static
    {
        $newPath = $newPath . basename($pathFile);
        Storage::disk($this->disk)->move($pathFile, $newPath . basename($pathFile));
        $this->instance = $newPath;
        return $this;
    }

    public function delete($pathFile): static
    {
        if ($pathFile != null && $pathFile != '') {
            Storage::disk($this->disk)->delete(Str::after($pathFile, $this->folderPrefix));
        }
        return $this;
    }

    public function deleteSimpleFiles(array $files): static
    {

        $files = array_map(function ($value) {
            $value = Str::after(Str::after($value, url('/')), 'public/uploads/');
            return $value;

        }, $files);

        $files = array_filter($files, function ($value) {
            return !Str::startsWith($value, 'files/');
        });

        Storage::disk($this->disk)->delete(array_values($files));
        return $this;
    }

    public function getInstance()
    {
        return $this->instance;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }


    /**
     * Upload a new avatar and replace the old one if it exists.
     *
     * @param string $folder Folder to store the avatar.
     * @param UploadedFile $newFile New avatar file.
     * @param string|null $currentAvatarPath Path to the current avatar to be replaced.
     * @return string New avatar path.
     */
    public function uploadAvatar(string $folder, UploadedFile $newFile, ?string $currentAvatarPath = null): string
    {
        // Set the storage folder
        $this->setFolder($folder);

        // Delete the existing avatar if it exists
        if ($currentAvatarPath) {
            $this->delete($currentAvatarPath);
        }

        // Upload the new file
        $path = $newFile->storeAs($this->folder, $newFile->hashName(), $this->disk);
        $this->instance = $this->folderPrefix . $path;

        return $this->instance;
    }

    /**
     * Uploads images for the specified fields and deletes old images from related models if applicable.
     *
     * @param string $folder The directory where images will be uploaded.
     * @param array $data The data array containing file information for uploading.
     * @param array $imageFields The fields within $data that need to be processed for image upload.
     * @param Model|null $model The model instance that may contain old file paths for deletion.
     * @param array $relationFields The relationships and their specific fields that need old images deleted.
     * @return array The updated $data array with new image paths.
     */
    public function uploadImages(string $folder, array $data, array $imageFields, Model $model = null, array $relationFields = []): array
    {
        foreach ($imageFields as $field) {
            if (isset($data[$field])) {
                $currentPath = $model && isset($model[$field]) ? $model[$field] : null;
                $data[$field] = $this->uploadAvatar($folder, $data[$field], $currentPath);
            }
        }
        foreach ($relationFields as $relation => $fields) {
            if ($model && isset($model->$relation)) {
                foreach ($fields as $field) {
                    if (isset($model->$relation->$field)) {
                        $this->delete($model->$relation->$field);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Deletes images associated with model attributes.
     *
     * @param Model $model The model from which images will be deleted.
     * @param array $attributes List of model attributes that contain image paths to delete.
     * @return $this
     */
    public function deleteModelImages(Model $model, array $attributes): static
    {
        foreach ($attributes as $attribute) {
            $imageData = $model[$attribute];

            if (is_string($imageData) && Str::startsWith($imageData, '[')) {
                $paths = json_decode($imageData, true);
                if (is_array($paths)) {
                    foreach ($paths as $path) {
                        $this->delete($path);
                    }
                }
            } else {
                $this->delete($imageData);
            }
        }

        return $this;
    }

    /**
     * Khởi tạo S3Client chuẩn cho Cloudflare R2 (tắt Expect: 100-continue, ép HTTP/1.1 & tự động retry)
     */
    public function getR2S3Client(): \Aws\S3\S3Client
    {
        $r2Config = config('filesystems.disks.r2', []);

        return new \Aws\S3\S3Client([
            'version' => 'latest',
            'region' => $r2Config['region'] ?? 'auto',
            'endpoint' => $r2Config['endpoint'] ?? env('CLOUDFLARE_R2_ENDPOINT'),
            'use_path_style_endpoint' => $r2Config['use_path_style_endpoint'] ?? true,
            'credentials' => [
                'key' => $r2Config['key'] ?? env('CLOUDFLARE_R2_ACCESS_KEY_ID'),
                'secret' => $r2Config['secret'] ?? env('CLOUDFLARE_R2_SECRET_ACCESS_KEY'),
            ],
            'retries' => [
                'mode' => 'standard',
                'max_attempts' => 5,
            ],
            'http' => [
                'verify' => false,
                'expect' => false,
                'connect_timeout' => 30,
                'timeout' => 300,
                'curl' => defined('CURLOPT_HTTP_VERSION') ? [
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    CURLOPT_TCP_KEEPALIVE => 1,
                ] : [],
            ],
        ]);
    }

    /**
     * Lấy tên bucket Cloudflare R2
     */
    public function getR2Bucket(): string
    {
        return (string) (config('filesystems.disks.r2.bucket') ?: env('CLOUDFLARE_R2_BUCKET', ''));
    }

    /**
     * Tạo Public URL từ đường dẫn tương đối trên R2
     */
    public function getR2PublicUrl(string $relativePath): string
    {
        $baseUrl = config('filesystems.disks.r2.url') ?: env('CLOUDFLARE_R2_PUBLIC_URL', '');
        return rtrim($baseUrl, '/') . '/' . ltrim($relativePath, '/');
    }

    /**
     * Upload file (ảnh học bạ, tài liệu, media) lên Cloudflare R2
     *
     * @param UploadedFile $file File cần upload
     * @param string $folder Thư mục trên R2 (mặc định: uploads)
     * @param string|null $oldPath Đường dẫn tương đối hoặc URL cũ để xóa nếu có
     * @param string|null $customFilename Tùy chọn đặt tên file (nếu null sẽ dùng hashName)
     * @return array ['path' => 'folder/...', 'url' => 'https://pub-xxx.r2.dev/folder/...']
     */
    public function uploadFileToR2(UploadedFile $file, string $folder = 'uploads', ?string $oldPath = null, ?string $customFilename = null): array
    {
        $folder = trim($folder, '/');
        $filename = $customFilename ?: $file->hashName();
        $relativePath = $folder . '/' . $filename;
        $mimeType = $file->getMimeType() ?: 'application/octet-stream';

        // Sử dụng tuần tự (concurrency = 1) và part_size = 8MB để tránh lỗi cURL error 56 SSL_read reset trên R2
        Storage::disk('r2')->putFileAs($folder, $file, $filename, [
            'concurrency' => 1,
            'mup_threshold' => 52428800, // 50MB
            'part_size' => 8388608,      // 8MB
            'ContentType' => $mimeType,
        ]);

        if ($oldPath && $oldPath !== $relativePath) {
            $this->deleteR2File($oldPath);
        }

        return [
            'path' => $relativePath,
            'url' => $this->getR2PublicUrl($relativePath),
        ];
    }

    /**
     * Upload video lên Cloudflare R2
     *
     * @param UploadedFile $file File video cần upload
     * @param string $folder Thư mục trên R2 (mặc định: videos/raw)
     * @param string|null $oldPath Đường dẫn tương đối hoặc URL cũ để xóa nếu có
     * @return array ['path' => 'videos/raw/...', 'url' => 'https://pub-xxx.r2.dev/videos/raw/...']
     */
    public function uploadVideoToR2(UploadedFile $file, string $folder = 'videos/raw', ?string $oldPath = null): array
    {
        return $this->uploadFileToR2($file, $folder, $oldPath);
    }

    /**
     * Khởi tạo phiên Multipart Upload (Chunked Upload) trên Cloudflare R2 cho video dung lượng lớn
     */
    public function initMultipartUploadToR2(string $originalFilename, string $folder = 'videos/lessons', ?string $mimeType = 'video/mp4'): array
    {
        $folder = trim($folder, '/');
        $ext = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION) ?: 'mp4');
        $allowedExts = ['mp4', 'mov', 'webm', 'mkv', 'avi', 'm4v'];
        if (!in_array($ext, $allowedExts, true)) {
            $ext = 'mp4';
        }

        $filename = Str::random(40) . '.' . $ext;
        $key = $folder . '/' . $filename;

        $client = $this->getR2S3Client();
        $result = $client->createMultipartUpload([
            'Bucket' => $this->getR2Bucket(),
            'Key' => $key,
            'ContentType' => $mimeType ?: 'video/mp4',
        ]);

        return [
            'upload_id' => (string) $result['UploadId'],
            'key' => $key,
            'url' => $this->getR2PublicUrl($key),
        ];
    }

    /**
     * Tải lên 1 phần (Part / Chunk >= 5MB, trừ part cuối) lên Cloudflare R2 với cơ chế tự động thử lại 3 lần
     */
    public function uploadPartToR2(string $key, string $uploadId, int $partNumber, UploadedFile $chunkFile): array
    {
        $client = $this->getR2S3Client();
        $bucket = $this->getR2Bucket();
        $realPath = $chunkFile->getRealPath();

        $maxRetries = 3;
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $stream = null;
            try {
                $stream = fopen($realPath, 'rb');
                $result = $client->uploadPart([
                    'Bucket' => $bucket,
                    'Key' => $key,
                    'UploadId' => $uploadId,
                    'PartNumber' => $partNumber,
                    'Body' => $stream,
                    'ContentLength' => filesize($realPath),
                ]);

                if (is_resource($stream)) {
                    fclose($stream);
                }

                $etag = (string) ($result['ETag'] ?? '');

                return [
                    'part_number' => $partNumber,
                    'etag' => trim($etag, '"'),
                ];
            } catch (Throwable $e) {
                if (is_resource($stream)) {
                    fclose($stream);
                }
                $lastException = $e;
                Log::warning("[FileService::uploadPartToR2] Part {$partNumber} attempt {$attempt}/{$maxRetries} failed: " . $e->getMessage());
                if ($attempt < $maxRetries) {
                    usleep(400000 * $attempt); // 0.4s, 0.8s
                }
            }
        }

        throw $lastException;
    }

    /**
     * Hoàn tất Multipart Upload trên Cloudflare R2
     */
    public function completeMultipartUploadToR2(string $key, string $uploadId, array $parts, ?string $oldPath = null): array
    {
        $normalizedParts = [];
        foreach ($parts as $part) {
            $partNum = (int) ($part['PartNumber'] ?? $part['part_number'] ?? 0);
            $etag = trim((string) ($part['ETag'] ?? $part['etag'] ?? ''), '"');
            if ($partNum > 0 && $etag !== '') {
                $normalizedParts[$partNum] = [
                    'PartNumber' => $partNum,
                    'ETag' => '"' . $etag . '"',
                ];
            }
        }

        ksort($normalizedParts);

        $client = $this->getR2S3Client();
        $client->completeMultipartUpload([
            'Bucket' => $this->getR2Bucket(),
            'Key' => $key,
            'UploadId' => $uploadId,
            'MultipartUpload' => [
                'Parts' => array_values($normalizedParts),
            ],
        ]);

        if ($oldPath && $oldPath !== $key) {
            $this->deleteR2File($oldPath);
        }

        return [
            'path' => $key,
            'url' => $this->getR2PublicUrl($key),
        ];
    }

    /**
     * Hủy phiên Multipart Upload trên Cloudflare R2 khi hủy hoặc lỗi
     */
    public function abortMultipartUploadToR2(string $key, string $uploadId): bool
    {
        try {
            $client = $this->getR2S3Client();
            $client->abortMultipartUpload([
                'Bucket' => $this->getR2Bucket(),
                'Key' => $key,
                'UploadId' => $uploadId,
            ]);
            return true;
        } catch (Throwable $e) {
            Log::warning('[FileService::abortMultipartUploadToR2] Failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa file trên Cloudflare R2 an toàn
     *
     * @param string|null $path Hoặc relative path ('videos/raw/xxx.mp4') hoặc full public URL
     * @return bool
     */
    public function deleteR2File(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        try {
            $relativePath = $path;
            $r2PublicUrl = rtrim(config('filesystems.disks.r2.url') ?: env('CLOUDFLARE_R2_PUBLIC_URL', ''), '/');

            if ($r2PublicUrl && Str::startsWith($path, $r2PublicUrl)) {
                $relativePath = ltrim(substr($path, strlen($r2PublicUrl)), '/');
            } elseif (Str::contains($path, ['.r2.dev', '.r2.cloudflarestorage.com'])) {
                $parsed = parse_url($path, PHP_URL_PATH);
                if ($parsed) {
                    $relativePath = ltrim($parsed, '/');
                }
            }

            if (Storage::disk('r2')->exists($relativePath)) {
                return Storage::disk('r2')->delete($relativePath);
            }
        } catch (Throwable $e) {
            Log::warning('[FileService::deleteR2File] Failed: ' . $e->getMessage());
        }

        return false;
    }
}
