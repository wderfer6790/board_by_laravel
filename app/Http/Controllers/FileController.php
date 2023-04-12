<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use Illuminate\Support\Facades\{File as FileInfo, DB};

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘몬된 접근입니다";
            goto sendRes;
        }

        $file = $request->file('upload_file');
        if (!$file) {
            $res['msg'] = "선택된 파일이 없습니다.";
            goto sendRes;
        }

        $mimeType = $file->getMimeType();
        $filename = $file->getClientOriginalName();
        $uploadName = uniqid(date('ymdHis_')) . "." . strtolower($file->getClientOriginalExtension());
        $uploadPath = storage_path('app/public/uploaded') . DIRECTORY_SEPARATOR . $uploadName;
        if (!copy($file->getPathname(), $uploadPath)) {
            $res['msg'] = "업로드 중 문제가 발생하였습니다.copy";
            goto sendRes;
        }

        $uploadedFile = new File([
            'name' => $filename,
            // asset path
            'path' => str_replace(base_path() . DIRECTORY_SEPARATOR, "", storage_path('uploaded') . DIRECTORY_SEPARATOR . $uploadName),
            'mime' => $mimeType,
        ]);

        if (!$uploadedFile->save()) {
            $res['msg'] = "업로드 중 문제가 발생하였습니다.";
            goto sendRes;
        }

        $res['res'] = true;
        $res['file_id'] = $uploadedFile->id;
        $res['name'] = $uploadedFile->name;
        $res['src'] = asset($uploadedFile->path);

        sendRes:
        return response()->json($res);
    }

    public function download(Request $request)
    {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest()) {
            $res['msg'] = "잘몬된 접근입니다";
            goto sendRes;
        }



        sendRes:
        return response()->json($res);
    }

    public function delete($id)
    {
        $res = ['res' => false, 'msg' => ""];

        $file = File::find($id);
        if (!$file) {
            $res['msg'] = "삭제할 파일을 찾지 못하였습니다.";
            goto sendRes;
        }

        // detach all relations
        $file->users()->detach();
        $file->articles()->detach();
        $file->replies()->detach();

        $fileable_cnt = DB::table('fileable')->where('file_id', $file->id)->count();
        if ($fileable_cnt > 0) {
            $res['msg'] = "파일삭제 중 문제가 발생하였습니다.(1)";
            goto sendRes;
        }

        // uploaded file delete
        if (!FileInfo::delete($file->path) || FileInfo::exists($file->path) || FileInfo::isFile($file->path)) {
            $res['msg'] = "파일삭제 중 문제가 발생하였습니다.(2)";
            goto sendRes;
        }

        // file row delete
        if ($file->delete() === false) {
            $res['msg'] = "파일삭제 중 문제가 발생하였습니다.(3)";
            goto sendRes;
        }

        $res['res'] = true;

        sendRes:
        return response()->json($res);
    }
}
