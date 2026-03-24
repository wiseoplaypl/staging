/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2018 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
 */
$(document).ready(function () {
    if ($('#kb_christmas_thatha_image img').length) {
        if (typeof (random_image) != 'undefined') {
            if (design_type = 'random') {
                thatha_fly('random', 15000, random_image);
            } else {
                thatha_fly('flip', 15000, 0, random_image);
            }
        }
    }
});

/********Santa Claus********/
var right_to_left_image = '';
var left_to_right_image = '';
function thatha_fly(type, new_speed, fly_image, next_image) {
    if (new_speed) {
        ani_speed = new_speed;
    }
    if (fly_image) {
        right_to_left_image = fly_image;
    }
    if (next_image) {
        left_to_right_image = next_image;
    }

    if (type == 'random') {
        $('#kb_christmas_thatha_image img').attr('src', fly_image);
        thatha_random_animation();
    } else {
        $('#kb_christmas_thatha_image img').attr('src', next_image);
        animate_vertical_horizontal(type);
    }
}

function animate_vertical_horizontal(flip_style) {
    $('#kb_christmas_thatha_image').css('bottom', 0).css('right', -$('#kb_christmas_thatha_image').width());
    var pos = $(document).width();
    var thatha_image_width = $('#kb_christmas_thatha_image').width();

    if (flip_style == 'flip') {

        var bottom_pos = $(document).height() / 4.5;

        $('#kb_christmas_thatha_image').animate({right: pos, bottom: (bottom_pos * 1)}, ani_speed, function (e) {
            set_flying_image(right_to_left_image);
            $('#kb_christmas_thatha_image').animate({right: -(thatha_image_width), bottom: (bottom_pos * 2)}, ani_speed, function (e) {
                set_flying_image(left_to_right_image);
                $('#kb_christmas_thatha_image').animate({right: pos, bottom: (bottom_pos * 3)}, ani_speed, function (e) {
                    set_flying_image(right_to_left_image);
                    $('#kb_christmas_thatha_image').animate({right: -(thatha_image_width), bottom: (bottom_pos * 4)}, ani_speed, function (e) {
                        set_flying_image(left_to_right_image);
                        animate_vertical_horizontal(flip_style);
                    });
                });
            });
        });

    } else {

        var bottom_pos = $(document).height() / 3.5;

        $('#kb_christmas_thatha_image').animate({right: pos, bottom: (bottom_pos * 1)}, ani_speed, function (e) {
            set_flying_image(right_to_left_image);
            $('#kb_christmas_thatha_image').css('bottom', $('#kb_christmas_thatha_image').css('bottom')).css('right', -$('#kb_christmas_thatha_image').width());
            $('#kb_christmas_thatha_image').animate({right: pos, bottom: (bottom_pos * 2)}, ani_speed, function (e) {
                set_flying_image(left_to_right_image);
                $('#kb_christmas_thatha_image').css('bottom', $('#kb_christmas_thatha_image').css('bottom')).css('right', -$('#kb_christmas_thatha_image').width());
                $('#kb_christmas_thatha_image').animate({right: pos, bottom: (bottom_pos * 3)}, ani_speed, function (e) {
                    set_flying_image(left_to_right_image);
                    animate_vertical_horizontal(flip_style);
                });
            });
        });

    }
}

function set_flying_image(get_image) {
    $('#kb_christmas_thatha_image img').attr('src', get_image);
}

function thatha_random_animation() {
    var h = $(document).height() - 50;
    var w = $(document).width() - 50;
    var nh = Math.floor(Math.random() * h);
    var nw = Math.floor(Math.random() * w);
    $('#kb_christmas_thatha_image').animate({top: nh, right: nw}, 5000, function () {
        thatha_random_animation();
    });
}
