{if $kb_effect_type eq 'christmas'}
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
{/if}
<script>
    var kb_effect_type = '{$kb_effect_type}';
    var kb_snowfall_flake_color = '{$kb_snowfall_flake_color}';

    var kb_snowfall_flake_size = '{$kb_snowfall_flake_size}';
    var kb_snowfall_number_flakes = '{$kb_snowfall_number_flakes}';
    var kb_snowfall_min_speed = '{$kb_snowfall_min_speed}';
    var kb_snowfall_max_speed = '{$kb_snowfall_max_speed}';
    var kb_snowfall_disable_time = '{$kb_snowfall_disable_time}';

    var kb_flurry_character = '{$kb_flurry_character}';
    var kb_flurry_height = '{$kb_flurry_height}';
    var kb_flurry_frequency = '{$kb_flurry_frequency}';
    var kb_flurry_speed = '{$kb_flurry_speed}';
    var kb_flurry_min_size = '{$kb_flurry_min_size}';
    var kb_flurry_max_size = '{$kb_flurry_max_size}';
    var kb_flurry_wind_drift = '{$kb_flurry_wind_drift}';
    var kb_flurry_wind_variance = '{$kb_flurry_wind_variance}';
    var kb_flurry_rotation = '{$kb_flurry_rotation}';
    var kb_flurry_rotation_variance = '{$kb_flurry_rotation_variance}';

    var kb_letitsnow_falltime = '{$kb_letitsnow_falltime}';
    var kb_letitsnow_speed = '{$kb_letitsnow_speed}';
    var kb_letitsnow_max_count = '{$kb_letitsnow_max_count}';
    var kb_letitsnow_max_size = '{$kb_letitsnow_max_size}';
    var kb_letitsnow_min_size = '{$kb_letitsnow_min_size}';

    var kb_christmas_min_size = '{$kb_christmas_min_size}';
    var kb_christmas_max_size = '{$kb_christmas_max_size}';
    var kb_christmas_fallTimeMultiplier = '{$kb_christmas_fallTimeMultiplier}';
    var kb_christmas_fallTimeDifference = '{$kb_christmas_fallTimeDifference}';
    var kb_christmas_spawnInterval = '{$kb_christmas_spawnInterval}';

</script>
{if $is_enabled_extra_effect == 1}
   {if $extra_effect_type == 1}
        <img src="{$image_path}/happy-new-year_1.gif" class="move_top_right move_top  move_left" id="move" style="z-index: 999999;"/> {*Variable contains html content(special Character), escape not required*}

        <style>
        body{
            overflow-x: hidden;
        }
        #move{
            bottom:0;
            position:fixed;
            -webkit-animation:linear infinite alternate;
            -webkit-animation-name: move_left;
            -webkit-animation-duration: 5s;
        }   
        #move.move_top_right 
        {
            right:0;
            -webkit-animation-name: move_top;
        }
        #move.move_top {
            -webkit-animation-name: move_top;
        }
        @-webkit-keyframes move_left {
            0% { left: 0%;}
            50%{ left : 78%;}
            100%{ left: 0%;}
        }
        @-webkit-keyframes move_top {
            0% { bottom: 0%;}
            50%{ bottom : 78%;}
            100%{ bottom: 0%;}
        }

        </style>
        {elseif $extra_effect_type == 2}
            <img src="{$image_path}/happy-new-year_11.gif" class="move_left" id="move" style="z-index: 999999;"/>	   {*Variable contains html content(special Character), escape not required*}

            <style>
            body{    overflow-x: hidden;}
            #move{
                bottom:0;
                position:fixed;
                -webkit-animation:linear infinite alternate;
                -webkit-animation-name: move_left;
                -webkit-animation-duration: 10s;
            }   
            #move.move_top_right {
                right:0;
                -webkit-animation-name: move_top;
            }
            #move.move_top {
                -webkit-animation-name: move_top;
            }
            @-webkit-keyframes move_left {
                0% { left: 0%;}
                50%{ left : 78%;}
                100%{ left: 0%;}
            }
            @-webkit-keyframes move_top {
                0% { bottom: 0%;}
                50%{ bottom : 78%;}
                100%{ bottom: 0%;}
            }

            </style>
        {elseif $extra_effect_type == 3}
            <img src="{$image_path}/happy-new-year_10.png" class="bigImage move_top" id="move" style="z-index: 999999;"/>	  {*Variable contains html content(special Character), escape not required*}

            <style>
            .bigImage {
                max-width: 300px;
            }
            body{    overflow-x: hidden;}
            #move{
                bottom:0;
                position:fixed;
                -webkit-animation:linear infinite alternate;
                -webkit-animation-name: move_left;
                -webkit-animation-duration: 10s;
            }   
            #move.move_top_right {
                right:0;-webkit-animation-name: move_top;
            }
            #move.move_top {
                -webkit-animation-name: move_top;
            }
            @-webkit-keyframes move_left {
                0% { left: 0%;}
                50%{ left : 78%;}
                100%{ left: 0%;}
            }
            @-webkit-keyframes move_top {
                0% { bottom: 0%;}
                50%{ bottom : 78%;}
                100%{ bottom: 0%;}
            }

            </style>
        {elseif $extra_effect_type == 4}
            	<img src="{$image_path}/happy-new-year_2.png" class="bigImage move_top" id="move" style="z-index: 999999;"/>	  {*Variable contains html content(special Character), escape not required*}
	<img src="{$image_path}/bottom.png" class="bottom_image" style="z-index: 999999;"/>	  {*Variable contains html content(special Character), escape not required*}

            <style>
            .bigImage {
                max-width: 300px;
            }
            body{    overflow-x: hidden;}
            #move{
                bottom:0;
                position:fixed;
                -webkit-animation:linear infinite;
                -webkit-animation-name: move_top;
                -webkit-animation-duration: 10s;
            } 

            @-webkit-keyframes move_top {
                0% { bottom: -100%;}
                100%{ bottom : 100%;}
            }
            .bottom_image {
                position: fixed;
                bottom: 0;
                z-index: 999;
            }
            </style>
        {elseif $extra_effect_type == 5}
            	<img src="{$image_path}/happy-new-year_3.png" class="bigImage_watch bigwatchMove_top_left_mid bigwatchMove_top_right_mid" id="move" style="z-index: 999999;"/>	  {*Variable contains html content(special Character), escape not required*}

                <style>
                .bigImage {
                    max-width: 300px;
                }
                body{ 
                    overflow-x: hidden;
                }
                .bigImage_watch{
                    bottom:0;
                    position:fixed;
                    -webkit-animation:linear infinite alternate;
                    -webkit-animation-name: bigwatchMove_left;
                    -webkit-animation-duration: 5s;
                        left:10px;
                }   

                .bigImage_watch.bigwatchMove_top_left_mid {
                    -webkit-animation-name: bigwatchMove_top_mid;
                }
                .bigImage_watch.bigwatchMove_top_right_mid {
                    right:0;left:inherit
                }
                .bigImage_watch.bigwatchMove_top {
                    -webkit-animation-name: bigwatchMove_top;
                }

                @-webkit-keyframes bigwatchMove_top_mid {
                    0% { top: -10%;}
                    50%{ top : 0%;}
                    100%{ top: -10%;}
                }
                .bigImage_watch {
                    max-width: 166px;
                }
                </style>
        {elseif $extra_effect_type == 6}
            <img src="{$image_path}/happy-new-year_5.gif" class="bottom_image_center shamp_bottle" style="z-index:999999;"/>	  {*Variable contains html content(special Character), escape not required*}
            <img src="{$image_path}/bottom.png" class="bottom_image" style="z-index: 999999;"/> {*Variable contains html content(special Character), escape not required*}	  
            <style>
            .bottom_image_center{
                    position: fixed;
                bottom: 0;
                z-index: 999;
                    left: 0;
                right: 0;
                margin: 0 auto;
            }
            .shamp_bottle {
                max-width: 220px;
                    -webkit-animation: cssAnimation 5s forwards; 
                animation: cssAnimation 5s forwards;
            }
            .bottom_image {
                position: fixed;
                bottom: 0;
                z-index: 999;
            }
            @keyframes cssAnimation {
                0%   {
                    bottom: -10%;
                }
                    20%  {
                        bottom: 0%;
                    }
                90%  {
                    bottom: 0;
                }
                100% {
                    bottom: -100%;
                }
            }
            @-webkit-keyframes cssAnimation {
               0%   {
                   bottom: -10%;
               }
                    20%  {
                        bottom: 0%;
                    }
                90%  {
                    bottom: 0;
                }
                100% {
                    bottom: -100%;
                }
            }

            .animated{
                animation-duration:1s;animation-fill-mode:both
            }
            @keyframes bounceInUp{
            0%,60%,75%,90%,to{
                animation-timing-function:cubic-bezier(.215,.61,.355,1)}0%{
                transform:translate3d(0,3000px,0)}60%{
                transform:translate3d(0,-20px,0)}75%{
                transform:translate3d(0,10px,0)}90%
            transform:translate3d(0,-5px,0)
                }
                to{
                    transform:translateZ(0)
                }
            }
                animation-name:bounceInUp
                }
            </style>
        {elseif $extra_effect_type == 7}
            <img src="{$image_path}/happy-new-year_6.gif" class="bottom_image_center shamp_glass " style="z-index: 999999;"/>	  {*Variable contains html content(special Character), escape not required*}
	<img src="{$image_path}/happy-new-year_7.png" class="bottom_image_right bumper_gift" style="z-index: 999999;"/>	  {*Variable contains html content(special Character), escape not required*}
	<img src="{$image_path}/happy-new-year_9.png" class="animated infinite pulse bottom_image_left open_shamp" style="z-index: 999999;"/>	  {*Variable contains html content(special Character), escape not required*}
	<img src="{$image_path}/bottom.png" class="bottom_image" style="z-index: 999999;"/>	  {*Variable contains html content(special Character), escape not required*}

        <style>
        .bottom_image_right{
                left:inherit;
                right:0;

        }
        .bottom_image_left{
                left:0;
                right:inherit
        }
        .bottom_image_center{
        left: 0;
            right: 0;
        }
        .shamp_glass {
                position: fixed;
            bottom: -30px;
            z-index: 999;	
            margin: 0 auto;
            max-width: 120px;
                -webkit-animation: cssAnimation 15s forwards; 
            animation: cssAnimation 15s forwards;
        }
        .bottom_image {
            position: fixed;
            bottom: 0;
            z-index: 999;
        }
        @keyframes cssAnimation {
            0%   {
                bottom: -50%;
            }
                20%  {
                    bottom: -5%;
                }
            90%  {
                bottom: -5%;
            }
            100% {
                bottom: -100%;
            }
        }
        @-webkit-keyframes cssAnimation {
           0%   {
               bottom: -50%;
           }
                20%  {
                    bottom: -5%;
                }
            90%  {
                bottom: -5%;
            }
            100% {
                bottom: -100%;
            }
        }
        .bumper_gift {
            max-width: 140px;
            position: fixed;
            bottom: 0;
                -webkit-animation:linear infinite alternate;
            -webkit-animation-name: move_top;
            -webkit-animation-duration: 10s;
        }


        @-webkit-keyframes move_top {
            0% { bottom: -10%;}
            100%{ bottom : 10%;}
        }
        .open_shamp{
                max-width: 140px;
            position: fixed;
            bottom: 0;
                right:0;
                z-index: 9999;
        }
        .animated{
            animation-duration:1s;animation-fill-mode:both}.animated.infinite{
            animation-iteration-count:infinite
            }
        @keyframes pulse{
            0%{
                transform:scaleX(1)}50%{
                transform:scale3d(1.05,1.05,1.05)
            }
            to
        {
            transform:scaleX(1)
        }
        }
        .pulse{
            animation-name:pulse
        }
        </style>
{/if}
{/if}
{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Admin tpl file
*}