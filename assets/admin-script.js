jQuery(function($) {

  // Content Hub Settings: Tabs
  const tabs = document.querySelectorAll(".nav-tab-wrapper > .nav-tab");

  for(i = 0; i < tabs.length; i++) {
    tabs[i].addEventListener("click", switchTab);
  }

  function switchTab(event) {
    event.preventDefault();
    document.querySelector(".nav-tab-wrapper > .nav-tab.nav-tab-active").classList.remove("nav-tab-active");
    document.querySelector(".tab-pane.active").classList.remove("active");

    let clickedTab = event.currentTarget;
    let anchor = event.target;
    let activePaneID = anchor.getAttribute("href");

    clickedTab.classList.add("nav-tab-active");
    document.querySelector(activePaneID).classList.add("active");
  }

  // Content Hub Settings: Set Sources Type
  $('#chps_sources_type').on('change', function(e) {
    e.preventDefault();

    if(this.value == 1) {
      //Type: Posts & Tags
      $('.tr_select_tags').show();
      $('.tr_select_post_types').hide();
      $('.tr_select_topics').hide();
    }else if(this.value == 2){
      //Type: Custom Post Types & Global Topics
      $('.tr_select_tags').hide();
      $('.tr_select_post_types').show();
      $('.tr_select_topics').show();
    }
  });

  // Content Hub Settings: Color Picker
  if ( $.isFunction( $.fn.wpColorPicker ) ) {
		$( 'input.chps_color' ).wpColorPicker();
	}

  // Content Hub Settings: Color Theme
  $('#chps_color_theme').on('change', function(e) {
    e.preventDefault();

    console.log(`change color theme to ${this.value}`);

    if(this.value == 'dark') {
      $('[name="chps_basic_color_1"]').wpColorPicker('color', '#ffffff');
      $('[name="chps_basic_color_2"]').wpColorPicker('color', '#1D2327');
      $('#chps_custom_colors').fadeOut();
    }else if(this.value == 'light'){
      $('[name="chps_basic_color_1"]').wpColorPicker('color', '#1D2327');
      $('[name="chps_basic_color_2"]').wpColorPicker('color', '#ffffff');
      $('#chps_custom_colors').fadeOut();
    }else if(this.value == 'custom'){
      $('#chps_custom_colors').fadeIn();
    }
  })

  $('#chps_bg_use_color').on('change', function(e) {
    e.preventDefault();

    console.log(`change use bg color to ${this.checked}`);

    if(this.checked) {
      $('[name="chps_basic_color_1"]').wpColorPicker('color', '#ffffff');
      $('[name="chps_basic_color_2"]').wpColorPicker('color', '#1D2327');
      $('#chps_bg_color_wrap').fadeIn();
    }else{
      $('#chps_bg_color_wrap').fadeOut();
    }
  })

  // Content Hub Settings & Add/Edit post: Media Uploader
  $('body').on('click', '.chps_upload_image_button', function(e) {
    e.preventDefault();

    var button = $(this),
      image_uploader = wp.media({
        title: 'Custom image',
        library: {
          type: 'image'
        },
        button: {
          text: 'Use this image'
        },
        multiple: false
      }).on('select', function() {
        var attachment = image_uploader.state().get('selection').first().toJSON();
        var url = attachment.sizes.large ? attachment.sizes.large.url : attachment.sizes.full.url;
        $('.chps_upload_image_button').hide();
        $('#chps_image').val(url);
        $('#chps_image_preview').addClass('has-image');
        $('#chps_image_preview .image_tag').html('<img src="' + attachment.url + '">');
      })
      .open();
  });

  $('#clear_image').on('click', function(e) {
    $('#chps_image').val('');
    $('#chps_image_preview').removeClass('has-image');
    $('#chps_image_preview .image_tag').html('');
    $('.chps_upload_image_button').show();
  });

});
