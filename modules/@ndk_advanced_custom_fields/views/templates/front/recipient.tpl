<table style="text-align:justify;">
	
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr>
		<td width="15%">&nbsp;</td>
		<td width="70%" style="font-size:30px; text-align:center; padding-top:20px;">{l s='A gift for you ' mod='ndk_advanced_custom_fields'}</td>
		<td width="15%">&nbsp;</td>
	</tr>
	
	
	
	<tr>
		<td width="15%">&nbsp;</td>
		<td width="70%" style="font-size:20px; text-align:center; padding-top:20px;">{l s='For' mod='ndk_advanced_custom_fields'} {$recipient->firstname} {$recipient->lastname} </td>
		<td width="15%">&nbsp;</td>
	</tr>
	<tr>
		<td width="15%">&nbsp;</td>
		<td width="70%" style="font-size:20px; text-align:center; ">{l s='gift offer by' mod='ndk_advanced_custom_fields' } {if $recipient->who_offers == ''}{$customer->firstname} {$customer->lastname}{else}{$recipient->who_offers}{/if}</td>
		<td width="15%">&nbsp;</td>
	</tr>
	
	<tr><td colspan="3">&nbsp;</td></tr>
	<tr><td colspan="3">&nbsp;</td></tr>
	
	
	<tr>
		<td width="15%">&nbsp;</td>
		<td width="70%" style="text-align:center; ">
			{l s='It contains'  mod='ndk_advanced_custom_fields'} <br/>
			<span  style="font-size:20px; text-align:center; ">{$recipient->title}</span><br/>
			{$recipient->details} 
		</td>
		<td width="15%">&nbsp;</td>
	</tr>
	{if $image_link !=''}
	<tr>
		<td width="15%">&nbsp;</td>
		<td width="70%"><img src="{$image_link}"/></td>
		<td width="15%">&nbsp;</td>
	</tr>
	{/if}
	<tr>
		<td width="15%">&nbsp;</td>
		<td width="70%" style="text-align:center; font-size:20px; font-weight:bold;">{$recipient->message}</td>
		<td width="15%">&nbsp;</td>
	</tr>
	
	<tr><td colspan="3">&nbsp;</td></tr>
	
	<tr>
		<td width="15%">&nbsp;</td>
		<td width="70%" style="text-align:center;">
			{l s='This code is available from' mod='ndk_advanced_custom_fields'} {$availability}
		</td>
		<td width="15%">&nbsp;</td>
	</tr>
	
	<tr><td colspan="3">&nbsp;</td></tr>
	
	<tr>
		<td width="30%">&nbsp;</td>
		<td width="40%" style="padding:15px; text-align:center; border:3px solid #000;font-size:20px">
			<p>{l s='CODE : '  mod='ndk_advanced_custom_fields'}{$recipient->code}<br/>
			{l s='ORDER N° : '  mod='ndk_advanced_custom_fields'}{$order->reference}</p>
		</td>
		<td width="30%">&nbsp;</td>
	</tr>
	
	<tr><td colspan="3">&nbsp;</td></tr>
	
</table>


