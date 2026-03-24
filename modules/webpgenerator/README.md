# Install
## Prestashop 1.7
PrestaShop 1.7 provides a functionality to regenerate images on the fly, but unfortunately it supports only files with JPG extension.

So if you deleted the re-sized JPG images you should disable this functionality in order to skip this step.

You can do this by commenting out the following code in the /src/Adapter/Image/ImageRetriever.php file around line 146:

```php
if (!file_exists($resizedImagePath)) {
    ImageManager::resize(
        $mainImagePath,
        $resizedImagePath,
        (int)$image_type['width'],
        (int)$image_type['height']
    );
}
```

