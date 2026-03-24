$(document).ready(function(){
    window.setTimeout(function(){
        st_pro_videos.init();
    },2000);
    if(!stprovideos.st_is_16){
        prestashop.on('updatedProduct', function (event) {
            window.setTimeout(function(){
                st_pro_videos.run({yi:0});
            },2000);
        });
        /*if(stprovideos.quick_view){
            prestashop.on('quickViewLoaded', function(event){
                var id_array = event.quickview_id.split('-');
                if(id_array.length!=4)
                    return ;
                $.post(stprovideos.get_videos_url, {'id_product': id_array[2]}).then(function (resp) {
                    if(resp.videos){
                        stprovideos.videos = resp.videos;
                        stprovideos.gallery_image_url = resp.gallery_image_url;
                        stprovideos.thumbnail_image_url = resp.thumbnail_image_url;
                        st_pro_videos.run({yi:1});
                    }
                }).fail(function (resp) {
                    console.log('stprovideos-quick-view: can\'t get videos.');
                });
            });
        }*/
    }
});

var st_pro_videos =  {
  options: {
    'autoplay': 1,
    'muted' : 0,
    'controls_youtube' : 0,
    'controls': 1,
    'loop': 1
  },
  init: function() {
    if(typeof(stprovideos)=='undefined')
        return false;
    if(!stprovideos.st_is_16 && stprovideos.quick_view && typeof(stprovidequickview)!='undefined'){
        stprovideos.videos = stprovidequickview ? stprovidequickview.videos : undefined;
    }
    this.options = $.extend(this.options, {
        'autoplay': stprovideos.autoplay,
        'muted' : stprovideos.muted,
        'controls_youtube' : stprovideos.controls_youtube,
        'controls': stprovideos.controls,
        'loop': stprovideos.loop
    });
    if(typeof(sttheme)!='undefined' && typeof(sttheme.product_thumbnails)!='undefined' && sttheme.product_thumbnails==3)
        stprovideos.thumb_slider = 0;

    $(document).on('click','.st_pro_video_play', function(e){
        e.preventDefault();
        st_pro_videos.st_pro_manually_play($(this).data('video-id'));
        return false;
    });

    $(document).on('click','.st_pro_video_stop', function(e){
        e.preventDefault();
        st_pro_videos.st_pro_videos_remove(0);
        return false;
    });
    st_pro_videos.run({yi:1});
  },
  run: function(params){
    if(typeof(stprovideos.videos)=='undefined')
        return false;
    if(stprovideos.how_to_display==0){
        this.append_player();
        this.append_static_paly(0);
        this.handle_click_on_thumb(0);
    }else if(stprovideos.how_to_display==2){
        this.append_player();
        this.append_static_paly(1);
        this.append_paly_on_first_thumb();
        this.handle_click_on_thumb(1);
    }else if(stprovideos.how_to_display==7 || stprovideos.how_to_display==8){
        if(params.yi==1)
            this.append_desc_player();
    }else{// if(!stprovideos._st_themes_17)
        this.init_classic_pro_video(params);
    }
    this.init_st_pro_videos(params);
  },
  append_player: function(){
    var $pc_container = $(stprovideos.video_container);
    if($pc_container.length)
        $pc_container.append('<button class="st_pro_video_stop st_pro_video_btn st_pro_videos_invisible" type="button" aria-disabled="false"><span aria-hidden="true" class="vjs-icon-placeholder"></span></button>'+this.bulid_video_html(stprovideos.videos[0], false));
  },
  append_desc_player: function(){
    var $pc_container = $(stprovideos.desc_container);
    if($pc_container.length){
        var x = '<div class="st_pro_video_miaoshu">'+this.bulid_video_html(stprovideos.videos[0], false)+'</div>';
        if(stprovideos.how_to_display==8)
            $pc_container.append(x);
        else
            $pc_container.prepend(x);
    }
  },
  bulid_video_html: function(video, lazy){
    return '<div id="st_pro_video_'+video.id_st_pro_video+'" data-video-id="'+video.id_st_pro_video+'" class="st_pro_videos_box '+(stprovideos.slider ? ' st_pro_videos_box_static ' : ' st_pro_videos_invisible ')+(stprovideos.controls_youtube ? ' st_pro_videos_controls_youtube ' : '')+' st_pro_video_flex"><video class="st_pro_videos video-js vjs-big-play-centered" data-autoplay="'+video.autoplay+'" data-muted="'+video.muted+'" data-loop="'+video.loop+'" controls style="width:100%; height: auto"><source '+(lazy?'data-':'')+'src="'+video.url+'" type="video/'+(/youtu(\.be|be\.com)/.test(video.url) ? 'youtube' : 'mp4')+'"></video></div>';
  },
  append_static_paly: function(type){
    var $container = $(stprovideos.video_container);
    if($container.length)
        $container.append('<button class="st_pro_video_play '+(type ? 'st_pro_video_play_on_first_gallery' : 'st_pro_video_play_static')+' st_pro_video_btn '+(stprovideos.how_to_display==2 ? ' st_pro_videos_invisible ' : '')+'" type="button" aria-disabled="false" data-video-id="'+stprovideos.videos[0].id_st_pro_video+'"><span aria-hidden="true" class="vjs-icon-placeholder"></span>'+(stprovideos.play_video_text ? '<span class="st_play_video_text">'+stprovideos.play_video_text+'</span>' : '')+'</button>');

  },
  /*append_paly_on_first: function(){
    var $container = $(stprovideos.video_selector);
    if($container.length)
        $container.append('<button class="st_pro_video_play st_pro_video_play_on_first st_pro_video_btn" type="button" aria-disabled="false"><span aria-hidden="true" class="vjs-icon-placeholder"></span></button>');
  },*/
  append_paly_on_first_thumb: function(){
    var $container = $(stprovideos.thumbnail_selector).first();
    if(!$container.length)
        return false;
    if(typeof(stprovideos.videos)!=='undefined')
        $container.addClass('st_pro_video_relative').append('<button class="st_pro_video_play_on_first_thumb st_pro_video_btn st_pro_video_play_icon" type="button" aria-disabled="false"><span aria-hidden="true" class="vjs-icon-placeholder"></span></button>');
  },
  handle_click_on_thumb: function(type){
    if(stprovideos.how_to_display!=0 && stprovideos.how_to_display!=2)
        return false;
    var classname = type ? '.st_pro_video_play_on_first_gallery' : '.st_pro_video_play_static';
    switch(stprovideos.slider){
        case 0:
        break;
        case 1:
            if(stprovideos.how_to_display==2 && $(stprovideos.gallery_container).length && typeof($(stprovideos.gallery_container)[0].swiper)!='undefined'){
                $(classname).toggleClass('st_pro_videos_invisible', !$(stprovideos.gallery_container)[0].swiper.isBeginning);
                $(stprovideos.gallery_container)[0].swiper.on('slideChangeTransitionEnd', function(swiper){
                    $(classname).toggleClass('st_pro_videos_invisible', !swiper.isBeginning);
                });
            }
            if(stprovideos.how_to_display==0){
                $(classname).addClass('st_pro_videos_invisible');
            }
        break;
        case 2:
        break;
        case 3:
            if(typeof($(stprovideos.gallery_container).slick)!='undefined'){
                $(classname).toggleClass('st_pro_videos_invisible', $(stprovideos.gallery_container).slick('slickCurrentSlide')!=0);
                $(stprovideos.gallery_container).on('afterChange', function(event, slick, currentSlide, nextSlide){
                    if($(stprovideos.gallery_container).slick('slickCurrentSlide')==0)
                        $(classname).removeClass('st_pro_videos_invisible');
                    else{
                        $(classname).addClass('st_pro_videos_invisible');
                        st_pro_videos.st_pro_videos_remove(2);
                    }
                });
            }
        break;
        case 4:
            if(stprovideos.how_to_display==2 && $(stprovideos.gallery_container).length && typeof($(stprovideos.gallery_container).data("owlCarousel"))!='undefined'){
                $(classname).toggleClass('st_pro_videos_invisible', $(stprovideos.gallery_container).data("owlCarousel").owl.currentItem!=0);
                $(document).on('main_gallery_after_action', function(){
                    $(classname).toggleClass('st_pro_videos_invisible', $(stprovideos.gallery_container).data("owlCarousel").owl.currentItem!=0);
                });
            }
            if(stprovideos.how_to_display==0){
                $(classname).addClass('st_pro_videos_invisible');
            }
        break;
    }
    switch(stprovideos.thumb_slider){
        case 0:
            $(stprovideos.thumbnail_selector).on('click', function(){
                if(stprovideos.how_to_display==2){
                    if($(this).find('.st_pro_video_play_on_first_thumb').length){
                        if($(classname).siblings('.st_pro_videos_box').hasClass('st_pro_videos_invisible'))
                            $(classname).removeClass('st_pro_videos_invisible');
                    }else{
                        $(classname).addClass('st_pro_videos_invisible');
                        st_pro_videos.st_pro_videos_remove(2);
                    }
                }
                if(stprovideos.how_to_display==0){
                    st_pro_videos.st_pro_videos_remove(0);
                }
            });
        break;
        case 1:
            if($(stprovideos.thumbnail_container).length && typeof($(stprovideos.thumbnail_container)[0].swiper)!='undefined'){
                $(stprovideos.thumbnail_container)[0].swiper.on('click', function(swiper){
                    if(stprovideos.how_to_display==2){
                        if($(swiper.slides).eq(swiper.clickedIndex).find('.st_pro_video_play_on_first_thumb').length==1){
                            if($(classname).siblings('.st_pro_videos_box').hasClass('st_pro_videos_invisible')){
                                $(classname).removeClass('st_pro_videos_invisible');
                            }
                        }
                        else{
                            $(classname).addClass('st_pro_videos_invisible');
                            st_pro_videos.st_pro_videos_remove(2);
                        }
                    }
                    if(stprovideos.how_to_display==0){//why  && stprovideos.slider!=1 2019 03 13 dao zhi panda dian ji thumb, video bu guan bi.
                        st_pro_videos.st_pro_videos_remove(0);
                    }
                });
            }
        break;
        case 2:
        break;
        case 3:
        break;
        case 4:
            if($(stprovideos.thumbnail_container).length && typeof($(stprovideos.thumbnail_container).data("owlCarousel"))!='undefined'){
                $(stprovideos.thumbnail_container).on("click", ".owl-item", function(e){
                    e.preventDefault();
                    if(stprovideos.how_to_display==2){
                        if($(this).find('.st_pro_video_play_on_first_thumb').length==1){
                            if($(classname).siblings('.st_pro_videos_box').hasClass('st_pro_videos_invisible')){
                                $(classname).removeClass('st_pro_videos_invisible');
                            }
                        }
                        else{
                            $(classname).addClass('st_pro_videos_invisible');
                            st_pro_videos.st_pro_videos_remove(2);
                        }
                    }
                });
            }
        break;
        case 5:
            $(document).on('hover', stprovideos.thumbnail_selector, function(){
                if(stprovideos.how_to_display==2){
                    if($(this).find('.st_pro_video_play_on_first_thumb').length){
                        if($(classname).siblings('.st_pro_videos_box').hasClass('st_pro_videos_invisible'))
                            $(classname).removeClass('st_pro_videos_invisible');
                    }else{
                        $(classname).addClass('st_pro_videos_invisible');
                        st_pro_videos.st_pro_videos_remove(2);
                    }
                }
                if(stprovideos.how_to_display==0){
                    st_pro_videos.st_pro_videos_remove(0);
                }
            });
        break;
    }
  },
  st_pro_manually_play: function(id){
        var video = $('#st_pro_video_'+id+' .st_pro_videos');
        if(!video.length)
            return false;
        st_pro_videos.st_pro_videos_toggle_btns(1, id);

        var player = videojs.getPlayer(video[0]);
        if(player && !player.paused())
            player.pause();
        else if(player && (video.data('autoplay')==1 || (this.options.autoplay==1 && video.data('autoplay')==4)) || stprovideos.how_to_display==0 || stprovideos.how_to_display==2){
            player.ready(function(){
                setTimeout(function(){
                    if(!$('#st_pro_video_'+id).hasClass('.st_pro_videos_invisible')){
                        player.play();
                    }
                },100);
            });
            if(stprovideos.how_to_display!=0 && stprovideos.how_to_display!=2){
                switch(stprovideos.thumb_slider){
                    case 0:
                        if($(stprovideos.thumbnail_selector).filter('[data-video-id="'+id+'"]').length){
                            $(stprovideos.thumbnail_selector).find('.selected').removeClass('selected');
                            $(stprovideos.thumbnail_selector).filter('[data-video-id="'+id+'"]').find('.thumb').addClass('selected');
                        }
                    break;
                }
            }
        }
    },
    init_st_pro_videos: function(params){
      $('.st_pro_videos').each(function(k,v){
        var is_youtube = $(this).find('source').attr('type').indexOf('youtube')!==-1;
        var options = st_pro_videos.options;
        if($(this).data('autoplay')!=4)
            options = $.extend(options, {'autoplay': $(this).data('autoplay')});
        if($(this).data('muted')!=4)
            options = $.extend(options, {'muted': $(this).data('muted')});
        if($(this).data('loop')!=4)
            options = $.extend(options, {'loop': $(this).data('loop')});
        var myPlayer = videojs(this, {
            controls: is_youtube && options.controls_youtube ? false : options.controls,
            fluid: true,
            autoplay: options.autoplay, // zhi qian why false, dao zhi youtube huo qu budao zhi, bu zhi dong bo fang.
            loop: options.loop===1,
            muted: options.muted,
            techOrder: is_youtube ? ['youtube'] : ['html5'],
            "youtube": is_youtube && options.controls_youtube ? { "ytControls": 2 } : {},
            "playsinline": "playsinline",
            preload: (stprovideos.how_to_display==0 || stprovideos.how_to_display==2 ? 'none' : 'metadata')
          }).on("ended",function(){
              if(typeof(stprovideos.auto_close_at_end)!='undefined' && stprovideos.auto_close_at_end)
                st_pro_videos.st_pro_videos_remove(0);
          });
      });
        if(stprovideos.how_to_display==0 || stprovideos.how_to_display==2){
            var video = $('#st_pro_video_'+stprovideos.videos[0].id_st_pro_video+' .st_pro_videos');
            if(params.yi==1 && (video.data('autoplay')==1 || (st_pro_videos.options.autoplay==1 && video.data('autoplay')==4)))
                st_pro_videos.st_pro_manually_play(stprovideos.videos[0].id_st_pro_video);
            else
                st_pro_videos.st_pro_videos_remove(0);
        }else{
            switch(stprovideos.slider){
                case 0:
                    if(params.yi==1 && (stprovideos.how_to_display==1 || stprovideos.how_to_display==4)){
                        var key = stprovideos.videos.length-1;
                        st_pro_videos.st_pro_manually_play(stprovideos.videos[key].id_st_pro_video);
                    }
                    else
                        st_pro_videos.st_pro_videos_remove(0);
                break;
                case 1://Swiper
                    if($(stprovideos.gallery_container).length && typeof($(stprovideos.gallery_container)[0].swiper)!='undefined'){
                        if(stprovideos.videos.length==$(stprovideos.gallery_container)[0].swiper.slides.length)//ugly way to tell if a product has no images, because changeStart will not run in this case.
                            st_pro_videos.swiper_video($(stprovideos.gallery_container)[0].swiper);
                        else if((stprovideos.how_to_display==1 || stprovideos.how_to_display==4)){
                            $(stprovideos.gallery_container)[0].swiper.slideTo(0);
                        }
                        st_pro_videos.swiper_sticker($(stprovideos.gallery_container)[0].swiper);
                    }
                break;
                case 3:
                    if(typeof($(stprovideos.gallery_container).slick)!='undefined'){
                        st_pro_videos.slick_video($(stprovideos.gallery_container).slick('getSlick').$slides, $(stprovideos.gallery_container).slick('getSlick').$slides[0]);
                        st_pro_videos.slick_sticker($(stprovideos.gallery_container).slick('getSlick').$slides[0]);
                    }
                break;
                case 4://Owl1
                    if($(stprovideos.gallery_container).length && typeof($(stprovideos.gallery_container).data("owlCarousel"))!='undefined'){
                        if((stprovideos.how_to_display==1 || stprovideos.how_to_display==4)){
                            // $(stprovideos.gallery_container).trigger("owl.goTo", 0);
                            $(stprovideos.gallery_container).trigger("main_gallery_after_action", [$(stprovideos.gallery_container).data("owlCarousel")]);
                        }
                        st_pro_videos.owl1_sticker($(stprovideos.gallery_container).data("owlCarousel"));
                    }
                break;
            }
            switch(stprovideos.thumb_slider){
                case 0:
                break;
                case 1://Swiper requests to scroll to the first manually
                    if((stprovideos.how_to_display==1 || stprovideos.how_to_display==4) && $(stprovideos.thumbnail_container).length && typeof($(stprovideos.thumbnail_container)[0].swiper)!='undefined')
                        $(stprovideos.thumbnail_container)[0].swiper.slideTo(0);
                break;
                case 3://Slick, it's show the first slide by default
                break;
                case 4://Owl1
                break;
            }
        }
    },
    append_item: function(type,slider,html){
        var pre = (stprovideos.how_to_display==1 || stprovideos.how_to_display==4);
        switch(type){
            case 0:
            case 5:
                pre ? slider.prepend(html) : slider.append(html);
            break;
            case 1://Swiper
                if(typeof(slider[0].swiper)!='undefined'){
                    pre ? slider[0].swiper.prependSlide(html) : slider[0].swiper.appendSlide(html);
                }
            break;
            case 2://Owl 2
                pre ? slider.trigger('add.owl.carousel', [$(html), 0]) : slider.trigger('add.owl.carousel', [html]);
            break;
            case 3://Slick
                pre ? slider.slick('slickAdd', html, 0, true) : slider.slick('slickAdd', html);
            break;
            case 4://Owl 1
                pre ? slider.data("owlCarousel").addItem(html, 0) : slider.data("owlCarousel").addItem(html, -1);
            break;
        }
    },
    slider_reinit: function(type,slider){
        switch(type){
            case 0:
                if ($('#main .js-qv-product-images li').length > 2) {
                  $('#main .js-qv-mask').addClass('scroll');
                  $('.scroll-box-arrows').addClass('scroll');
                    $('#main .js-qv-mask').scrollbox({
                      direction: 'h',
                      distance: 113,
                      autoPlay: false
                    });
                    $('.scroll-box-arrows .left').click(function () {
                      $('#main .js-qv-mask').trigger('backward');
                    });
                    $('.scroll-box-arrows .right').click(function () {
                      $('#main .js-qv-mask').trigger('forward');
                    });
                } else {
                  $('#main .js-qv-mask').removeClass('scroll');
                  $('.scroll-box-arrows').removeClass('scroll');
                }
            break;
            case 1://Swiper
                if(typeof(slider[0].swiper)!='undefined'){
                    slider[0].swiper.update(true);
                    if(stprovideos._st_themes_17){
                        if($(slider[0].swiper.slides).length==$(slider[0].swiper.slides).filter('.swiper-slide-visible').length)
                        {
                          $(slider[0].swiper.params.nextButton).hide();
                          $(slider[0].swiper.params.prevButton).hide();
                        }
                        else
                        {
                          $(slider[0].swiper.params.nextButton).show();
                          $(slider[0].swiper.params.prevButton).show();
                        }
                    }
                }
            break;
            case 2://Owl 2
                slider.trigger('refresh.owl.carousel');
            break;
            case 3://Slick
            break;
            case 4://Owl 1
                slider.data("owlCarousel").isTransition=false;// sometimes the ge shi 1, daozhi next prev yong buqi.
            break;
            case 5://
                $('#thumbs_list_frame').width(parseInt($('#thumbs_list_frame >li').outerWidth(true) * $('#thumbs_list_frame >li').length) + 'px');
                $('#thumbs_list').trigger('goto', 0);
                serialScrollFixLock('', '', '', '', 0);
                if(stprovideos.how_to_display==1 || stprovideos.how_to_display==4)
                    $('#views_block .shown').removeClass('shown');
            break;
        }
    },
    slider_init: function(type,slider,gt){
        var pre = (stprovideos.how_to_display==1 || stprovideos.how_to_display==4);
        switch(type){
            case 0:
                st_pro_videos.chu_shi_hua[gt](slider);
            break;
            case 1://Swiper
                if(typeof(slider[0].swiper)!='undefined'){
                    st_pro_videos.chu_shi_hua[gt](slider);
                }
            break;
            case 2://Owl 2
                st_pro_videos.chu_shi_hua[gt](slider);
            break;
            case 3://Slick
                st_pro_videos.chu_shi_hua[gt](slider);
            break;
            case 4://Owl 1
                if(typeof(slider.data("owlCarousel"))!='undefined')
                    st_pro_videos.chu_shi_hua[gt](slider);
            break;
            case 5://serialScroll
                st_pro_videos.chu_shi_hua[gt](slider);
            break;
        }
    },
    init_classic_pro_video: function(params){
        var gallery_container = $(stprovideos.gallery_container);
        var thumb_container = $(stprovideos.thumbnail_container);
        if(gallery_container.length)
            this.slider_init(stprovideos.slider, gallery_container,'slider');
        if(thumb_container.length)
            this.slider_init(stprovideos.thumb_slider, thumb_container, 'thumb');
    },
    swiper_sticker: function(swiper){
        if(!stprovideos._st_themes_17)
            return false;
        var active_video = $(swiper.slides).eq(swiper.activeIndex).find('.st_pro_videos');
        $(stprovideos.video_container+' .st_sticker_block').toggle(!active_video.length);
    },
    swiper_video: function(swiper){
        $(swiper.wrapper).find('.st_pro_videos').each(function(){
            var player = videojs.getPlayer(this);
            if(player){
                player.pause();
            }
        });
        var active_video = $(swiper.slides).eq(swiper.activeIndex).find('.st_pro_videos');
        if(active_video.length){
            if(active_video.data('autoplay')==1 || (active_video.data('autoplay')==4 && stprovideos.autoplay==1)){
                var player = videojs.getPlayer(active_video[0]);
                if(player){
                    player.ready(function(){
                        setTimeout(function(){
                        if(active_video.closest('.swiper-slide').hasClass('swiper-slide-active')){
                            player.play();
                        }
                        },100);
                    });
                }
            }
        }
    },
    slick_video: function(slides,nextSlide){
        $(slides).each(function(){
            var player = videojs.getPlayer($(this).find('.st_pro_videos')[0]);
            if(player){
                player.pause();
            }
        });

        var active_video = $(nextSlide).find('.st_pro_videos');
        if(active_video.length && (active_video.data('autoplay')==1 || (active_video.data('autoplay')==4 && stprovideos.autoplay==1))){
            var player = videojs.getPlayer(active_video[0]);
            if(player){
                player.ready(function(){
                    setTimeout(function(){
                    if(active_video.closest('.slick-slide').hasClass('slick-current'))
                        player.play();
                    },100);
                });
            }
        }
    },
    slick_sticker: function(nextSlide){
        var active_video = $(nextSlide).find('.st_pro_videos');
        $('.product-cover .expander, .product-cover .product-flags').toggle(!active_video.length);
    },
    owl1_video: function(owl1){
        owl1.$elem.find('.st_pro_videos').each(function(){
            var player = videojs.getPlayer(this);
            if(player){
                player.pause();
            }
        });
        var active_video = owl1.$owlItems.eq(owl1.currentItem).find('.st_pro_videos');
        if(active_video.length){
            if(active_video.data('autoplay')==1 || (active_video.data('autoplay')==4 && stprovideos.autoplay==1)){
                var player = videojs.getPlayer(active_video[0]);
                if(player){
                    player.ready(function(){
                        setTimeout(function(){
                        if(active_video.closest('.owl-item').hasClass('active')){
                            player.play();
                        }
                        },100);
                    });
                }
            }
        }
    },
    owl1_sticker: function(owl1){
        if(!stprovideos._st_themes_16)
            return false;
        var active_video = owl1.$owlItems.eq(owl1.currentItem).find('.st_pro_videos');
        $(stprovideos.video_container+' .new, '+stprovideos.video_container+' #reduction_percent').toggle(!active_video.length);
    },
    chu_shi_hua:{
        slider: function(gallery_container){
            if(!stprovideos.slider)
                gallery_container.append('<button class="st_pro_video_stop st_pro_video_btn st_pro_videos_invisible" type="button" aria-disabled="false"><span aria-hidden="true" class="vjs-icon-placeholder"></span></button>');
            $.each(stprovideos.videos, function(){
                var html = st_pro_videos.bulid_video_html(this, false);
                switch(stprovideos.slider){
                    case 1://Swiper
                        html = '<div class="swiper-slide"><img src="'+stprovideos.gallery_image_url+'">'+html+'</div>';
                    break;
                    case 2://Owl 2
                    break;
                    case 3://Slick
                        html = '<div><img src="'+stprovideos.gallery_image_url+'" class="img-fluid">'+html+'</div>';
                    break;
                    case 4://Owl 1
                        html = '<div class="item"><img src="'+stprovideos.gallery_image_url+'" class="img-responsive">'+html+'</div>';
                    break;
                }
                st_pro_videos.append_item(stprovideos.slider, gallery_container, html);
                if(stprovideos._st_themes_17){
                    var pre = (stprovideos.how_to_display==1 || stprovideos.how_to_display==4);
                    var invisible_trigger = '<a href="javascript:;" class="st_pro_videos_invisible"></a>';;
                    pre ? $('.pro_popup_trigger_box').prepend(invisible_trigger) : $('.pro_popup_trigger_box').append(invisible_trigger);
                }
            });
            st_pro_videos.slider_reinit(stprovideos.slider, gallery_container);
            if(stprovideos.slider){
                switch(stprovideos.slider){
                    case 1://Swiper
                        if(typeof(gallery_container[0].swiper)!='undefined'){
                            gallery_container[0].swiper.on('slideChangeTransitionStart', function(swiper){
                                st_pro_videos.swiper_video(swiper);
                                st_pro_videos.swiper_sticker(swiper);
                            });
                        }
                    break;
                    case 2://Owl 2
                        gallery_container.on('changed.owl.carousel', function(event) {
                            $(gallery_container).find('.st_pro_videos').each(function(){
                                var player = videojs.getPlayer(this);
                                if(player)
                                    player.pause();
                            });

                            var active_video = event.item.find('.st_pro_videos');
                            if(active_video.length && (active_video.data('autoplay')==1 || (active_video.data('autoplay')==4 && stprovideos.autoplay==1))){
                                var player = videojs.getPlayer(active_video[0]);
                                if(player){
                                    player.ready(function(){
                                        setTimeout(function(){
                                        if(active_video.closest('.owl-item').hasClass('active'))
                                            player.play();
                                        },100);
                                    });
                                }
                            }
                        });
                    break;
                    case 3://Slick
                        gallery_container.on('beforeChange', function(event, slick, currentSlide, nextSlide){
                            st_pro_videos.slick_video(slick.$slides, slick.$slides[nextSlide]);
                            st_pro_videos.slick_sticker(slick.$slides[nextSlide]);
                        });
                    break;
                    case 4://Owl 1
                        $(document).on('main_gallery_after_action', function(event, owl1){
                            st_pro_videos.owl1_video(owl1);
                            st_pro_videos.owl1_sticker(owl1);
                        });
                    break;
                }
            }
        },
        thumb: function(thumb_container){
            $.each(stprovideos.videos, function(){
                st_pro_videos.append_item(stprovideos.thumb_slider, thumb_container, this.thumbnail_html);
            });
            st_pro_videos.slider_reinit(stprovideos.thumb_slider, thumb_container);

            if(stprovideos.slider){
            }else{
                $(stprovideos.thumbnail_selector).on((((stprovideos.st_is_16 && stprovideos.thumbnail_event==0) || stprovideos.thumbnail_event==2) ? 'mouseenter' : 'click'), function(e){
                    var id = $(this).data('video-id');
                    if(id){
                        st_pro_videos.st_pro_videos_remove(0,id);
                        st_pro_videos.st_pro_manually_play(id);
                        if(stprovideos.st_is_16)
                            $('#views_block .shown').removeClass('shown');
                    }
                    else
                        st_pro_videos.st_pro_videos_remove(0,id);
                });
            }
        },
    },
  st_pro_videos_toggle_btns: function(status, id) {
    if(status===1)
    {
        $('.st_pro_video_stop, #st_pro_video_'+id).removeClass('st_pro_videos_invisible');
        $('.st_pro_video_play').addClass('st_pro_videos_invisible');
    }
    else
    {
        if(status!==2)
            $('.st_pro_video_play').removeClass('st_pro_videos_invisible');
        $('.st_pro_video_stop').addClass('st_pro_videos_invisible');
        if(!stprovideos.slider || stprovideos.how_to_display==0 || stprovideos.how_to_display==2)
            $('.st_pro_videos_box').addClass('st_pro_videos_invisible');
    }
  },
  st_pro_videos_remove: function(status,id) {
    if(typeof(id)=='undefined')
        id=0;
    $.each($('.st_pro_videos'), function(){
        if($(this).parent().data('video-id')==id)
            return true;
        var player = videojs.getPlayer(this);
        if(player)
            player.pause();
    });
    st_pro_videos.st_pro_videos_toggle_btns(status,id);
  }
};
