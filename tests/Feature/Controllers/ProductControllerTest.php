<?php

namespace Tests\Feature\Controllers;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    public function testUpload()
    {
        $csvData = "sku,title,ean,uk_only\n";
        $csvData .= "123,Product 1,0186538314034,true\n";
        $csvData .= "124,Product 2,0186538314035,false\n";
        $csvFile = tmpfile();
        fwrite($csvFile, $csvData);
        $path = stream_get_meta_data($csvFile)['uri'];
        $file = new UploadedFile($path, 'products.csv', 'text/csv', null, true);

        $response = $this->post(route('product.upload'), ['file' => $file]);

        $response->assertStatus(200);
        $response->assertJson(['success' => 'success']);

        $this->assertDatabaseHas('products', ['sku' => '123', 'title' => 'Product 1', 'ean' => '0186538314034', 'uk_only' => 'true']);
        $this->assertDatabaseHas('products', ['sku' => '124', 'title' => 'Product 2', 'ean' => '0186538314035', 'uk_only' => 'false']);

        $response->assertJsonFragment(['not_updated' => []]);

        fclose($csvFile);
    }

    public function testUploadWithExistingProducts()
    {
        Product::create([
            'sku' => '123',
            'title' => 'Existing Product',
            'ean' => '0186538314034',
            'uk_only' => 'true'
        ]);

        $csvData = "sku,title,ean,uk_only\n";
        $csvData .= "123,Product 1,0186538314034,true\n";
        $csvFile = tmpfile();
        fwrite($csvFile, $csvData);
        $path = stream_get_meta_data($csvFile)['uri'];
        $file = new UploadedFile($path, 'products.csv', 'text/csv', null, true);

        $response = $this->post(route('product.upload'), ['file' => $file]);

        $response->assertStatus(200);
        $response->assertJson(['success' => 'success']);

        $this->assertDatabaseMissing('products', ['sku' => '123', 'title' => 'Product 1', 'ean' => '0186538314034', 'uk_only' => 'true']);

        $response->assertJsonFragment(['not_updated' => [['sku' => '123', 'ean' => '0186538314034']]]);

        fclose($csvFile);
    }
}
