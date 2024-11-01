(function ($, d3) {
  $(document).ready(function () {

    draw();

    // redraw on resize
    window.addEventListener('resize', novicell.debounce(draw, 250));

    function draw() {
      $('.sta-circular-percentage').each(function () {
        drawArc($(this));
      });
    }

    function drawArc($holder) {
      var value = $holder.attr('data-value');
      value = isNaN(value) ? 0 : Math.floor(value);
      // console.log({ value });
      if (value <= 0 || 100 <= value) {
        return;
      }

      var lineWidth = 2;
      var size = $holder.width();
      var center = size / 2;
      var radius = center - lineWidth / 2;
      var startAngle = 0;
      var endAngle = Math.floor(value / 100 * 360);

      var svg = d3.create('svg')
        // .attr('class', 'sta-circular-percentage-svg')
        .attr('viewBox', [0, 0, size, size])
        .attr('width', size + 'px');

      var line = d3.path();
      line.arc(center, center, radius, startAngle * Math.PI / 180, endAngle * Math.PI / 180);

      svg.append('path')
        .attr('d', line)
        // .attr('stroke', '')
        .attr('stroke-width', lineWidth)
        // .attr('stroke-linecap', 'round')
        .attr('fill', 'none');

      $holder.html(svg.node());
    }
  });
})(jQuery, d3);
