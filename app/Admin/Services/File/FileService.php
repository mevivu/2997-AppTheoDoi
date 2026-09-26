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
     * Upload video lên Cloudflare R2
     *
     * @param UploadedFile $file File video cần upload
     * @param string $folder Thư mục trên R2 (mặc định: videos/raw)
     * @param string|null $oldPath Đường dẫn tương đối hoặc URL cũ để xóa nếu có
     * @return array ['path' => 'videos/raw/...', 'url' => 'https://pub-xxx.r2.dev/videos/raw/...']
     */
    public function uploadVideoToR2(UploadedFile $file, string $folder = 'videos/raw', ?string $oldPath = null): array
    {
        if ($oldPath) {
            $this->deleteR2File($oldPath);
        }

        $folder = trim($folder, '/');
        $hashName = $file->hashName();
        $relativePath = $folder . '/' . $hashName;

        // Lưu trực tiếp vào disk r2
        Storage::disk('r2')->putFileAs($folder, $file, $hashName);

        $baseUrl = config('filesystems.disks.r2.url') ?: env('CLOUDFLARE_R2_PUBLIC_URL', '');
        $publicUrl = rtrim($baseUrl, '/') . '/' . ltrim($relativePath, '/');

        return [
            'path' => $relativePath,
            'url' => $publicUrl,
        ];
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
