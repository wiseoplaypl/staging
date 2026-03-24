/**
 * Copyright 2024 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

 window.addEventListener('load',function() {
    $('.lgmodule-success').fadeOut(4000);

    if ($('#show_header_off').prop('checked') == true || $('#show_link_add_review_off').prop('checked') == true) {
        $('#url_add_review').closest('div.form-group').slideUp();
    }

    if ($('#show_header_on').prop('checked') == true && $('#show_link_add_review_on').prop('checked') == true) {
        $('#url_add_review').closest('div.form-group').slideDown();
    }

    $(document).on('change', '#show_link_add_review_off', function () {
        $('#url_add_review').closest('div.form-group').slideUp();
    });

    $(document).on('change', '#show_link_add_review_on', function () {
        $('#url_add_review').closest('div.form-group').slideDown();
    });


    if ($('#show_header_off').prop('checked') == true) {
        $('#show_image_header_on').closest('div.form-group').slideUp();
        $('#show_link_business_on').closest('div.form-group').slideUp();
        $('#show_link_all_reviews_on').closest('div.form-group').slideUp();
        $('#show_link_add_review_on').closest('div.form-group').slideUp();
        $('#show_powered_by_google_on').closest('div.form-group').slideUp();
        $('#show_total_ratings_on').closest('div.form-group').slideUp();
        $('#url_add_review').closest('div.form-group').slideUp();
    }

    if ($('#show_header_on').prop('checked') == true) {
        $('#show_image_header_on').closest('div.form-group').slideDown();
        $('#show_link_business_on').closest('div.form-group').slideDown();
        $('#show_link_all_reviews_on').closest('div.form-group').slideDown();
        $('#show_link_add_review_on').closest('div.form-group').slideDown();
        $('#show_powered_by_google_on').closest('div.form-group').slideDown();
        $('#show_total_ratings_on').closest('div.form-group').slideDown();
        if ($('#show_link_add_review_on').prop('checked') == true) {
            $('#url_add_review').closest('div.form-group').slideDown();
        }

    }

    $(document).on('change', '#show_header_off', function () {
        $('#show_image_header_on').closest('div.form-group').slideUp();
        $('#show_link_business_on').closest('div.form-group').slideUp();
        $('#show_link_all_reviews_on').closest('div.form-group').slideUp();
        $('#show_link_add_review_on').closest('div.form-group').slideUp();
        $('#show_powered_by_google_on').closest('div.form-group').slideUp();
        $('#show_total_ratings_on').closest('div.form-group').slideUp();
        $('#url_add_review').closest('div.form-group').slideUp();
    });

    $(document).on('change', '#show_header_on', function () {
        $('#show_image_header_on').closest('div.form-group').slideDown();
        $('#show_link_business_on').closest('div.form-group').slideDown();
        $('#show_link_all_reviews_on').closest('div.form-group').slideDown();
        $('#show_link_add_review_on').closest('div.form-group').slideDown();
        $('#show_powered_by_google_on').closest('div.form-group').slideDown();
        $('#show_total_ratings_on').closest('div.form-group').slideDown();
        if ($('#show_link_add_review_on').prop('checked') == true) {
            $('#url_add_review').closest('div.form-group').slideDown();
        }
    });
});
