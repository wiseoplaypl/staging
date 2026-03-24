{*
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 *}
{$cronKey = Configuration::get('module-webpconverter-cron-key')}
<div class="panel kpi-container">
    <div class="panel-heading">
        <i class="icon-clock-o"></i>&nbsp;&nbsp;{l s='Setting a cron job' mod='webpgenerator'}
    </div>
    <div class="kpi-refresh"></div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <h2>{l s='Setting up the cron job through the server - (Recommended)' mod='webpgenerator'}</h2>
        </div>
        <div class="alert alert-info col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <p>{l s='There are many servers and hosting management systems, but this guide will help you to set up the Cron Job. In order to set up the Cron Job, you need to open the page where you manage your domain.' mod='webpgenerator'}</p>
            <p>{l s='Once you have entered the management interface you should look for something like Cron Jobs or Scheduled Tasks. Now it\'s time to enter the schedule and the command.' mod='webpgenerator'}</p>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <hr>

            <h3 class="modal-title text-info">{l s='What\'s a Cron Job?' mod='webpgenerator'}</h3>
            <p>
                <strong>{l s='A cron job is a task that repeats every X time' mod='webpgenerator'}</strong>
            </p>
            <p>{l s='The cron job can be divided in three parts the schedule and the command and the output' mod='webpgenerator'}</p>
            <p>
                <span class="badge">
                    {l s='Schedule' mod='webpgenerator'}
                </span>
                +
                <span class="badge">
                    {l s='Command' mod='webpgenerator'}
                </span>
                +
                <span class="badge">
                    {l s='Output' mod='webpgenerator'}
                </span>
            </p>
            <br>
            <p>
                <u>{l s='For example, this Cron Job:' mod='webpgenerator'}</u>
            </p>
            <code>
                <strong>
                    50 * * * * curl -l -k "{Context::getContext()->link->getModuleLink('webpgenerator','cron', ['key' => $cronKey])}"
                    >/dev/null 2>&1
                </strong>
            </code>
            <p>
                {l s='Will generate the images at minute 50 of every hour for every day and month indefinitely (0:50, 1:50, 2:50...)' mod='webpgenerator'}
            </p>

            <hr>

            <h3 class="modal-title text-info">{l s='Schedule - How does the schedule work?' mod='webpgenerator'}</h3>
            <p>{l s='The schedule tells the crob how often the command or script has to be executed.' mod='webpgenerator'}</p>
            <p>{l s='In some systems, you will have to choose among prefedined options like once a day, every hour, every 3 hours... but in most cases, you will be able to choose the programmation yourself. To set up the schedule you will find 5 parameters to configure.' mod='webpgenerator'}</p>

            <p>
                <span>┌---------------- {l s='minute (0 - 59)' mod='webpgenerator'}</span>
                <br>
                <span>|&nbsp;┌------------- {l s='hour (0 - 23)' mod='webpgenerator'}</span>
                <br>
                <span>|&nbsp;|&nbsp;┌------------- {l s='day of month (1 - 31)' mod='webpgenerator'}</span>
                <br>
                <span>|&nbsp;|&nbsp;|&nbsp;┌------------- {l s='month (1 - 12)' mod='webpgenerator'}</span>
                <br>
                <span>|&nbsp;|&nbsp;|&nbsp;|&nbsp;┌------------- {l s='day of week (0 - 6), Sunday = 0 or 7' mod='webpgenerator'}</span>
                <br>
                <span>|&nbsp;|&nbsp;|&nbsp;|&nbsp;|</span>
                <br>
                <span>*&nbsp;*&nbsp;*&nbsp;*&nbsp;*</span>
            </p>

            <p>
                <span>{l s='In each field you can set up a number or an asterisk.' mod='webpgenerator'}</span>
                <br>
                <span>{l s='The asterisk has the special meaning "every" ("every hour", "every minute"...)' mod='webpgenerator'}</span>
                <br>
                <span>{l s='You can also modify the asterisk operator by adding a slash and a number after it to set an interval.' mod='webpgenerator'}</span>
                <br>
                <span>{l s='To make it easier for you to understand. Here are the most common schedules to generate the missing WebP images:' mod='webpgenerator'}</span>
            </p>

            <table class="table">
                <thead>
                <tr>
                    <th>{l s='Schedule' mod='webpgenerator'}</th>
                    <th>{l s='What it does?' mod='webpgenerator'}</th>
                    <th>{l s='Will be executed at...' mod='webpgenerator'}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>0 * * * *</td>
                    <td>{l s='Generate the missing WebP images each hour at minute 0' mod='webpgenerator'}</td>
                    <td>(0:00, 1:00, 2:00...)</td>
                </tr>
                <tr>
                    <td>0 */3 * * *</td>
                    <td>{l s='Generate the missing WebP images every 3 hours at minute 0' mod='webpgenerator'}</td>
                    <td>(0:00, 3:00, 6:00...)</td>
                </tr>
                <tr>
                    <td>0 */6 * * *</td>
                    <td>{l s='Generate the missing WebP images every 6 hours at minute 0' mod='webpgenerator'}</td>
                    <td>(0:00, 6:00, 12:00, 18:00, 24:00)</td>
                </tr>
                <tr>
                    <td>0 */12 * * *</td>
                    <td>{l s='Generate the missing WebP images twice a day at minute 0' mod='webpgenerator'}</td>
                    <td>(0:00, 12:00)</td>
                </tr>
                <tr>
                    <td>0 0 * * *</td>
                    <td>{l s='Generate the missing WebP images once a day at minute 0' mod='webpgenerator'}</td>
                    <td>(0:00)</td>
                </tr>
                </tbody>
            </table>
            <br>
            <p>{l s='This behaviour can also be applied to the other modifiers, although you probably won\'t need to use it.' mod='webpgenerator'}</p>

            <hr>

            <h3 class="modal-title text-info">{l s='Schedule - How does the schedule work?' mod='webpgenerator'}</h3>
            <p>{l s='Once we know what schedule will be using, the next step is to call the script that will generate the missing WebP images. Depending on the system you use you will be asked for a URL or a command.' mod='webpgenerator'}</p>
            <p>{l s='Here you will find all the options, you just need to click on the one you need to use to copy it to your clipboard.' mod='webpgenerator'}</p>

            <hr>

            <h4>
                <strong>{l s='Cron URL' mod='webpgenerator'}</strong>
                <a class="link_copy badge"
                   data-url='{Context::getContext()->link->getModuleLink('webpgenerator','cron', ['key' => $cronKey])}'>
                    {l s='Click to copy the URL' mod='webpgenerator'}
                </a>
            </h4>
            <code>{Context::getContext()->link->getModuleLink('webpgenerator','cron', ['key' => $cronKey])}</code>

            <hr>

            <h4>
                <strong>{l s='The command' mod='webpgenerator'}</strong>
            </h4>

            {if $smarty.const._PS_VERSION_ !== null && $smarty.const._PS_VERSION_|substr:0:3 == "1.7"}
                <p>{l s='If you can use a command, you have two options:' mod='webpgenerator'}</p>
            {/if}
            <br>
            <h5>
                <strong>{l s='Use CURL: (recommended method)' mod='webpgenerator'}</strong>
                <a class="link_copy badge"
                   data-url='curl -l -k "{Context::getContext()->link->getModuleLink('webpgenerator','cron', ['key' => $cronKey])}" >/dev/null 2>&1'>
                    {l s='Click to copy the curl command' mod='webpgenerator'}
                </a>
            </h5>
            <code>
                curl -l -k "{Context::getContext()->link->getModuleLink('webpgenerator','cron', ['key' => $cronKey], ['key' => $cronKey])}" >/dev/null 2>&1
            </code>

            {if $smarty.const._PS_VERSION_ !== null && $smarty.const._PS_VERSION_|substr:0:3 == "1.7"}
                <h5>
                    <strong>{l s='Use the terminal (from the prestashop folder)' mod='webpgenerator'}</strong>
                    <a class="link_copy badge"
                       data-url='bin/console webp-generator:cron'>{l s='Click to copy the terminal command' mod='webpgenerator'}</a>
                </h5>
                <code>bin/console webp-generator:cron</code>
            {/if}
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.link_copy', function () {
        if (copyToClipboard($(this).data('url')) && typeof toastr != 'undefined') {
            toastr.success("Feed URL successfully copied to clipboard!");
        }
    });

    function copyToClipboard(text) {
        var inputc = document.body.appendChild(document.createElement("input"));
        inputc.value = text;
        inputc.focus();
        inputc.select();
        document.execCommand('copy');
        inputc.parentNode.removeChild(inputc);
        return true;
    }

</script>