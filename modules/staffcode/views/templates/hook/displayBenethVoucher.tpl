{*
 * File generated with module files generator by SzpaQ <dev-bot>
 * @author SzpaQ
 * @copyright 2021 SzpaQ
 * @module staffcode (Staff Code)
 * @version 0.0.1
 * @license All Rights Reserved
 *}
<style>

.block-staff-code {
    padding: 0;
}
.block-staff-code .staff-code-area{
    padding: 1rem;
    background: hsla(0,0%,80%,.15);
}

</style>
<div class="block-staff-code">
    {if $staff_code_error}
        <div class="alert alert-danger js-error" role="alert" style="display: block;">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            <span class="m-l-1 js-error-text">{$staff_code_error}</span>
        </div>
    {/if}
    {if $staff_code}
        <ul class="promo-name card-body">
            <li class="cart-summary-line">
                <span class="label">{l s='Staff Code' mod='staffcode'}:</span>
                <div class="pull-right">
                    <span style="text-transform:uppercase;font-weight:bold">{$staff_code->code}</span>
                    <a href="{$link->getModuleLink('staffcode', 'code', ['delete'=>1])}"> <i class="fa fa-trash-o" aria-hidden="true"></i></a>
                </div>
            </li>
        </ul>
    {else}
    <div class="staff-code">
        <div class="staff-code-area">
            <div class="staff-code-input">
                <form action="{$link->getModuleLink('staffcode', 'code')}" method="post" class="">
                    <div class="input-group">
                        <i class="fa fa-user btn voucher-icon" aria-hidden="true"></i>
                        <input type="hidden" name="back_url" value="{$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}" onload="">
                        <input type="hidden" name="addCode" value="1">
                        <input class="form-control" type="text" name="staff_code" placeholder="{l s='Staff Code' mod='staffcode'}" maxlength="5">
                        <button type="submit" class="btn btn-secondary">
                        <span>{l s='Add' mod='staffcode'}</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {/if}
</div>
