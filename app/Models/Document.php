<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Functions;
use \Gumlet\ImageResize;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $table = 'document';
	public $timestamps = true;

	/**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'extension',
        'filePath',
        'miniaturesJSON'
    ];

    // saves sent photo resourse at sent firectory name
    // returns object
	public function saveImage($photo = null, $createMiniatures = null, $directoryName = 'app/images')
	{
		$allowedFormats = ['png' , 'jpeg', 'web', 'webp', 'gif'];
		$personId = session()->get('authUser-id');
		
        $basePath = storage_path();
        if(!is_dir(storage_path($directoryName))){
            $values = explode('/', $directoryName);
            foreach($values as $dir){
                $basePath .= '/' . $dir;
                if($dir == ''){
                    continue;
                }
                if(!is_dir($basePath)){
                    mkdir($basePath, 0777);
                }
            }
        }
        $path = storage_path($directoryName);
        if(!is_dir($path)){
            mkdir($path);
        }
		$response = [];
        if(is_null($photo))
            return null;
        if(!$createMiniatures){
            // generating a random file name
            $fileName = Functions::generateHash(null, true).'-'.str_replace([' ', ':'], '', (new \DateTime())->format('d-m-y h:i:s')).'-'.$personId.'.jpeg';
            $filePath = $path . '/' . $fileName;
            $image = new ImageResize($photo->getRealPath());
            $done  = $image->resize(256, 256, true)->save($filePath);
            if($done){
                $documentObj = Document::create([
                    'name'      => str_replace('.jpeg', '', $fileName),
                    'extension' => 'jpeg',
                    'filePath'  => $directoryName
                ]);
            }
        }else{
            $sizes = [
                //'bigphto'      => [500, 700],
                //'meddiumphoto' => [300, 500],
                'smallphoto'   => [160, 300],
                'minphoto'     => [80, 140]
            ];
            $fileName = Functions::generateHash(null, true).'-'.str_replace([' ', ':'], '', (new \DateTime())->format('d-m-y h:i:s')).'@!@'.$personId;
            $path = storage_path($directoryName);
            $image = new ImageResize($photo->getRealPath());
            $miniaturesArray = [];
            foreach($sizes as $size => $measures){
                $name = $fileName . '@!@' . $size . '.jpeg';
                $thisPath = $path . '/' . $name;
                $image->resize($measures[0], $measures[1], true)->save($thisPath);
                $miniaturesArray[] = $name;
            }
            $documentObj = Document::create([
                'name'           => str_replace('.jpeg', '', $fileName),
                'extension'      => 'jpeg',
                'filePath'       => $directoryName,
                'miniaturesJSON' => json_encode($miniaturesArray)
            ]);
        }
		return $documentObj;
	}

    // fetch miniatures of this document
    public function fetchMiniaturesAsBase64Array()
    {
        $pathToImage = storage_path($this->filePath);
        $imageInBase64 = [];
        $miniaturesArray = json_decode($this->miniaturesJSON, true);
        foreach($miniaturesArray as $miniature){
            $dataOfMiniature = explode('@!@1@!@', $miniature);
            $imageInBase64[str_replace('.' . $this->extension, '', $dataOfMiniature[1])] = base64_encode(file_get_contents($pathToImage . '/' . $miniature));
        }
        return $imageInBase64;
    }

    // returns the path of this document
    public function parsePath()
    {
        $path = storage_path($this->filePath) . '/' . $this->name . '.' . $this->extension;
        return $path;
    }

    // remove the file and document
    public function removeObjectAndFile($id = null)
    {
        $id = ($id ? $id : $this->id);
        $documentObj = Document::find($id);
        if(!$documentObj){
            return null;
        }
        $path = $documentObj->parsePath();
        if(file_exists($path)){
            unlink($path);
        }
        return $documentObj->forceDelete();
    }

    public function saveAnImageWithCrop($photo, $cropData = [], $thePath = 'app/images')
    {
        if($cropData){
            $newData = [];
            foreach($cropData as $key => $data){
                if($key == 'imgX' && $data == ''){
                    $data = false;
                }
                if($key == 'imgY' && $data == ''){
                    $data = false;
                }
                $newData[$key] = $data;
            }
            $cropData = $newData;
        }
        $path = storage_path($thePath);
        if(!is_dir($path)){
            mkdir($path, 0777);
        }
        Storage::makeDirectory($path);
        $pathToImg = (is_object($photo) ? $photo->getRealPath() : $photo);
        $image = new ImageResize($pathToImg);
        $name = Functions::generateHash(null, true) . '--' . session()->get('authUser-id') . '.jpeg';
        if(!array_key_exists('resize', $cropData)){
            $image->freecrop($cropData['imgWidth'], $cropData['imgHeight'], $cropData['imgX'], $cropData['imgY']);
        }else{
            $image->resize($cropData['imgWidth'], $cropData['imgHeight'], true);
        }
        $image->save($path . '/' . $name);
        $documentObj = Document::create([
            'name'      => str_replace('.jpeg', '', $name),
            'extension' => 'jpeg',
            'filePath'  => $thePath
        ]);
        return $documentObj;
    }
}
