<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\UploadRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class ProductController extends Controller
{
    public function upload(UploadRequest $request)
    {
        /** @var UploadedFile $file */
        $file = $request->file;
        $content = $file->getContent();
        $lines = explode("\n", $content);
        array_shift($lines);
        $alreadyExists = [];
        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }
            $data = str_getcsv($line);
            $product = Product::where(['sku' => $data[0], 'ean' => $data[2]])->first();
            if ($product) {
                $alreadyExists[] = ['sku' => $data[0], 'ean' => $data[2]];
            } else {
                $product = new Product([
                    'sku' => $data[0],
                    'title' => $data[1],
                    'ean' => $data[2],
                    'uk_only' => $data[3],
                ]);
                $product->save();
            }
        }
        return response()->json([
            'success' => 'success',
            'not_updated' => $alreadyExists
        ]);
    }

}
