{*
*
* DISCLAIMER
*
* Do not edit or add to this file.
* You are not authorized to modify, copy or redistribute this file.
* Permissions are reserved by FME Modules.
*
*  @author    FMM Modules
*  @copyright FME Modules 2021
*  @license   Single domain
*}

<form action="{$form_action}" method="post" enctype="multipart/form-data" class="well">
    {* language selection *}
    {* <div class="form-group">
      <div class="col-lg-3">
        <label class="label control-label">{l s='Select Language' mod='storelocator'}</label>
      </div>
      <div class="col-lg-7">
        <select name="csv_lang" class="form-control">
          {foreach from=$languages item=lang}
            <option value="{$lang.id_lang|escape:'htmlall':'UTF-8'}">{$lang.name|escape:'htmlall':'UTF-8'}</option>
          {/foreach}
        </select>
      </div>
    </div>
    <div class="clearfix"></div><br> *}

    <div class="hidden_fields">
      <input type="hidden" name="importStoreContacts" value="1" />
      <input type="hidden" name="nb_rows" value="{$no_of_rows|escape:'htmlall':'UTF-8'}" />
      <input type="hidden" name="nb_cols" value="{$no_of_cols|escape:'htmlall':'UTF-8'}" />
    </div>
    <table>
      <thead>
        <tr>
          {for $i = 0 to $nb_column}
            {* displaying maximum columns *}
            {if $i == $MAX_COLUMNS}
              {break}
            {/if}

            <th class="center" style="background:#F1F1F1;">
              <select name="head_{$i|escape:'htmlall':'UTF-8'}" style="background:#fff;font-weight: bold;padding: 2px;width: 100%;">
                <option value="0" selected="selected"> - </option>
                {foreach from=$fields key=$key item=value}
                  <option value="{$key|escape:'htmlall':'UTF-8'}">{$value|trim:'"'|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
              </select>
            </th>
          {/for}
        </tr>
        <tr>
          {for $i = 0 to $nb_column}
            {* displaying maximum columns *}
            {if $i == $MAX_COLUMNS}
              {break}
            {/if}
            <th class="center" style="background:#F1F1F1;padding-left: 5px">{$data['head'][$i]|trim:'"'|escape:'htmlall':'UTF-8'}</th>
          {/for}
        </tr>
      </thead>
      <tbody>
          {foreach from=$content item=line name=row}
            <tr>
              {assign var='count' value=0}
              {foreach from=$line item=value name=col}
                {if $count == $MAX_COLUMNS}
                  {break}
                {/if}
                <input type="hidden"
                  value="{$value|trim:'"'|escape:'htmlall':'UTF-8'}"
                  name="col_{$count|escape:'htmlall':'UTF-8'}[]"/>
                <td style="padding-left: 5px">{$value|trim:'"'|escape:'htmlall':'UTF-8'}</td>
                {assign var='count' value=($smarty.foreach.col.iteration)}
              {/foreach}
            </tr>
          {/foreach}
          <tr>
          </tr>
      </tbody>
      <tfoot>
      </tfoot>
      <tr>
        <td colspan="12"><hr/>
          <div class="clearfix"></div>
        </td>
      </tr>
      <tr>
        <td colspan="12">
          <div class="margin-form form-group">
            <button id="import-button" name="importStoreContacts" class="btn btn-default pull-right" type="submit">
              <i class="process-icon-next"></i>{l s='Import data' mod='storelocator'}
            </button>
          </div>
        </td>
      </tr>
    </table>
</form>