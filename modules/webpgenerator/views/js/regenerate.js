/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 *
 * @var otherImagesUrl
 * @var imageList
 * @var productProgress
 * @var categoryProgress
 * @var supplierProgress
 * @var manufacturerProgress
 *
 * @var storeProgress
 */
function stop(entity) {
    $.ajaxq.clear(entity + "Sync");
    $.ajaxq.clear(entity + "Delete");
}

function regenerate(entity) {
    $('#' + entity + '-imgs .img-stop').removeAttr('disabled');
    let oneImagePercent = 100;

    if (imageList[entity].todo.length > 0) {
        oneImagePercent = 100 / imageList[entity].todo.length;

    }
    for (var i = 0; i < imageList[entity].todo.length; i++) {
        toastr.clear();
        updateProgressBar($('#' + entity + '-imgs .progress-bar'), 0);
        let current = i;
        $.ajaxq(entity + "Sync", {
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'regenerate',
                image: imageList[entity].todo[i],
                currentIndex: i,
                type: entity,
                ajax: true
            },
        }).success(function (response) {
            if (response.success) {
                toastr.success(entity + ' image ' + imageList[entity].todo[current] + ' regenerated');
            } else {
                toastr.error(response.error);
            }
            updateProgressBar($('#' + entity + '-imgs .progress-bar'), oneImagePercent * (current + 1));
            updateProgressNumbers($('#' + entity + '-imgs .img-completed'), (current + 1));
            setEntityProgress(entity, current);
            if (oneImagePercent * (current + 1) === 100) {
                $('#' + entity + '-imgs .img-stop').attr('disabled', 'true');
            }
        }).error(function (response) {
            toastr.error(entity + ' image ' + imageList[entity].todo[current] + ' regeneration failed');
        });
    }
}

function resume(entity) {
    $('#' + entity + '-imgs .img-stop').removeAttr('disabled');
    let oneImagePercent = 100;

    if (imageList[entity].todo.length > 0) {
        oneImagePercent = 100 / imageList[entity].todo.length;
    }
    for (var i = 0; i < imageList[entity].todo.length; i++) {
        if (i < getEntityProgress(entity)) {
            continue;
        }
        toastr.clear();
        let current = i;
        $.ajaxq(entity + "Sync", {
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'regenerate',
                image: imageList[entity].todo[i],
                currentIndex: i,
                type: entity,
                ajax: true
            },
        }).success(function (response) {
            if (response.success) {
                toastr.success(entity + ' image ' + imageList[entity].todo[current] + ' regenerated');
            } else {
                toastr.error(response.error);
            }
            updateProgressBar($('#' + entity + '-imgs .progress-bar'), oneImagePercent * (current + 1));
            updateProgressNumbers($('#' + entity + '-imgs .img-completed'), (current + 1));
            setEntityProgress(entity, current);
            if (oneImagePercent * (current + 1) === 100) {
                $('#' + entity + '-imgs .img-stop').attr('disabled', 'true');
            }
        }).error(function (response) {
            toastr.error(entity + ' image ' + imageList[entity].todo[current] + ' regeneration failed');
        });
    }
}

function deleteImage(entity) {
    if (!confirm("Are you sure? You can always regenerate the JPG files if needed")) {
        return;
    }
    let oneImagePercent = 100;

    if (imageList[entity].todo.length > 0) {
        oneImagePercent = 100 / imageList[entity].todo.length;
        console.log();
    }
    for (var i = 0; i < imageList[entity].todo.length; i++) {
        toastr.clear();
        updateProgressBar($('#' + entity + '-imgs .progress-bar'), 0);
        let current = i;
        $.ajaxq(entity + "Delete", {
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'delete',
                image: imageList[entity].todo[i],
                type: entity,
                ajax: true
            },
        }).success(function (response) {
            if (response.success) {
                toastr.success(entity + ' image ' + imageList[entity].todo[current] + ' deleted');
            } else {
                toastr.error(response.error);
            }
            updateProgressBar($('#' + entity + '-imgs .progress-bar'), oneImagePercent * (current + 1));
            updateProgressNumbers($('#' + entity + '-imgs .img-completed'), (current + 1));
        }).error(function (response) {
            toastr.error(entity + ' image ' + imageList[entity].todo[current] + ' delete failed');
        });
    }
}

function updateProgressNumbers(element, current) {
    $(element).text(current);
}

function updateProgressBar(element, percent) {
    if (percent > 0) {
        percent = percent.toFixed(2);
    }
    $(element).css('width', percent + "%");
    $(element).attr('aria-valuenow', percent);
    $(element).attr('valuenow', percent);
    $(element).find('.sr-percent').text(percent);
}

function getEntityProgress(entity) {
    let entities = {
        product: productProgress,
        category: categoryProgress,
        supplier: supplierProgress,
        manufacturer: manufacturerProgress,
        store: storeProgress
    };
    return entities[entity];
}

function setEntityProgress(entity, progress) {
    let entities = {
        product: productProgress,
        category: categoryProgress,
        supplier: supplierProgress,
        manufacturer: manufacturerProgress,
        store: storeProgress
    };

    productProgress = progress;
}

function loadOtherImages(skipExisting, type, callback) {
    $(`#${type}-images-load`).css('display', 'inline-block');
    $.ajax({
        type: 'GET',
        cache: false,
        dataType: 'json',
        url: otherImagesUrl,
        data: {
            ajax: true,
            controller: 'AdminWebpgeneratorRegenerate',
            type: type,
            action: 'otherImages',
            token: token,
            skipExisting: skipExisting
        },
        success: function (data) {
            imageList[type] = data[type];
            //updateProgressNumbers($(`#${type}-imgs .img-total`), (imageList[type].todo.length + 1));
            updateProgressNumbers($(`#${type}-imgs .img-total`), (imageList[type].todo.length));
            $(`#${type}-imgs .btn-group`).show();
            $(`#${type}-images-load`).hide();
            if (typeof callback === "function") {
                callback();
            }
        }
    });
}


function deleteAllWebpImages() {
    if (confirm("Are you sure? This process is irreversible and we are not responsible for any losses!")) {
        $.ajaxq("custom_others", {
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'delete',
                type: 'others',
                ajax: true
            },
        }).success(function (response) {
            toastr.success("Images were deleted");
            updateProgressBar($('#product-imgs .progress-bar'), 0);
            updateProgressNumbers($('#product-imgs .img-completed'), 0);
            updateProgressBar($('#category-imgs .progress-bar'), 0);
            updateProgressNumbers($('#category-imgs .img-completed'), 0);
            updateProgressBar($('#supplier-imgs .progress-bar'), 0);
            updateProgressNumbers($('#supplier-imgs .img-completed'), 0);
            updateProgressBar($('#manufacturer-imgs .progress-bar'), 0);
            updateProgressNumbers($('#manufacturer-imgs .img-completed'), 0);
            updateProgressBar($('#store-imgs .progress-bar'), 0);
            updateProgressNumbers($('#store-imgs .img-completed'), 0);
        }).error(function (response) {
            toastr.error("Delete failed, most probably the server timed out");
        });
    }
}

function generateCustomImage(imageSrc) {
    if (document.getElementById(imageSrc).value.length) {
        //const imgPath = imgBasePath + '/' + document.getElementById(imageSrc).value;
        let rawPath = document.getElementById(imageSrc).value.replace(window.location.origin, "");
        if (rawPath.charAt(0) != '/') {
            rawPath = '/' + rawPath;
        }
        const imgPath = imgBasePath + rawPath;
        $.ajaxq("custom_others", {
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'regenerate',
                image: imgPath,
                type: 'others',
                ajax: true
            },
        }).success(function (response) {
            if (response.success) {
                toastr.success(imgPath + ' regenerated');
                document.getElementById(imageSrc).value = '';
            } else {
                toastr.error(response.error);
            }
        }).error(function (response) {
            toastr.error(imgPath + ' regeneration failed');
        });
    }
}


document.addEventListener("DOMContentLoaded", function () {
    let oneImagePercent = 100 / imageList['product'].todo.length;
    if (productProgress > imageList['product'].todo.length || oneImagePercent * (productProgress + 1) > 91) {
        productProgress = imageList['product'].todo.length - 1;
    }
    updateProgressBar($('#product-imgs .progress-bar'), oneImagePercent * (productProgress + 1));
    updateProgressNumbers($('#product-imgs .img-completed'), (productProgress + 1));

    oneImagePercent = 100 / imageList['category'].todo.length;
    updateProgressBar($('#category-imgs .progress-bar'), oneImagePercent * (categoryProgress + 1));
    updateProgressNumbers($('#category-imgs .img-completed'), (categoryProgress + 1));

    oneImagePercent = 100 / imageList['supplier'].todo.length;
    updateProgressBar($('#supplier-imgs .progress-bar'), oneImagePercent * (supplierProgress + 1));
    updateProgressNumbers($('#supplier-imgs .img-completed'), (supplierProgress + 1));

    oneImagePercent = 100 / imageList['manufacturer'].todo.length;
    updateProgressBar($('#manufacturer-imgs .progress-bar'), oneImagePercent * (manufacturerProgress + 1));
    updateProgressNumbers($('#manufacturer-imgs .img-completed'), (manufacturerProgress + 1));

    oneImagePercent = 100 / imageList['store'].todo.length;
    updateProgressBar($('#store-imgs .progress-bar'), oneImagePercent * (storeProgress + 1));
    updateProgressNumbers($('#store-imgs .img-completed'), (storeProgress + 1));

    const customImageDialog = document.getElementById('custom-image-dialog');

    document.getElementById('page-header-desc-configuration-custom-regenerate').onclick = function () {
        if (customImageDialog.closest('#content-tab-regenerate') && !customImageDialog.closest('#content-tab-regenerate.active')) {
            $("#pch-tabs-container .list-group-item.t-pane[data-target='custom-regenerate']").click();
        } else {
            customImageDialog.showModal();
        }
    }

    document.getElementById('custom-regenerate-submit').onclick = function () {
        generateCustomImage('img-source-path');
    }
    document.getElementById('custom-regenerate-cancel').onclick = function () {
        customImageDialog.close();
    }

    document.getElementById('custom-regenerate-submit-form').onclick = function (e) {
        if (document.getElementById('regen-img-source-path').value.length) {
            e.preventDefault();
        }
        generateCustomImage('regen-img-source-path');
    }

    document.getElementById('page-header-desc-configuration-delete-webp-images').onclick = function () {
        deleteAllWebpImages();
    }
    document.getElementById('delete-all-webp-images').onclick = function () {
        deleteAllWebpImages();
    }
});

// page-header-desc-configuration-custom-regenerate

function setRange() {
    var rangeStart = parseInt(prompt("Start from:", 1));
    var rangeEnd = parseInt(prompt("Until:", imageList.product.todo[imageList.product.todo.length - 1]));
    console.log(imageList.product.todo);
    // imageList.product.todo = [];
    imageList.product.todo = imageList.product.todo.filter(element => (parseInt(element) <= rangeEnd) && (parseInt(element) >= rangeStart));
    console.log(imageList.product.todo);
    updateProgressBar($('#product-imgs .progress-bar'), 0);
    updateProgressNumbers($('#product-imgs .img-completed'), 0);
    updateProgressNumbers($('#product-imgs .img-total'), imageList.product.todo.length);
    setEntityProgress('product', 0);
    alert(rangeStart + "   " + rangeEnd);
}