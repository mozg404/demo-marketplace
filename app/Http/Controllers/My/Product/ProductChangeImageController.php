<?php

namespace App\Http\Controllers\My\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\MyProduct\ProductChangeImageRequest;
use App\Models\Product;
use App\Services\Product\ProductPreviewAttacher;
use App\Services\Toaster;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class ProductChangeImageController extends Controller
{
    public function index(Product $product): Response
    {
        return Inertia::render('common/ImageUploaderModal', [
            'imageUrl' => $product->getFirstMediaUrl($product::MEDIA_COLLECTION_PREVIEW),
            'aspectRatio' => 3/4,
            'saveRoute' => route('my.products.change.image.update', $product->id),
            'product' => $product,
        ]);
    }

    public function update(
        Product $product,
        ProductChangeImageRequest $request,
        ProductPreviewAttacher $previewAttacher,
        Toaster $toaster
    ): RedirectResponse {
        try {
            $previewAttacher->attachPreviewFromUploadedFile($product, $request->file('image'));
            $toaster->success('Изображение сохранено');

            return redirect()->back();
        } catch (FileCannotBeAdded $e) {
            $toaster->error('Не удалось загрузить изображение');

            return redirect()->back()->withErrors(['image' => 'Не удалось загрузить аватар']);
        }
    }
}
