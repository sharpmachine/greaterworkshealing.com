jQuery(document).ready( function() {
	var $=jqnc(),plot,previousPoint = null;

	new DateRange('#range','#start','#end');

   // helper for returning the weekends in a period
    function weekendAreas(axes) {
        var markings = [];
        var d = new Date(axes.xaxis.min);
        // go to the first Saturday
        d.setUTCDate(d.getUTCDate() - ((d.getUTCDay() + 1) % 7));
        d.setUTCSeconds(0);
        d.setUTCMinutes(0);
        d.setUTCHours(0);
        var i = d.getTime();
        do {
            // when we don't set yaxis, the rectangle automatically
            // extends to infinity upwards and downwards
            markings.push({ xaxis: { from: i, to: i + 2 * 24 * 60 * 60 * 1000 } });
            i += 7 * 24 * 60 * 60 * 1000;
        } while (i < axes.xaxis.max);

        return markings;
    }

	function plotChart (series) {
		if ( !co ) return;
		co.grid.markings = weekendAreas;
		if ('asMoney' == co.yaxis.tickFormatter) co.yaxis.tickFormatter = asMoney;
		if ('asMoney' == co.xaxis.tickFormatter) co.xaxis.tickFormatter = asMoney;
		if ( series[0] && series[0]['data'].length > 0)
			$.plot($('#chart'), series, co);
	}

	function mapChart (data) {
		$('#map').vectorMap({
		    map: 'world_mill_en',
		    series: {
		      regions: [{
		        values: data,
		        scale: ['#E9FFBA', '#618C03'],
		        normalizeFunction: 'polynomial'
		      }]
		    },
		    onRegionLabelShow: function(e, el, code) {
		      el.html('<strong>'+asMoney(data[code])+'</strong> '+el.html());
		    },
			backgroundColor: 'transparent',
			regionStyle: {
				initial: { fill: '#d2d2d2' }
			}
		});
	}

    function showTooltip(x, y, contents) {
        $('<div id="tooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y-40,
            left: x+10,
            border: '1px solid #dfdfdf',
			borderRadius: '3px',
			boxShadow: '1px 2px 3px #777',
            padding: '2px',
            backgroundColor: '#fff',
            opacity: 0.90
        }).appendTo("body").show();

    }
    $('#chart').bind('plothover', function (event, pos, item) {
        $('#x').text(pos.x.toFixed(2));
        $('#y').text(pos.y.toFixed(2));

        if (item) {
            if (previousPoint != item.datapoint) {
                previousPoint = item.datapoint;

                $("#tooltip").remove();
                var x = item.datapoint[0],
                    y = Math.round(item.datapoint[1]);

				if (co.yaxis.tickFormatter == asMoney) y = asMoney(item.datapoint[1]);


                showTooltip(item.pageX, item.pageY, '<strong>'+y+'</strong> ' +item.series.label);
            }
        } else {
			$('#tooltip').remove();
			previousPoint = null;
        }
    });

	if ( $('#chart').length > 0 ) plotChart(d); // Flot
	if ( $('#map').length > 0 ) mapChart(d);	// Map

	$('#export-settings-button').click(function () { $('#export-settings-button').hide(); $('#export-settings').removeClass('hidden'); });
	$('#selectall_columns').change(function () {
		if ($(this).attr('checked')) $('#export-columns input').not(this).attr('checked',true);
		else $('#export-columns input').not(this).attr('checked',false);
	});
	$('input.current-page').unbind('mouseup.select').bind('mouseup.select',function () { this.select(); });


});