<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /**
     * 图文回复图片上传
     *
     * @param  Request  $request
     * @return Response
     */
    public function newsImage(Request $request)
    {
        $fileds = [
            'image' => 'required|image',//'dimensions:min_width=100,min_height=200'
        ];
        $this->validate($request, $fileds);
        $imageDir = 'wechat/news/' . date('Ym');
        $imageUrl = $this->uploadImage($request->file('image'),$imageDir);

        return response()->ajax($imageUrl);
    }

    /**
     * 图片回复图片上传
     *
     * @param  Request  $request
     * @return Response
     */
    public function userAvatar(Request $request)
    {
        $fileds = [
            'image' => 'required|image',//'dimensions:min_width=100,min_height=200'
        ];
        $this->validate($request, $fileds);
        $imageDir = 'user/avatar/' . date('Ym');
        $imageUrl = $this->uploadImage($request->file('image'),$imageDir);

        return response()->ajax($imageUrl);
    }

    private function uploadImage(UploadedFile $file,$imageDir)
    {
        $path = $file->store($imageDir,'oss');
        $imageUrl  = getImageUrl($path);
        return $imageUrl;
    }
}