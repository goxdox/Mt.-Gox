<script language="javascript" type="text/javascript" src="/js/jquery.jqplot.js"></script>
<script type="text/javascript" src="/js/plugins/jqplot.ohlcRenderer.min.js"></script>
<script type="text/javascript" src="/js/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="/js/plugins/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="/js/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="/js/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
<script type="text/javascript" src="/js/plugins/jqplot.highlighter.js"></script>
<script type="text/javascript" src="/js/plugins/jqplot.cursor.min.js"></script>
<!--[if IE]><script language="javascript" type="text/javascript" src="../src/excanvas.js"></script><![endif]--> 
<link rel="stylesheet" type="text/css" href="/css/jquery.jqplot.min.css" />
<script type="text/javascript" language="javascript" src="/js/jquery.dataTables.min.js"></script> 
<link rel="stylesheet" type="text/css" href="/css/jquery.dataTables.css" />
<script type="text/javascript" language="javascript">
var gDepthTable;

$(function () {
  $('#volume').hide();
  $('#depth').hide();

  gDepthTable=$('#depth_table').dataTable({
		"bLengthChange": false,
		"bFilter": false,
		"bPaginate": false,
		"bAutoWidth": false,
		"aaSorting": [[ 1, "asc" ]]
		
		});
});




function getDepth()
{
	$.post("/code/getDepth.php", { }, onDepth , "json" );
	$('#volume').hide();
	$('#depth').show();
}

function getHistory()
{
	$.post("/data/allHistory.json", { }, onHistory , "json" );
	$('#volume').show();
}

function getRecent()
{
	$.post("/code/getHistory.php", { }, onRecent , "json" );
	$('#volume').hide();
}

function onDepth(result)
{
	
	if(result.asks && result.bids)
	{
		var data1=result.asks;
		var data2=result.bids;		

		gDepthTable.fnClearTable();
		for(var n=0; n<data1.length; n++)
		{
			gDepthTable.fnAddData([ "Ask" ,i4_round(data1[n][0],4),i4_round(data1[n][1],2) ]);
		}
		for(var n=0; n<data2.length; n++)
		{
			gDepthTable.fnAddData([ "Bid" ,i4_round(data2[n][0],4),i4_round(data2[n][1],2)]);
		}
	
		var plot = $.jqplot('plot',[ data1, data2 ], {
				show: false,
				axes:{
				      xaxis:{
			tickOptions:{formatString:'$%.4f'},
	        label:'Price',
	        autoscale: true,
				       
				        
				      },
				      yaxis:{
				    	  autoscale: true,
				        label:'Amount',
				        min: 0
				        
				      }
				    },
				    
								
				//seriesDefaults:{ renderer:$.jqplot.BarRenderer, 
				//	rendererOptions:{barWidth: 2,shadowOffset:0,shadowDepth:0,shadowAlpha:0} },
				legend: {show: true, location: 'ne'},
					
					 series: [
					          {label: 'Ask'}, 
					          {label: 'Bid'}
					      ],
					      highlighter: {
								show: true,
						      showMarker:true,
						      tooltipLocation: 'nw',
						      formatString:'<table class="jqplot-highlighter"><tr><td>Price:</td><td>%s</td></tr><tr><td>Amount:</td><td>%s</td></tr></table>'
						  }
				  //cursor: {tooltipLocation:'nw'}
			});
		
		plot.redraw();
	}
	$('#error').text(result.error);
	$('#status').text(result.status);
}

function onVolume()
{
	if( $('#volCheck').attr('checked'))
	{
		showVolHistory();
	}else showHistory();
}
var gCandleData;

function showVolHistory()
{
	var open;
	var high;
	var low;
	var close;
	var vol=gCandleData.vol;

	var data=gCandleData.plot;
	var period=gCandleData.period*1000;
	var start=gCandleData.start*1000;
	points=new Array();
	volume=new Array();
	for(var n=0; n<data.length; n++)
	{
		points[n]=[start,data[n][0],data[n][1],data[n][2],data[n][3]];
		volume[n]=[start,vol[n]];
		start += period;
	}
	
	
	var plot = $.jqplot('plot',[ points, volume ], {
		show: false,
		  series: [{renderer:$.jqplot.OHLCRenderer, rendererOptions:{candleStick:true}},{yaxis:'y2axis'}],
		  axesDefaults:{},
		  axes: {
		      xaxis: {
		          renderer:$.jqplot.DateAxisRenderer,
		          tickOptions:{formatString:'%m/%d'}
		      },
		      yaxis: {
		          tickOptions:{formatString:'$%.4f'}
		      }
		  },
		  highlighter: {
				show: true,
		      showMarker:true,
		      tooltipAxes: 'y',
		      tooltipLocation: 'nw',
		      yvalues: 4,
		      formatString:'<table class="jqplot-highlighter"><tr><td>open:</td><td>%s</td></tr><tr><td>high:</td><td>%s</td></tr><tr><td>low:</td><td>%s</td></tr><tr><td>close:</td><td>%s</td></tr></table>'
		  }
	} );
	plot.redraw();
}

function showHistory()
{
	var open;
	var high;
	var low;
	var close;
	var volume;

	var data=gCandleData.plot;
	var period=gCandleData.period*1000;
	var start=gCandleData.start*1000;
	points=new Array();
	for(var n=0; n<data.length; n++)
	{
		points[n]=[start,data[n][0],data[n][1],data[n][2],data[n][3]];
		start += period;
	}
	
	
	var plot = $.jqplot('plot',[ points ], {
		show: false,
		  series: [{renderer:$.jqplot.OHLCRenderer, rendererOptions:{candleStick:true}}],
		  axesDefaults:{},
		  axes: {
		      xaxis: {
		          renderer:$.jqplot.DateAxisRenderer,
		          tickOptions:{formatString:'%m/%d'}
		      },
		      yaxis: {
		          tickOptions:{formatString:'$%.4f'}
		      }
		  },
		  highlighter: {
				show: true,
		      showMarker:true,
		      tooltipAxes: 'y',
		      tooltipLocation: 'nw',
		      yvalues: 4,
		      formatString:'<table class="jqplot-highlighter"><tr><td>open:</td><td>%s</td></tr><tr><td>high:</td><td>%s</td></tr><tr><td>low:</td><td>%s</td></tr><tr><td>close:</td><td>%s</td></tr></table>'
		  }
	} );
	plot.redraw();
}
// gra
function onHistory(result)
{
	if(result.plot)
	{
		gCandleData=result;

		onVolume();

	}
}


function onRecent(result)
{
	if(result.plot)
	{
		var data=result.plot.data;
		var minTime=data[0][0];
		var maxTime=data[data.length-1][0];
		
		var plot = $.jqplot('plot',[ data ], { 
			show: false,
			highlighter: {
				show: true,
			     tooltipAxes: 'y',
		      showMarker:true
		  },
			axes:{ yaxis:{tickOptions:{formatString:'$%.4f'}},
				xaxis:{
				min: minTime,
				max: maxTime,
            renderer:$.jqplot.DateAxisRenderer, 
            tickOptions:{formatString:'%H:%M:%S'},
            tickInterval:'8 hour'
        	}}

 			} );
		plot.redraw();
	}
	$('#error').text(result.error);
	$('#status').text(result.status);
}




</script>

<div id="status"></div>
<div id="error"></div>
<fieldset>
 <legend>Data</legend>
<table class="btcx_table" >
<tr><td><a onClick="getRecent()">Last 48 Hours</a></td><td><a onClick="getHistory()">All time</a></td><td><a onClick="getDepth()">Depth of Market</a></td></tr>
</table>
<div id="plot" style="width:600px;height:400px;"></div>
<div id="volume"><input type="checkbox" id="volCheck" onchange="onVolume()" /> Show Volume Data</div>
</fieldset>
<hr>
<fieldset id="depth" >
 <legend>Depth Table</legend>
<table width="100%" class="sort_table" id="depth_table" >
<thead style="cursor: pointer;" ><tr><th>Type</th><th>Price</th><th>Amount</th></tr></thead>
</table>
</fieldset>
