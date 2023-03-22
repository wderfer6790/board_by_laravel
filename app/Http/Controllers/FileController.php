<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;

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
        $pathname = uniqid('uploaded_') . "." . $file->getClientOriginalExtension();
        $uploadPath = storage_path('app/public/uploaded/') . $pathname;
        if (!copy($file->getPathname(), $uploadPath)) {
            $res['msg'] = "업로드 중 문제가 발생하였습니다.copy";
            goto sendRes;
        }

        $now = date('Y-m-d H:i:s');
        $fileId = $request->post('file_id');
        if ($fileId) {
            $uploadedFile = File::find($fileId)->first();
            $oldFiles = $uploadedFile->files;
            $uploadedFile->files = $oldFiles + [[
                    'name' => $filename,
                    'path' => $uploadPath,
                    'mime' => $mimeType,
                    'upload_at' => $now,
                ]];
            $uploadedFile->save();

        } else {
            $uploadedFile = new File();
            if ($uploadedFile->fill([
                'files' => [
                    [
                        'name' => $filename,
                        'path' => $uploadPath,
                        'mime' => $mimeType,
                        'upload_at' => $now
                    ]
                ],
            ])->save()) {
                $res['msg'] = "업로드 중 문제가 발생하였습니다.";
                goto sendRes;
            }
        }

        $res['res'] = true;
        $res['file_id'] = $uploadedFile->id;
        $res['name'] = $filename;
        $res['src'] = asset('storage/uploaded/' . $pathname);
        $res['type'] = explode('/', $mimeType)[0];

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

    public function delete(Request $request)
    {
        $res = ['res' => false, 'msg' => ""];
        if (!$request->isXmlHttpRequest() || !$request->isMethod('delete')) {
            $res['msg'] = "잘몬된 접근입니다";
            goto sendRes;
        }

        $fileId = $request->input('file_id');
        if (!$fileId) {
            $res['msg'] = "잘못된 접근입니다";
            goto sendRes;
        }

        $file = File::find($fileId);
        if (!$file) {
            $res['msg'] = "삭제할 대상을 찾지 못하였습니다.";
        }

        sendRes:
        return response()->json($res);
    }
}
