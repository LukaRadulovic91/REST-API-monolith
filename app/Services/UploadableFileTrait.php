<?php

namespace App\Services;

use App\Enums\MediaType;
use App\Models\CandidateMedia;
use App\Models\Media;
use File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DB;
use Illuminate\Http\JsonResponse;
use function Illuminate\Events\queueable;

/**
 * Trait UploadableFileTrait
 *
 * @package App\Services
 */
trait UploadableFileTrait
{
    protected $folder = 'uploads/';

    /**
     * paginate number
     *
     * @var array
     */
    protected $_relations = [
        'Post' => 'candidateMedia',
    ];

    /**
     * @param $fieldName
     * @param $attributeName
     *
     * @return void
     */
    public function uploadCVFile($fieldName = 'cv_path', $attributeName = 'file'): void
    {
        $this->storeFilesWithRelation('cv_path', MediaType::CV, 'candidateMedia');
    }

    /**
     * @param $file_name
     * @param $attributeName
     *
     * @return void
     */
    public function uploadCertificates($file_name = 'certificates', $attributeName = 'file')
    {
        $this->storeFilesWithRelation('certificates', MediaType::CERTIFICATE, 'candidateMedia');
    }

    /**
     * Store multiple files with relation to candidate.
     *
     * @param string $fieldName
     * @param string $type
     * @param string $relation
     *
     * @return string
     */
    public function storeFilesWithRelation($fieldName, $type, $relation = 'candidateMedia'): string
    {
        $files = request()->file($fieldName);
        if ($this->$fieldName) File::delete($this->$fieldName);

        if ($files !== false && count($files) > 0) {
            $mediaData = [];
            foreach ($files as $file) {
                $mediaData[] = $this->storeSingleFile($file, $type);
            }

            CandidateMedia::insert($this->prepareRelationData($mediaData, $type));

            return 'done';
        } else {
            return 'No files provided';
        }
    }

    /**
     * Store a single file and return media data.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $type
     *
     * @return array
     */
    private function storeSingleFile($file, $type)
    {
        $className = (new \ReflectionClass($this))->getShortName();
        $folderPath = $this->getFolderName($this->folder . Str::lower(Str::plural($className, 2)) . '/');;

        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs($folderPath, $this->getFileName($type, $file), 'public');


        $fileMimeType = $file->getMimeType();

        return Media::create([
            'name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => $fileMimeType,
            'disk' => 'public',
            'size' => Storage::disk('public')->size($filePath),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Prepare relation data for media.
     *
     * @param array $mediaData
     * @param string $type
     *
     * @return array
     */
    private function prepareRelationData(array $mediaData, string $type): array
    {
        $relationData = [];
        foreach ($mediaData as $media) {
            $relationData[] = [
                'candidate_id' => $this->id,
                'media_id' => $media['id'],
                'type' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $relationData;
    }


    /**
     * @param $className
     *
     * @return string
     */
    protected function getFolderPath($className): string
    {
        return $this->folder . Str::lower(Str::plural($className, 2)) . '/';
    }

    /**
     * @param $file
     *
     * @return string
     */
    protected function getFileName($type, $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return MediaType::getKey($type) .'-'.Str::slug($this->title ?? $this->name ?? $this->id) . '-' . Str::random(2) . '.' . $extension;
    }

    /**
     * method used to return folder name related to current month and year
     *
     * @param $path
     * @return string
     */
    protected function getFolderName($path)
    {
        return $path . now()->format('m-Y');
    }

}
