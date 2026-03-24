{**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 *}
<script
    async
    data-environment='{$klarnapayment.environment|escape:"htmlall":"UTF-8"}'
    src='{$klarnapayment.sdk_url|escape:"htmlall":"UTF-8"}'
    {if $klarnapayment.is_client_id_required}
        data-client-id='{$klarnapayment.client_id|escape:"htmlall":"UTF-8"}'
    {/if}
></script>
