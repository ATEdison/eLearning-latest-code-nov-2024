(function ($) {
  $(document).ready(function () {
    var $holder = $('.sta-hero-home');
    if ($holder.length < 1) {
      return;
    }

    var $navigationSlider = $('.sta-hero-home-navigation');
    var $mapHolder = $('.sta-hero-home-map');
    var $map = $mapHolder.find('svg');
    var mapSVG = d3.select($map[0]);
    var $markerDesc = $('.sta-hero-home-map-marker-desc');
    // var $bgVideo = $('.sta-hero-home-bg-video');

    var templateMarkerDesc = wp.template('hero-home-map-marker-desc');

    var mapWidth = 313.606;
    var mapHeight = 267.393;
    var backendMarkerSize = 10;
    var markerBorderStrokeWidth = 2;
    var markerSize = 24;
    var markerRadius = markerSize / 2;
    var markerDotSize = 12;

    $(document).on('mouseenter', '.sta-hero-home-map-marker circle', function () {
      var $marker = $(this).closest('.sta-hero-home-map-marker');
      focusMarker($marker);
    });

    // $(document).on('mouseleave', '.sta-hero-home-map-marker circle', function () {
    //   var $marker = $(this).closest('.sta-hero-home-map-marker');
    //   $marker.removeClass('active');
    // });

    $(document).on('click', '.sta-hero-home-navigation-item button', function () {
      var $navItem = $(this).parent();
      if ($navItem.hasClass('active')) {
        return;
      }

      var $navigationSlider = $navItem.closest('.sta-hero-home-navigation');
      if ($navigationSlider.hasClass('slick-initialized')) {
        $navigationSlider.slick('slickGoTo', $navItem.attr('data-slick-index'));
      }

      var mapIndex = $navItem.attr('data-slick-index');
      mapIndex = isNaN(mapIndex) ? $navItem.index() : mapIndex;

      // bg video & image
      // var videoUrl = $navItem.attr('data-video');
      var imageUrl = $navItem.attr('data-image');
      // $holder.css('background-image', 'url(' + imageUrl + ')');
      // $bgVideo.attr('poster', imageUrl);
      // $bgVideo.find('source').attr('src', videoUrl);
      // $bgVideo[0].load();
      $holder.css('background-image', 'url(' + imageUrl + ')');
      $holder.find('.sta-hero-home-bg-video:not(.d-none)').addClass('d-none');
      $holder.find('.sta-hero-home-bg-video:nth-child(' + (mapIndex + 1) + ')').each(function () {
        var $video = $(this);
        var $source = $video.find('source');

        // first load
        if (!$source.attr('src')) {
          $source.attr('src', $source.attr('data-src'));
          $video[0].load();
        }
        $video.removeClass('d-none');
      });

      // active nav
      $navItem.parent().find('> .active').removeClass('active');
      $navItem.addClass('active');

      // activate markers
      activateMarkers(mapIndex);
    });

    $(document).on('beforeChange', '.sta-hero-home-navigation', function (e, slick, currentSlide, nextSlide) {
      // console.log({ e, slick, currentSlide });
      // window.staSlick = slick;
      $(slick.$slides[nextSlide]).find('button').trigger('click');
    });

    initMap();

    initNavigationSlider();
    $(window).resize(function () {
      initNavigationSlider();
    });

    function focusMarker($marker) {
      $map.find('.sta-hero-home-map-marker.active').removeClass('active');
      $marker.addClass('active');
      $marker.addClass('transform-hidden');
      var data = JSON.parse($marker.attr('data-marker'));
      $markerDesc.html(templateMarkerDesc(data));
    }

    function activateMarkers(mapIndex) {
      $markerDesc.html('');
      // console.log({ mapIndex });
      $map.find('.sta-hero-home-map-marker.d-block').removeClass('d-block');
      $map.find('.sta-hero-home-map-marker[data-map_id=' + mapIndex + ']').each(function (itemIndex) {
        $(this).addClass('d-block');

        // focus the first marker
        if (itemIndex < 1) {
          focusMarker($(this));
        }
      });
    }


    function initNavigationSlider() {
      if (!$navigationSlider.hasClass('slick-initialized')) {
        $navigationSlider.slick({
          infinite: false,
          arrows: true,
          dots: false,
          mobileFirst: true,
          touchMove: false,
          swipe: false,
          centerMode: true,
          variableWidth: true,
          // fade: true,
          // cssEase: 'linear',
          responsive: [
            {
              breakpoint: 992,
              settings: 'unslick',
            },
          ]
        });
      }
    }

    function initMap() {
      var mapDataRaw = $mapHolder.attr('data-map');
      if (!mapDataRaw) {
        return;
      }
      var data = JSON.parse(mapDataRaw);
      // console.log(data);
      $.each(data, function (itemIndex, item) {
        addMarker(item);
      });

      // focus the first marker
      focusMarker($map.find('.sta-hero-home-map-marker.d-block').first());
    }

    function addMarker(data) {
      var posX = data.position.left * mapWidth / 100;
      var posY = data.position.top * mapHeight / 100;
      if (isNaN(posX) || isNaN(posY)) {
        console.log('Marker Error:', data);
        return;
      }
      var markerCenterX = (posX + backendMarkerSize / 2);
      var markerCenterY = (posY + backendMarkerSize / 2);

      var center = markerSize / 2;

      // top left
      var markerX = markerCenterX - markerRadius;
      var markerY = markerCenterY - markerRadius;

      var marker = mapSVG.append('g')
        .attr('class', 'sta-hero-home-map-marker')
        .attr('width', markerSize)
        .attr('height', markerSize)
        .attr('transform', 'translate(' + markerX + ',' + markerY + ')')
        .attr('data-map_id', data.map_id)
        .attr('data-marker', JSON.stringify(data));

      // display only first map markers
      if (data.map_id < 1) {
        marker.node().classList.add('d-block');
      }

      // rect for filling group space
      marker.append('rect')
        .attr('width', markerSize)
        .attr('height', markerSize)
        .attr('fill', 'none')
        .attr('stroke', 'none');

      // border
      var line = d3.path();
      line.arc(center, center, center - markerBorderStrokeWidth / 2, 0, 360 * Math.PI / 180);

      var path = marker.append('path')
        .attr('class', 'sta-hero-home-map-marker-border')
        .attr('d', line)
        .attr('stroke-width', markerBorderStrokeWidth)
        .attr('stroke', '#fff')
        .attr('fill', 'none');

      var pathLength = path.node().getTotalLength();
      path
        .attr('stroke-dasharray', pathLength / 18.5)
        .attr('stroke-dashoffset', pathLength);

      // circle
      marker.append('circle')
        .attr('cx', center)
        .attr('cy', center)
        .attr('r', markerDotSize / 2)
        .attr('stroke', '#fff')
        .attr('fill', '#fff');
    }
  });
})(jQuery);
