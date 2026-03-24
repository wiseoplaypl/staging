<nav id="hmd-modal" class="col-lg-3 hmd-sidebar-right hmd-sidebar-animate text-sm-center">
    <div class="hmd-container">
        <div class="hmd-header">
            <a href="#" class="hmd-dismiss-modal">×</a>
            <h2>Help</h2>
        </div>
        <div class="hmd-content">
            <div class="hmd-item" data-doc="debugMode">
                <h2>{l s='Enable Debug Mode' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='This option will display popups on each event response with the data being tracked.' mod='higoogleanalytics'}</p>
                    <p>{l s='Use this option only for debugging to see if the events are being fired properly and make sure to keep it disabled when you finished configuring the module.' mod='higoogleanalytics'}</p>

                    <div class="row hmd-images-block">
                        <div class="col-lg-6">
                            <a href="{$moduleAssetsDir|escape:'htmlall':'UTF-8'}add-to-cart-event.jpg" class="hmd-image-item" target="_blank">
                                <img src="{$moduleAssetsDir|escape:'htmlall':'UTF-8'}add-to-cart-event.jpg">
                                <span>{l s='Add to Cart event' mod='higoogleanalytics'}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="enableGa4Tracking">
                <h2>{l s='Enable Google Analytics 4 tracking' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='When this option is enabled the module will use Google Analytics 4 tracking.' mod='higoogleanalytics'}</p>
                    <p>{l s='Make sure to add the measurement ID for Google Analytics 4 property bellow.' mod='higoogleanalytics'}</p>    
                </div>
            </div>
            
            <div class="hmd-item" data-doc="ga4MeasurementId">
                <h2>{l s='Google Analytics 4 measurement ID' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='If you want to use Google Analytics 4 tracking, you\'ll need to create GA4 property and use here the measurement ID from property.' mod='higoogleanalytics'}</p>
                    <p>{l s='Check the bellow video for detailed instructions on how to create GA4 property and get the measurement ID.' mod='higoogleanalytics'}</p>
                    <a href="https://www.youtube.com/watch?v=_CKAg2mMwVc" target="_blank">https://www.youtube.com/watch?v=_CKAg2mMwVc</a>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="enableUaTracking">
                <h2>{l s='Enable Universal Analytics tracking' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='When this option is enabled the module will use Universal Analytics (Goolge Analytics 3) tracking.' mod='higoogleanalytics'}</p>
                    <p>{l s='Make sure to add the measurement ID for Universal Analytics property bellow.' mod='higoogleanalytics'}</p>    
                </div>
            </div>
            
            <div class="hmd-item" data-doc="uaTrackingId">
                <h2>{l s='Universal Analytics measurement ID' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='If you want to use Universal Analytics (Google Analytics 3) tracking, you\'ll need to create UA property and use here the measurement ID from property.' mod='higoogleanalytics'}</p>
                    <p>{l s='Check the bellow video for detailed instructions on how to create UA property and get the measurement ID.' mod='higoogleanalytics'}</p>
                    <a href="https://www.youtube.com/watch?v=_CKAg2mMwVc" target="_blank">https://www.youtube.com/watch?v=_CKAg2mMwVc</a>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="includeTaxes">
                <h2>{l s='Include taxes in conversion value' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='When this option is enabled, all prices for the conversion value will be calculated including taxes.' mod='higoogleanalytics'}</p>
                    <p>{l s='If you choose to include shipping and / or gift wrapping costs in conversion value and this option is enabled, the shipping and wrapping costs will be calculated taxes included too.' mod='higoogleanalytics'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="includeShipping">
                <h2>{l s='Include shipping cost in conversion value' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='If this option is enabled the shipping cost will be included in conversion value, otherwise it\'ll be excluded.' mod='higoogleanalytics'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="includeWrapping">
                <h2>{l s='Include gift wrapping cost in conversion value' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='If this option is enabled the gift wrapping cost will be included in conversion value, otherwise it\'ll be excluded.' mod='higoogleanalytics'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="includeProductTaxes">
                <h2>{l s='Include taxes for product prices' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='When this option is enabled, product prices will be tracked taxes included.' mod='higoogleanalytics'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="deductDiscount">
                <h2>{l s='Deduct discount amount from conversation value' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='When this option is enabled, the discount amount will be deducted from order total.' mod='higoogleanalytics'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="deductWholesalePrice">
                <h2>{l s='Deduct Cost price from conversation value' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='When this option is enabled, the wholesale price of products will be subtracted from the conversion value.' mod='higoogleanalytics'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="refundedOrderStates">
                <h2>{l s='Refunded order status(es)' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='Here you can set all orders states that you use for refunds.' mod='higoogleanalytics'}</p>
                    <p>{l s='Our module will use these statuses to track refund events.' mod='higoogleanalytics'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="partialRefundedOrderStates">
                <h2>{l s='Partially refunded order status(es)' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='Here you can set all orders states that you use for partial refunds.' mod='higoogleanalytics'}</p>
                    <p>{l s='Our module will use these statuses to track partial refund events.' mod='higoogleanalytics'}</p>
                </div>
            </div>

            <div class="hmd-item" data-doc="cleanDb">
                <h2>{l s='Clean Database when module uninstalled' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='We recommend to keep this option disabled. If you enable it, after uninstalling the module all data related to this module will be deleted from database.' mod='higoogleanalytics'}</p>
                    <p>{l s='This option can be used if for some reason you don\'t want to use the module anymore or you need to reset all settings to defaults.' mod='higoogleanalytics'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="eventActive">
                <h2>{l s='Active' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='When enabled, the event will be live and start collection data.' mod='higoogleanalytics'}</p>
                    <p>{l s='Later you can Enable / Disable any events.' mod='higoogleanalytics'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="eventActionType">
                <h2>{l s='Action Type' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='Here you can select the action when you want to track the event.' mod='higoogleanalytics'}</p>
                    <p>{l s='There are 2 available options:' mod='higoogleanalytics'}</p>
                    <p>{l s='Click - The event fires when user clicks on the element.' mod='higoogleanalytics'}</p>
                    <p>{l s='Scroll - The event fires when user sees the element.' mod='higoogleanalytics'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="eventSelector">
                <h2>{l s='Selector' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='Here you can enter class name, ID or HTML element' mod='higoogleanalytics'}</p>
                    <p>{l s='Please make sure to add a .(dot) before class name and #(hash) before ID selectors' mod='higoogleanalytics'}</p>
                    <p>{l s='You can add multiple selectors, comma separated' mod='higoogleanalytics'}</p>
                    <p>{l s='Example:' mod='higoogleanalytics'}</p>
                    <pre>.products-section-title, #custom-text</pre>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="eventCategory">
                <h2>{l s='Event Category' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='Typically the object that was interacted with' mod='higoogleanalytics'}</p>
                    <p>{l s='(e.g. \'CTA click\')' mod='higoogleanalytics'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="eventAction">
                <h2>{l s='Event Action' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='The type of interaction' mod='higoogleanalytics'}</p>
                    <p>{l s='(e.g. \'click\')' mod='higoogleanalytics'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="eventLabel">
                <h2>{l s='Event Label' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='Useful for categorizing events' mod='higoogleanalytics'}</p>
                    <p>{l s='(e.g. \'Subscribe now\')' mod='higoogleanalytics'}</p>
                    <p>{l s='This is an optional field.' mod='higoogleanalytics'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="eventValue">
                <h2>{l s='Event Value' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='A numeric value associated with the event' mod='higoogleanalytics'}</p>
                    <p>{l s='(e.g. 16)' mod='higoogleanalytics'}</p>
                    <p>{l s='Please note: This should be only numeric value.' mod='higoogleanalytics'}</p>
                    <p>{l s='This is an optional field.' mod='higoogleanalytics'}</p>
                </div>
            </div>
            
            <div class="hmd-item" data-doc="eventShop">
                <h2>{l s='Select Shops' mod='higoogleanalytics'}</h2>

                <div class="hmd-item-content">
                    <p>{l s='Here you can select all shops you want this event to be available.' mod='higoogleanalytics'}</p>
                    <p>{l s='You can choose a single shop or multiple shops if applicable' mod='higoogleanalytics'}</p>
                </div>
            </div>
            
        </div>

        <div class="hmd-footer">
            {l s='Feel free to [1]Contact Us[/1] if you need further assistance.' tags=["<a href='{$contactLink}' target='_blank'>"] mod='higoogleanalytics'}
        </div>
    </div>
</nav>