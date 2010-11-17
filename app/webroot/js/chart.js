/*
**************  MEGA CHART!!!!!!!!!!!!!!! *******
 
What we want from the chart:
	-Full width of screen
	Price line or
	-Candelsticks for > than tick time scale
		calculate the correct width of the sticks	
		what should we do when there is no action?
		tooltips or highligh the closest and add to label
		start time correctly
		They are located incorrectly
	Allow you to zoom on sections of history
	-Variable time scale for history
	It is laggy
	Hook it up to realtime feed
	Price line
	Order Depth
		As dots
		-Cumaltive
	Your open orders
	-Volume
		calculate the correct width of the bars	
	
	Mouse move should:
		-Show cross hairs and info in bottom left:
			-Price
			-Time
			-Volume
			-Depth
			average price for that depth
		
	Click should:
		open buy/sell order dialog
		
	Options:
		Display Volume
		Display Depth
		Display your orders
		Display price line
		
	Variable height	
	Axies:
		y
			Price
			volume
		x
			Depth amount
			Time
		
		
*/



function MegaChart()
{
	var mMouseX=0;
	var mMouseY=0;
	var mMaxDepth=0;
	var mMaxPrice=1;
	var mMinPrice=0;
	
	
	var mDepthMode=0;
	
	// options
	this.setDepthMode=function(x){ mDepthMode=x; }
	this.getDepthMode=function(){ return(mDepthMode); }
	///
	
	this.setMouseX = function(x){ mMouseX=x; }
	this.setMouseY = function(x){ mMouseY=x; }
	this.setMaxDepth = function(x){ mMaxDepth=x; }
	this.setMinPrice = function(x){ mMinPrice=x; }
	this.setMaxPrice = function(x){ mMaxPrice=x; }
	
	this.getMinPrice = function(){ return(mMinPrice); }
	this.getMaxPrice = function(){ return(mMaxPrice); }
	this.getMouseX = function(){ return(mMouseX); }
	this.getMouseY = function(){ return(mMouseY); }
	this.getMaxDepth = function(){ return(mMaxDepth); }
	
	this.getTickWidth = function(){ return(w/500); }
	
	
	this.prices = prices;
	function prices(d){ return(d[0]); }
	function date(d){ return(d[5]); }


	this.timeLabel = function(){ return(dateFormat(mMouseX*1000,"mm/dd HH:MM:ss") ); }
	this.priceLabel = function(){ return("Price: "+i4_round(mMouseY,4)); }
	this.depthLabel = function()
	{
		//alert(gBids.map(prices).reverse()+" "+mMouseY);
		var depthIndex = pv.search(gAsks.map(prices), mMouseY);
		depthIndex = depthIndex < 0 ? (-depthIndex - 2) : depthIndex;
		//alert(depthIndex);
		if(depthIndex==-1)
		{
			depthIndex = pv.search(gBids.map(prices), mMouseY)-1;
			depthIndex = depthIndex < 0 ? (-depthIndex - 2) : depthIndex;
			if(depthIndex==-1 || depthIndex>=gBids.length) return("0");
			else return("Bid Depth: "+i4_addCommas(i4_round(gBids[depthIndex][1],0)));
		}else
		{
			return("Ask Depth: "+i4_addCommas(i4_round(gAsks[depthIndex][1],0)));
		}
	}
	// the average price to buy this many
	this.depthPriceLabel = function()
	{
		// how many
		
		return("depthPrice");
	}
	
	this.volumeLabel = function()
	{
		plotIndex = pv.search(gPlot.map(date).reverse(), mMouseX)-1;
		plotIndex = plotIndex < 0 ? (-plotIndex - 2) : plotIndex;
		//alert(plotIndex+" "+mMouseX)
		if(plotIndex==-1 || plotIndex>=gPlot.length) return("Volume: 0");
		else return("Volume: "+i4_addCommas(i4_round(gPlot[plotIndex][4],0)));
	}
	
	this.ohlcLabel = function()
	{
		plotIndex = pv.search(gPlot.map(date).reverse(), mMouseX)-1;
		plotIndex = plotIndex < 0 ? (-plotIndex - 2) : plotIndex;
		//alert(plotIndex+" "+mMouseX)
		if(plotIndex==-1 || plotIndex>=gPlot.length) return("");
		else return("o:"+i4_round(gPlot[plotIndex][0],3)+
					" h:"+i4_round(gPlot[plotIndex][1],3)+
					" l:"+i4_round(gPlot[plotIndex][2],3)+
					" c:"+i4_round(gPlot[plotIndex][3],3));
	}
}

var gMegaChart=new MegaChart();

/* Sizing and scales. */
var w = $("#megaChart").width()-30-5,
    h = $("#megaChart").height()-20-5,    
    gAsks=[],
    gBids=[],
    gPlot=[],
    volumeAxis=pv.Scale.linear(0, 1000).range(0, h/3),
    depthAxis=pv.Scale.linear(0, 1000).range(0, w/2),
    x = pv.Scale.linear(0, 50).range(0, w),
    y = pv.Scale.linear(0, 1).range(0, h);

/* The root panel. */
var vis = new pv.Panel()
	.canvas('megaChart')
  //  .width(w)
  //  .height(h)
    .bottom(20)
    .left(30)
    .right(5)
    .top(5)
    .cursor('crosshair')
    .events("all")
    .event("mousemove" , moveMouse )
    .event("mousedown", placeOrder );



/* X-axis ticks. */
vis.add(pv.Rule)
    .data(function(){ return(x.ticks()); })
    .visible(f1)
    .left(x)
    .strokeStyle("#eee")
  .add(pv.Rule)
    .bottom(-5)
    .height(5)
    .strokeStyle("#000")
  .anchor("bottom").add(pv.Label)
   .text(function(t){ return(dateFormat(t*1000,"mm/dd HH:MM:ss"));});
  //  .text( dateFormat(x.tickFormat*1000,"mm/dd HH:MM:ss") );
  
/* Y-axis ticks. */
vis.add(pv.Rule)
    .data(function(){ return(y.ticks(6)); })
    .top(y)
    .strokeStyle(f2)
  .anchor("left").add(pv.Label)
    .text(y.tickFormat);


// Y-axis cursor 
vis.add(pv.Rule)
    .top(function(){ return(y(gMegaChart.getMouseY())); })
    .strokeStyle("rgba(255,0,0,.5)")
  .anchor("left");

//X-axis cursor 
var gXCursor=vis.add(pv.Rule)
    .left(function(){ return(x(gMegaChart.getMouseX())); })
    .strokeStyle("rgba(255,0,0,.5)")
  .anchor("top");

/*
// The price line 
vis.add(pv.Line)
    .data(data)
    //.interpolate("step-after")
    .left(f3)
    .bottom(f4)
    .lineWidth(3);
*/
/* works:
vis.add(pv.Line)
.data(function() gAsks)
.interpolate("step-after")
.left(function(d) depthAxis(d[1]))
.bottom(function(d) y(d[0]))
.strokeStyle("#e00")
.lineWidth(3);
*/

// Asks
vis.add(pv.Area)
.data(function() {return(gAsks);})
.interpolate("step-before")
.left(1)
.width(function(d){ return(depthAxis(d[1])); })
.top(function(d){ return(y(d[0]));} )
.fillStyle("rgba(121,173,210,.5)")
.anchor("right").add(pv.Line)
.lineWidth(1);

// Bids
vis.add(pv.Area)
.data(function() {return(gBids);})
.interpolate("step-after")
.left(1)
.width(function(d) depthAxis(d[1]))
.top(function(d) y(d[0]))
.fillStyle("rgba(255,173,210,.5)")
.anchor("right").add(pv.Line)
.lineWidth(1);

// candles
vis.add(pv.Rule)
.data(function() {return(gPlot);})
.right(function(d){ return x(d[5]); })
.top(function(d){ return y(Math.min(d[1], d[2]));})
.height(function(d){ return(1+Math.abs(y(d[1]) - y(d[2]))); } )
.strokeStyle(function(d){ return(d[0] < d[3] ? "#FF0000" : "#00FF00"); } )
.add(pv.Rule)
.top(function(d){ return y(Math.min(d[0], d[3])); } )
.height(function(d){ return Math.abs(y(d[0]) - y(d[3])); } )
.lineWidth(gMegaChart.getTickWidth);

//volume bars
vis.add(pv.Rule)
.data(function() {return(gPlot);})
.right(function(d) x(d[5]))
.bottom(1)
.height(function(d) volumeAxis(d[4]) )
.strokeStyle("rgba(0,255,0,.5)")
.lineWidth(gMegaChart.getTickWidth);

/// Legend
vis.add(pv.Label)
.left(10)
.bottom(10)
.text(gMegaChart.ohlcLabel);

vis.add(pv.Label)
.left(10)
.bottom(30)
.text(gMegaChart.depthPriceLabel);

vis.add(pv.Label)
.left(10)
.bottom(50)
.text(gMegaChart.depthLabel);

vis.add(pv.Label)
.left(10)
.bottom(70)
.text(gMegaChart.volumeLabel);

vis.add(pv.Label)
.left(10)
.bottom(90)
.text(gMegaChart.timeLabel);

vis.add(pv.Label)
.left(10)
.bottom(110)
.text(gMegaChart.priceLabel);

///////////

function updateHistory(result)
{
	var maxVolume=0;
	gPlot=result.plot;
	for(var n=0; n<gPlot.length; n++)
	{
		gPlot[n][5]=result.date-n*result.period;
		if(gPlot[n][1]>gMegaChart.getMaxPrice()) gMegaChart.setMaxPrice(gPlot[n][1]);
		if(gPlot[n][2]<gMegaChart.getMinPrice()) gMegaChart.setMinPrice(gPlot[n][2]);
		if(gPlot[n][4]>maxVolume) maxVolume=gPlot[n][4];
	}
	
	//alert(gMegaChart.getMinPrice()+" "+gMegaChart.getMaxPrice());
	
	x.domain(result.date-gPlot.length*result.period,result.date);
	y.domain(gMegaChart.getMaxPrice(),gMegaChart.getMinPrice()).nice();
	volumeAxis.domain(0,maxVolume);
	vis.render();
}

function updateDepth(asks,bids)
{
	gMegaChart.setMinPrice(1000);
	gMegaChart.setMaxPrice(0);
	
	gAsks=[];//[asks[0][0],0];
	var total=0;
	for(var n=0; n<asks.length; n++)
	{
		total+=asks[n][1];
		gAsks.push([asks[n][0],total]);
		if(asks[n][0]>gMegaChart.getMaxPrice()) gMegaChart.setMaxPrice(asks[n][0]);
	}
	gAsks.push([gMegaChart.getMaxPrice(),total]);
	gMegaChart.setMaxDepth(total);
	
	
	gBids=[]; //[bids[0][0],0];
	total=0;
	for(var n=0; n<bids.length; n++)
	{
		total+=bids[n][1];
		gBids.push([bids[n][0],total]);
		if(bids[n][0]<gMegaChart.getMinPrice()) gMegaChart.setMinPrice(bids[n][0]);
	}
	gBids.push([gMegaChart.getMinPrice(),total]);
	if(total>gMegaChart.getMaxDepth()) gMegaChart.setMaxDepth(total);
	gBids.reverse();
	
	depthAxis.domain(0,gMegaChart.getMaxDepth());
	y.domain(gMegaChart.getMaxPrice(),gMegaChart.getMinPrice()).nice();
	
	//alert( "("+depthAxis(gAsks[0][1])+","+y(gAsks[0][0])+")");
	vis.render();
}

function placeOrder()
{
	alert("hello");
}

function moveMouse()
{
	//alert("hello");
	gMegaChart.setMouseY(y.invert(vis.mouse().y));
	gMegaChart.setMouseX(x.invert(vis.mouse().x));
	
	//alert(gMegaChart.mMouseX);
	
	//yLine.data([mouseY]);
	vis.render();
	//idx = x.invert(vis.mouse().x) >> 0; update();
}

function f1(d) { return(d > 0); }
function f2(d) { return( d ? "#eee" : "#000"); }
function f3(d) { return( x(d[0]) ); }
function f4(d) { return( y(d[1]) ); }
function f5(d) { return(d[0]); }

