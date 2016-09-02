/**
 * Chartist.js plugin to display a data label on top of the points in a line chart.
 *
 */
/* global Chartist */
(function(window, document, Chartist) {
  'use strict';

  var defaultOptions = {
    labelClass: 'ct-label',
    labelOffset: {
      x: 4,
      y: 4
    },
    textAnchor: 'middle',
    labelInterpolationFnc: Chartist.noop
  };

  Chartist.plugins = Chartist.plugins || {};
  Chartist.plugins.ctBarLabels = function(options) {

    options = Chartist.extend({}, defaultOptions, options);

    return function ctBarLabels(chart) {
      if(chart instanceof Chartist.Bar) {
        chart.on('draw', function(data) {
          if(data.type === 'bar') {
            data.group.elem('text', {
              x: data.x2 + options.labelOffset.x,
              y: data.y1 + options.labelOffset.y,
              style: 'text-anchor: ' + options.textAnchor
            }, options.labelClass).text(options.labelInterpolationFnc(data.value.x === undefined ? data.value.y : data.value.x));
          }
        });
      }
    };
  };

}(window, document, Chartist));
