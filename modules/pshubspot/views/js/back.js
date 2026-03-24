/**
 * 2007-2024 Tiralineas
 * NOTICE OF LICENSE
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * @author    Ander <ander@tiralineas.digital>
 * @copyright 2007-2024 Tiralineas
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of Tiralineas
 */
class tiralineasHsImporter {
    constructor(endpoint) {
        this.endpoint = endpoint;
    }
    
    processChunk(object_type) {
        return fetch(this.endpoint+'&object_type='+object_type, {
            method: "get",
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
        });
    }
    async start(object_type, callback) {
        let json = JSON.parse("{}");
        while(!json.done){
            let response = await this.processChunk(object_type);
            if (response.ok) { 
                json = await response.json();
                callback(json);
            }
        }
    }
}

function tiralineas_check_date_filter_enabled() {
    document.getElementById('PS_HUBSPOT_DEAL_DATE_FILTER').disabled = !document.getElementById(
        'PS_HUBSPOT_DEAL_DATE_FILTER_ENABLED').checked;
}
