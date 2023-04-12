# Image Rendering Service

This package currently ships with 4 rendering
services, [Glide](http://glide.thephpleague.com/), [Imgix](https://www.imgix.com/), [Twicpics](https://twicpics.com), and a local minimalistic rendering service. It is very simple to implement another one like [Cloudinary](http://cloudinary.com/) or even another local service like or [Croppa](https://github.com/BKWLD/croppa).
Changing the image rendering service can be done by changing the `MEDIA_LIBRARY_IMAGE_SERVICE` environment variable to one of the following options:

- `A17\Twill\Services\MediaLibrary\Glide`
- `A17\Twill\Services\MediaLibrary\Imgix`
- `A17\Twill\Services\MediaLibrary\TwicPics`
- `A17\Twill\Services\MediaLibrary\Local`

For a custom image service you would have to implement the `ImageServiceInterface` and modify your `twill` configuration value `media_library.image_service` with your implementation class.
Here are the methods you would have to implement:

```php
<?php

public function getUrl($id, array $params = []);
public function getUrlWithCrop($id, array $crop_params, array $params = []);
public function getUrlWithFocalCrop($id, array $cropParams, $width, $height, array $params = []);
public function getLQIPUrl($id, array $params = []);
public function getSocialUrl($id, array $params = []);
public function getCmsUrl($id, array $params = []);
public function getRawUrl($id);
public function getDimensions($id);
public function getSocialFallbackUrl();
public function getTransparentFallbackUrl();
```

$crop_params will be an array with the following keys: crop_x, crop_y, crop_w and crop_y. If the service you are implementing doesn't support focal point cropping, you can call the getUrlWithCrop from your implementation.
