<?php

namespace App\Services;

use DB;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Trait UploudableImageTrait
 *
 * @package App\Services
 */
trait UploudableImageTrait
{
    /**
     * upload folder
     *
     * @var string
     */
    protected $folder = 'uploads/';

    /**
     * paginate number
     *
     * @var array
     */
    protected $_relations = [
        'Post' => 'gallery',
    ];

    /**
     * method used to upload image to the model
     *
     * @param string $fieldName
     * @throws \ReflectionException
     */
    public function uploadImage($fieldName = 'user_image_path')
    {
        $imagePath = $this->storeImage($fieldName);

        $this->$fieldName = $imagePath;
        $this->save();

    }

    /**
     * method used to store image if exists, otherwise returns the previous field value
     *
     * @param string $fieldName
     * @param string $attributeName
     * @return string
     * @throws \ReflectionException
     */
    public function storeImage($fieldName = 'user_image_path', $attributeName = 'image')
    {
        if ($image = request()->file($fieldName)) {
            if ($this->$attributeName) File::delete($this->$attributeName);
            $className = (new \ReflectionClass($this))->getShortName();
            return  request()->file($fieldName)->storeAs(
                    $this->getFolderName($this->folder . Str::lower(Str::plural($className, 2)) . '/'),
                    $this->getFileName($image),
                    'public'
                );
        } elseif (strpos(request($attributeName), 'data:image') !== false) {
            return $this->base64($attributeName);
        }
        return $this->$attributeName;
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

    /**
     * method used to return file name with extension related to the model object (using ID, name, title)
     *
     * @param $image
     * @return string
     */
    protected function getFileName($image = false)
    {
        $res = '';
        $addition = $this->translation ?: $this;

        if (isset($addition->title)) {
            $res .= Str::slug($addition->title) . '-';
        } elseif (isset($addition->name)) {
            $res .= Str::slug($addition->name) . '-';
        }

        $extension = $image != false ? $image->getClientOriginalExtension() : 'jpg';

        return $res . $this->id . '-' . Str::random(2) . '.' . $extension;
    }

    /**
     * method used to store base64 images (etc. uploads/posts/10-2018/image_name.jpg)
     *
     * @param string $attributeName
     * @return string
     * @throws \ReflectionException
     */
    protected function base64($attributeName = 'image')
    {
        $path = $this->getFolderName($this->folder . Str::lower(Str::plural((new \ReflectionClass($this))->getShortName(), 2)) . '/');
        $filename = $this->getFileName();
        Storage::disk('public')->put($path . '/' . $filename, base64_decode(explode(',', request($attributeName))[1]));
        return 'storage/' . $path . '/' . $filename;
    }

}
