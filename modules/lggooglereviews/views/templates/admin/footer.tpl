{**
 * Copyright 2023 LÍNEA GRÁFICA E.C.E S.L.
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
 *}

<div class="lg-block">
    <div class="row">
        <div class="col-lg-4">
            <div class="lg-block-company">
                <span>{l s='Module developed by' mod='lggooglereviews'}</span>
                <img src="{$lg_base_url|escape:'htmlall':'UTF-8'}views/img/publi/logo_lgaddons.png"/>
            </div>
            <div class="lg-block-partner">
                <img src="{$lg_base_url|escape:'htmlall':'UTF-8'}views/img/publi/ico_partners.png"/>
                <span>{l s='PrestaShop platinum partner' mod='lggooglereviews'}</span>
            </div>
            <div class="lg-block-creator">
                <img src="{$lg_base_url|escape:'htmlall':'UTF-8'}views/img/publi/ico_creator.png"/>
                <span>{l s='Modules creator partner' mod='lggooglereviews'}</span>
            </div>
        </div>
        <div class="col-lg-7">
            {* MODULE 1 *}
            {foreach $lg_modules as $name => $module}
            <div class="col-lg-4">
                <div class="lg-module-block">
                    <a href="{$module.url|escape:'htmlall':'UTF-8'}" target="_blank">
                        <div class="lg-title">
                            <img src="{$lg_base_url|escape:'htmlall':'UTF-8'}views/img/publi/{$name|escape:'htmlall':'UTF-8'}.png"/>
                            <span class="lg-module-name">
                                {$module.name|escape:'htmlall':'UTF-8'}
                            </span>
                        </div>
                        <div class="lg-description">
                            {$module.description|escape:'htmlall':'UTF-8'}
                        </div>
                        <div class="lg-rating">
                            <span class="lg-rating">{l s='Rating' mod='lggooglereviews'}:</span>
                            {if $module.rating > 4.5}
                                <img src="{$lg_base_url|escape:'htmlall':'UTF-8'}views/img/publi/stars_10_peq.png"/>
                            {else}
                                <img src="{$lg_base_url|escape:'htmlall':'UTF-8'}views/img/publi/stars_9_peq.png"/>
                            {/if}
                        </div>
                        <div class="lg-button">
                            <span>{l s='More information' mod='lggooglereviews'}</span>
                        </div>
                    </a>
                </div>
            </div>
            {/foreach}            
        </div>
        <div class="col-lg-1">
            <div class="lg-background-image">
                <span class="lg-vertical-text">
                    <a href="https://addons.prestashop.com/es/22_linea-grafica" target="_blank">
                        {l s='Discover all our modules' mod='lggooglereviews'}
                    </a>
                </span>
            </div>
        </div>
    </div>
</div>