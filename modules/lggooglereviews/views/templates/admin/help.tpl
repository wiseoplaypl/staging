{**
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
 *}

<div class="panel">
    <div class="panel-body lgmodule-container-help">
        <section class="tabs-container">
            <label for="tab-first-steps">{l s='After Installing' mod='lggooglereviews'}</label>
            <label for="tab-places">{l s='Places' mod='lggooglereviews'}</label>
            <label for="tab-maps-api-key">{l s='Your Google Place Api key' mod='lggooglereviews'}</label>
            <label for="tab-place-id">{l s='Your Place ID' mod='lggooglereviews'}</label>
            <label for="tab-request-reviews">{l s='Request Reviews' mod='lggooglereviews'}</label>
            {*<label for="tab-debug-mode">{l s='Debug Mode' mod='lggooglereviews'}</label>*}
            <label for="troubleshooting">{l s='Troubleshooting' mod='lggooglereviews'}</label>
        </section>

        <input name="tab" id="tab-first-steps" type="radio" checked />
        <section class="tab-content">
            <h2>{l s='After Installing' mod='lggooglereviews'}</h2>
            <div class="alert alert-warning">
                <p>{l s='To use this module, you must have your business registered on the Google My Bussiness platform and have an API key linked to Google Places in order to access its services.' tags=['<strong>'] mod='lggooglereviews'}</p>
            </div>
            <h3>{l s='General configuration' mod='lggooglereviews'}</h3>
            <p>{l s='Access the "Google Reviews" tab to include the general configuration of the module' mod='lggooglereviews'}</p>
            <ol>
                <li>{l s='Insert your Google Places API key. If you need to know how to obtain your key, see the "Your Google Place Api key" section.' mod='lggooglereviews' tags=['<strong>']}</li>
                <li>{l s='Select the position where you want to display the review block.' mod='lggooglereviews'}</li>
            </ol>
        </section>

        <input name="tab" id="tab-places" type="radio"/>
        <section class="tab-content">
            <h2>{l s='Places' mod='lggooglereviews'}</h2>
            <h3>{l s='List of places' mod='lggooglereviews'}</h3>
            <p>{l s='Access the "Places" tab, see the places you have already configured or include new ones' mod='lggooglereviews'}</p>
            <h3>{l s='Add a new place' mod='lggooglereviews'}</h3>
            <ol>
                <li>{l s='In the header of your list (empty if you have not included any place yet), click on the "+" symbol that you will find on the right'  mod='lggooglereviews' tags=['<strong>']}</li>
                <li>{l s='In the "Basic Configuration" tab complete the form fields' mod='lggooglereviews'}
                    <br>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}configuration_01.png">
                </li>
                <li>{l s='You can upload an 80x80px image to include the logo or a representative image of your company' mod='lggooglereviews'}</li>
                <li>{l s='Click "Save" to include your business' mod='lggooglereviews'}</li>
            </ol>
            <h3>{l s='Customize the elements to display in your business block' mod='lggooglereviews'}</h3>
            <ol>
                <li>{l s='You can select what items you want to display and how to do it in the public area of ​​your store. To do this, go to the "Presentation" tab of your business:'  mod='lggooglereviews' tags=['<strong>']}
                    <br>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}configuration_02.png">
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}configuration_03.png">
                </li>
                <li>{l s='In the field "Reviews to show" you can choose the number of blocks to show simultaneously in the slider' mod='lggooglereviews'}</li>
                <li>{l s='"Rich Snippets" are a set of tags that are not visible to your customers but are used by search engines to collect information from their opinions. If you want to make it easier for search engines to obtain this information, check "Yes"' mod='lggooglereviews'}</li>
                <li>{l s='"Show header" will include at the beginning of the block a header where you can show your business data such as: the name, the image, the ratings and the links to your profile in Google Places' mod='lggooglereviews'}</li>
                <li>{l s='"Show header image" will include the image associated with your company if you have uploaded it, and if not, the default icon that you have defined in Google My Business' mod='lggooglereviews'}</li>
                <li>{l s='"Show link to your company\'s Google My Business page" will show a link to your business profile on Google My Business' mod='lggooglereviews'}</li>
                <li>{l s='"Show link to [See all reviews]" will show a link where your customers can access your full list of reviews on Google My Business' mod='lggooglereviews'}</li>
                <li>{l s='"Show link to [Add Review]" will display a link to invite your customers to leave a review of your business' mod='lggooglereviews'}</li>
                <li>{l s='If you have selected the previous option, you must include the invitation url. In the "Request Reviews" section of the help we explain how to obtain it' mod='lggooglereviews'}</li>
                <li>{l s='Indicate if you want to display the message "Powered by Google"' mod='lggooglereviews'}</li>
                <li>{l s='Indicate if you want to show your score on Google my Business' mod='lggooglereviews'}</li>
                <li>{l s='Indicate if you want to show the number of reviews obtained in Google my Business' mod='lggooglereviews'}</li>
                <li>{l s='If you want to show the list of the latest reviews received, select the option' mod='lggooglereviews'}</li>
                <li>{l s='Indicate if you want to show a link to the profile of users who have left their reviews' mod='lggooglereviews'}</li>
            </ol>
            <h3>{l s='Review your business data' mod='lggooglereviews'}</h3>
                <p>{l s='In the tab "Statistics" you can see a statistical summary of your business data in Google My Business'  mod='lggooglereviews' tags=['<strong>']}</p>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}configuration_04.png">
        </section>


        <input name="tab" id="tab-maps-api-key" type="radio" />
        <section class="tab-content">
            <h2>{l s='Your Google Place Api key' mod='lggooglereviews'}</h2>
            <div class="alert alert-warning">
                <p>{l s='To use Google Maps and Places APIs, you need to register the application and get an API key. Probably, you already know, but we explain how to obtain this key if you do not know.' tags=['<strong>'] mod='lggooglereviews'}</p>
            </div>
            <h3>{l s='How create your Google Maps API KEY' mod='lggooglereviews'}</h3>
            <p>{l s='Follow the instructions below to create an API KEY.' mod='lggooglereviews'}</p>
            <ol>
                <li>{l s='You must log in with your gmail account or create a new gmail account that you want to associate with your project.' mod='lggooglereviews' tags=['<strong>']}</li>
                <li>
                    {l s='Once logged in, you should go to the Google Cloud Platform console: ' mod='lggooglereviews'}
                    <a href="https://console.cloud.google.com/" target="_blank">https://console.cloud.google.com/</a>
                </li>
                <li>{l s='In the left side menu, you will go to the APIS and Services section.' mod='lggooglereviews' tags=['<strong>']}</li>
                <li>{l s='Write the name of the project and click on the "CREATE" button.' mod='lggooglereviews' tags=['<strong>']}</li>
                <li>{l s='Afterwards, we will click on the button "ENABLE APIS AND SERVICES".' mod='lggooglereviews' tags=['<strong>']}</li>
                <li>{l s='For this case, select “API PLACES”.' mod='lggooglereviews' tags=['<strong>']}</li>
                <li>{l s='Click on the "ENABLE" button.' mod='lggooglereviews' tags=['<strong>']}</li>
                <li>{l s='Once the API is created, in the "Credentials" tab. There, click on the link "Credentials in APIs and services".' mod='lggooglereviews' tags=['<strong>']}</li>
                <li>{l s='Copy the API Key that is shown on the screen because we will need it later.' mod='lggooglereviews' tags=['<strong>']}</li>
                <li>{l s='You will see that in that same pop-up window, it gives you the option to “Restrict password”. Click on that link if you want that key to only be used in a certain domain, for example.' mod='lggooglereviews' tags=['<strong>']}</li>
                <li>{l s='Once you have restricted the password, if you have wanted, we will have to enter the billing information because otherwise, we will only be allowed to call this API once a day and, sincerely, I hope that your website has more than one visit per day. This does not mean that we are going to be charged anything since Google offers you this service for free for a large number of calls to the API, a number for which an SME business will normally not have to pay. If you exceed the number of views that Google gives you for free, it is likely that the business is going very, very well and that you should have no problem investing a little in this. Even if you don\'t get over it, Google asks you for your billing information in case that happens at some point. On the other hand, for your peace of mind, I tell you that Google allows you to put a limit on calls to its API per month. Therefore, you can limit the number of free calls per month and, thus, you will never be charged; simply, opinions will be stopped if you exceed that number.' mod='lggooglereviews' tags=['<strong>']}</li>
            </ol>
            <h3>{l s='How to enter the billing information?' mod='lggooglereviews'}</h3>
            <p>{l s='Follow the instructions below to enter your billing information.' mod='lggooglereviews'}</p>
            <ol>
                <li>{l s='In the Google Cloud Platform console, if we click on the menu icon with 3 horizontal lines, which is on the far left, the navigation menu will appear. There, we must click on the "Billing" item.' mod='lggooglereviews' tags=['<strong>']}</li>
                <li>{l s='We will click on the button "Manage billing accounts".' mod='lggooglereviews' tags=['<strong>']}</li>
                <li>{l s='We will click on the button "Add billing account" and we will follow the steps indicated by Google, entering the requested information.' mod='lggooglereviews' tags=['<strong>']}</li>
                <li>{l s='Select the Google payments profile that will be associated with this billing account. You can select an existing profile or create a new one.' mod='lggooglereviews' tags=['<strong>']}</li>
                <li>{l s='Finally, in the project that you are configuring, we must enable billing for that project. We will do this in "Billing", clicking on "Link billing account". We will select the account and click on "Establish account".' mod='lggooglereviews' tags=['<strong>']}</li>
            </ol>
        </section>

        <input name="tab" id="tab-place-id" type="radio" />
        <section class="tab-content">
            <h2>{l s='Your Place ID' mod='lggooglereviews'}</h2>
            <h3>{l s='How do I know what the Place ID of my business is?' mod='lggooglereviews'}</h3>
            <ol>
                <li>{l s='You can go to this page ' mod='lggooglereviews'}
                    <a href="https://developers.google.com/places/place-id" target="_blank">https://developers.google.com/places/place-id</a>.
                </li>
                <li>{l s='You just have to enter the name of your business as it appears in Google My Business and it will show you your PLACE ID.' mod='lggooglereviews'}
                    <br>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}place_01.png">
                </li>
                <li>{l s='Copy your Google Place ID in your module' mod='lggooglereviews'}
                    <br>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}place_02.png">
                </li>
            </ol>
        </section>

        <input name="tab" id="tab-request-reviews" type="radio" />
        <section class="tab-content">
            <h2>{l s='Where to find your url to request Reviews?' mod='lggooglereviews'}</h2>
            <ol>
                <li>{l s='Log in to your Google My Business account.' mod='lggooglereviews'}  (<a href="https://www.google.com/business/" target='_blanck'>https://www.google.com/business/</a>)</li>
                <li>{l s='If you have more than one company in your list, select the one you want to show on your website.' mod='lggooglereviews'}</li>
                <li>{l s='Click the button "Share profile"' mod='lggooglereviews'}
                    <br>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}business_01.png">
                </li>
                <li>{l s='You can directly copy the short URL so that your customers can write their reviews.' mod='lggooglereviews'}
                    <br>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}business_02.png">
                </li>
                <li>{l s='In your module, check "Yes" in the option "Show link to add review"'  mod='lggooglereviews'}
                    <br>
                    <img src="{$lg_help_url|escape:'htmlall':'UTF-8'}business_03.png">
                </li>
                <li>{l s='Include the copied url in the field "Url to add reviews"' mod='lggooglereviews'}
                </li>
                <li>{l s='Click "Save"' mod='lggooglereviews'}</li>
            </ol>
        </section>

        <input name="tab" id="troubleshooting" type="radio" />
        <section class="tab-content troubleshooting">
            <h2>{l s='Troubleshooting' mod='lggooglereviews'}</h2>
            <p>{l s='These are some of the queries we often see:' mod='lggooglereviews'}</p>
            <h3>{l s='If the place you have created comes out as NOT validated, the following checks must be carried out' mod='lggooglereviews'}</h3>
            <ol>
                <li>{l s='Without entering the billing information the module will not work.' mod='lggooglereviews'}</li>
                <li>{l s='It is necessary to have your company created in Google My Business.' mod='lggooglereviews'}</li>
                <li>{l s='In Google My Business you must have an address for your company (even if you are a 100% online store).' mod='lggooglereviews'}</li>
                <li>                    
                    {if (isset($lggooglereviews_places) && !empty($lggooglereviews_places)) && (isset($lggooglereviews_apikey) && !empty($lggooglereviews_apikey))}
                        {l s='Access the following URL:' mod='lggooglereviews'}
                        {foreach $lggooglereviews_places as $lggooglereviews_place}
                            <a href="https://maps.googleapis.com/maps/api/place/details/json?placeid={$lggooglereviews_place.google_place_id|escape:'htmlall':'UTF-8'}&key={$lggooglereviews_apikey|escape:'htmlall':'UTF-8'}" target="_blank">https://maps.googleapis.com/maps/api/place/details/json?placeid={$lggooglereviews_place.google_place_id|escape:'htmlall':'UTF-8'}&key={$lggooglereviews_apikey|escape:'htmlall':'UTF-8'}</a>
                        {/foreach}
                    {else}
                        {l s='Access the following URL (replacing the words PLACEID and KEY with the Place ID and API key):' mod='lggooglereviews'}
                        <a href="https://maps.googleapis.com/maps/api/place/details/json?placeid=PLACEID&key=APIKEY" target="_blank">https://maps.googleapis.com/maps/api/place/details/json?placeid=PLACEID&key=APIKEY</a>
                    {/if}
                    
                    <div class="alert alert-warning">
                        <p>{l s='In case you get an erroneous response, contact the module\'s technical support.' mod='lggooglereviews'}</p>
                    </div>
                </li>
                <li>{l s='In the event that the client makes changes to the configuration of the Google Maps API Key, it is necessary to go to the Place tab, click on modify place and save the configuration. It is the only way that the module can update the information and check again if the site is validated or not.' mod='lggooglereviews'}</li>
            </ol>
        </section>
        <div class="clearfix"></div>
    </div>
</div>