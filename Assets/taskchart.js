jQuery(document).ready(function(){
	
//	each( window.chartsQueue )

	var projectTasksCharts = $('.project-tasks-chart');
	
	if ( projectTasksCharts.length )
	{
		projectTasksCharts.each(function(idx, el)
		{
			var names = $(el).data("names") || [];
			var columns = $(el).data("columns") || [];
			// nullify so data can set by ident
			var columns0 = $.extend(true,[], columns);
			columns0.map(function(el){return el[1] = 0;});

			var chart = c3.generate({
				bindto: el,
				size: {
					width: 150,
					height: 150,
				},
				data: {
					columns: columns0,
					type : 'donut',
					order: null,	// "asc", "desc", null
					names: names,
					colors: { 
						planned: "#5A607F",
						due: 	 "#0058FF",
						overdue: "#FF0000",
						closed:  "#10BF2B",
						_total_: "#ffffff"
					}
				},
				transition: {
					duration: 500
				},
				interaction: {
					enabled: true
				},
				legend: {
					show: false,
					hide: true,
					position: "right"
				},
				donut: {
					width: 10,
					padAngle: 0,
					label: {
						show: false
					}
				},
				padding: {
					top: 0,
					bottom: 0,
					left: 0,
					right: 0
				}
			});
			
//			var __colTotal = columns.pop();
			var __colTotal = columns.splice(0, 1)[0];
			
			// see data order for other order
			chart.__animateOne = false;
			chart.addNextData = function()
			{
				if (columns.length)
				{
					if (chart.__animateOne)
					{
						var n = columns.pop();
						if ( n[1] > 0 ){
							// reduce white spacer
							__colTotal[1] -= n[1];
							chart.load({
								columns: [ n, __colTotal ]
							});
						}else{
							chart.addNextData();
							return;
						}
					}
					
					setTimeout(function () {
						if (chart.__animateOne){
							// set one
							chart.addNextData();
						}else{
							// set all
							chart.load({ columns: columns });
						}
					}, 500);
				}
			}
			chart.addNextData();
			
		})
	}
	
//	window.chartsQueue.push(
//	});

	
//	;
});
