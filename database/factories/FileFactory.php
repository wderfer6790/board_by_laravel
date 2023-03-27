<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\File;
use Illuminate\Support\Facades\{Storage, File as FileInfo};

class FileFactory extends Factory
{
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws DatabaseFactoriesException
     */
    public function definition()
    {
        $dir = Storage::disk('public');

        $from = collect($dir->Files('image/sample'))->random();
        $to = 'uploaded' . DIRECTORY_SEPARATOR . uniqid(date("ymdHis_")) . "." . FileInfo::extension($from);
        if (!$dir->copy($from, $to) || !$dir->exists($to)) {
            throw new DatabaseFactoriesException('file not copied');
        }

        return [
            'name' => FileInfo::basename($from),
            'path' => 'storage' . DIRECTORY_SEPARATOR . $to,
            'mime' => FileInfo::mimeType($dir->path($from)),
        ];
    }
}
