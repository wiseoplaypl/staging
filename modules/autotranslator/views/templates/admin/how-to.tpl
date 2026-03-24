{*
* 2007-2020 Amazzing
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*
*  @author    Amazzing <mail@amazzing.ru>
*  @copyright 2007-2020 Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*}

{if $provider_name == 'GoogleTranslate'}
	<ol>
		<li>{l s='Log in to [1]%s[/1]' mod='autotranslator' sprintf=['https://console.cloud.google.com/'] tags=['<span class="b">']}</li>
		<li>{l s='Click the [1]Project[/1] drop-down in top panel' mod='autotranslator' tags=['<span class="b">']}</li>
		<li>
			{l s='Clik [1]New project[/1], specify [1]Project name[/1], click [1]Create[/1]' mod='autotranslator' tags=['<span class="b">']}.
			{l s='Wait until you are redirected to dashboard' mod='autotranslator'}.
		<li>
			{l s='Click on sandwitch menu in top left corner and select [1]APIs & Services[/1]' mod='autotranslator' tags=['<span class="b">']}
			<ul>
				<li>
					{l s='Click [1]+ ENABLE APIS AND SERVICES[/1]' mod='autotranslator' tags=['<span class="b">']},
					{l s='find [1]Cloud Translation API[/1] and [1]ENABLE[/1] it' mod='autotranslator' tags=['<span class="b">']}
				</li>
				<li>
					{l s='If you don\'t have a billing account, you will be asked to create one' mod='autotranslator'}
					<ul>
						<li>
							{l s='Fill required form and specify card details' mod='autotranslator'}.
							{l s='You should get 300 USD credit' mod='autotranslator'}
						</li>
					</ul>
				</li>
			</ul>
		</li>
		<li>
			{l s='Click on sandwitch menu in top left corner and select [1]APIs & Services > Credentials[/1]' mod='autotranslator' tags=['<span class="b">']}
			<ul>
				<li>{l s='Click [1]Create credentials > API Key[/1]' mod='autotranslator' tags=['<span class="b">']}</li>
			</ul>
		</li>
	</ol>
{else if $provider_name == 'IBMTranslate'}
	<ol>
		<li>{l s='Log in to [1]%s[/1]' mod='autotranslator' sprintf=['https://cloud.ibm.com'] tags=['<span class="b">']}</li>
		<li>{l s='Go to [1]%s[/1]' mod='autotranslator' sprintf=['https://cloud.ibm.com/catalog/services/language-translator'] tags=['<span class="b">']}</li>
		<li>{l s='Select pricing plan (free plan available) and click [1]Create[/1] in right column' mod='autotranslator' tags=['<span class="b">']}</li>
		<li>{l s='Go to [1]%s[/1]' mod='autotranslator' sprintf=['https://cloud.ibm.com/resources'] tags=['<span class="b">']}</li>
		<li>{l s='Find [1]Services > Language Translator[/1] and click it' mod='autotranslator' tags=['<span class="b">']}</li>
		<li>{l s='Select [1]Manage[/1] tab in left column and there you will find [1]API Key[/1] and [1]URL[/1]' mod='autotranslator' tags=['<span class="b">']}</li>
	</ol>
{else if $provider_name == 'MicrosoftTranslate'}
	<ol>
		<li>
			{l s='Log in to [1]%s[/1]' mod='autotranslator' sprintf=['https://ms.portal.azure.com/'] tags=['<span class="b">']}
			({l s='You might be asked to add card details during registration' mod='autotranslator'})
		</li>
		<li>{l s='Click [1]+ Create a resource[/1]' mod='autotranslator' tags=['<span class="b">']}</li>
		<li>{l s='Find [1]Translator Text[/1], click on it and then click [1]Create[/1]' mod='autotranslator' tags=['<span class="b">']}</li>
		<li>{l s='Fill required fields and select pricing tier' mod='autotranslator'}. {l s='Each subscription has a free tier' mod='autotranslator'}.</li>
		<li>{l s='Click [1]Create[/1] to complete the process' mod='autotranslator' tags=['<span class="b">']}</li>
		<li>{l s='Click [1]Go to resource[/1] and copy [1]Key1[/1] wich is actually the [1]API Key[/1]' mod='autotranslator' tags=['<span class="b">']}</li>
	</ol>
{else if $provider_name == 'YandexTranslate'}
	<ol>
		<li>{l s='Log in to [1]%s[/1]' mod='autotranslator' sprintf=['http://api.yandex.com/key/form.xml?service=trnsl'] tags=['<span class="b">']}</li>
		<li>{l s='Add a simple Key description, accept User Agreement and click [1]Get API Key[/1]' mod='autotranslator' tags=['<span class="b">']}</li>
	</ol>
{/if}
{* since 3.0.0 *}
