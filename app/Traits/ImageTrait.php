<?php

namespace App\Traits;

trait ImageTrait {
    /**
     * @param $value, $id
     * @return string|null
     */
    public function getImagePath($value, $id): ?string
    {

        $id = '/'.(string)$id.'/';
        $pattern = [
            '/candidate/',
            $id,
        ];

        $path = preg_replace($pattern, '', $value) ?? '';

        $path = str_replace('/_', '', $path);
        $newPath = str_replace('image/', 'image/' . $this->user_id, $path);

        return !empty($value) ? env('IMAGE_SERVER') . '/' . $newPath : null;
    }
}
