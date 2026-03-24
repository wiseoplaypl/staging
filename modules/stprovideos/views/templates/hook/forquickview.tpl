<script>

    {if isset($video_data)}
    var stprovidequickview = {$video_data|json_encode nofilter};
    if(typeof(st_pro_videos)!='undefined')
        st_pro_videos.init();
    {else}
        var stprovidequickview = false;
    {/if}



</script>
