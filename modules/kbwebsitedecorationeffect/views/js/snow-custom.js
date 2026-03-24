/**
 * 2007-2022 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */


$(document).ready(function () {
    if (kb_effect_type == 'flake') {
        $(document).snowFlurry({
            maxSize:parseInt(kb_snowfall_flake_size),
            numberOfFlakes:parseInt(kb_snowfall_number_flakes),
            minSpeed:parseInt(kb_snowfall_min_speed),
            maxSpeed:parseInt(kb_snowfall_max_speed),
            color: kb_snowfall_flake_color,
            timeout:parseInt(kb_snowfall_disable_time)
        });
    } else if (kb_effect_type == 'flurry') {
        $('body').flurry({
            character: kb_flurry_character,
            color: kb_snowfall_flake_color,
            frequency: kb_flurry_frequency,
            height: kb_flurry_height,
            speed: kb_flurry_speed,
            small: kb_flurry_min_size,
            large: kb_flurry_max_size,
            wind: kb_flurry_wind_drift,
            windVariance: kb_flurry_wind_variance,
            rotation: kb_flurry_rotation,
            rotationVariance: kb_flurry_rotation_variance,
            startOpacity: 1,
            endOpacity: 0,
            opacityEasing: "cubic-bezier(1,.3,.6,.74)",
            blur: true,
            overflow: "hidden",
            zIndex: 9999
        });
    } else if (kb_effect_type == 'letitsnow') {
        $('body').letItSnow({
            fall_time:parseInt(kb_letitsnow_falltime),
            color:kb_snowfall_flake_color,
            size_min:parseInt(kb_letitsnow_min_size),
            size_max:parseInt(kb_letitsnow_max_size),
            zindex:9999,
            maxcount:parseInt(kb_letitsnow_max_count),
            wind:parseInt(kb_letitsnow_speed),
            easing_x:"easeInBack",
            easing_y:"easeInCubic",
        });
    } else if (kb_effect_type == 'christmas') {
        var snowEffectInterval = jQuery.fn.snow({
            // min size of element (default: 20)
            minSize:parseInt(kb_christmas_min_size),
            // max size of element (default: 50)
            maxSize:parseInt(kb_christmas_max_size),
            // flake fall time multiplier (default: 20)
            fallTimeMultiplier:parseInt(kb_christmas_fallTimeMultiplier),
            // flake fall time difference (default: 10000)
            fallTimeDifference:parseInt(kb_christmas_fallTimeDifference),
            // interval (miliseconds) between new element spawns (default: 500)
            spawnInterval: kb_christmas_spawnInterval,
            // jQuery element to apply snow effect on (should work on any block element) (default: body)
            target: jQuery("body"),
            //elements to use in generating snow effect
            elements: [
                    // Element #1
                { 
                  // html element to be spawned for this element
                  html: '<i class="fa fa-snowflake-o" aria-hidden="true"></i>',
                  // hex color for this element - works only for font based icons
                  color: kb_snowfall_flake_color
                },
                // Element #1
                {
                    html: '<i class="fa fa-bell-o" aria-hidden="true"></i>',
                    color: '#ed9b40'
                },
                // Element #2
                {
                    html: '<i class="fa fa-music" aria-hidden="true"></i>',
                    color: '#cc2037'
                },
            ]
        });
    }
});
