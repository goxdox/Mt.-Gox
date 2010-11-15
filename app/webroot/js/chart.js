/*
**************  MEGA CHART!!!!!!!!!!!!!!! *******
 
What we want from the chart:
	Full width of screen
	Variable height
	Price line or
	Candelsticks for > than tick time scale
	Variable time scale for history
	Order Depth
		As dots
		Cumaltive
	Your open orders
	Volume
	
	Mouse move should:
		-Show cross hairs and info in bottom left:
			-Price
			Time
			Volume
			Depth
			average price for that depth
		
	Click should:
		open buy/sell order dialog
		
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
	
	
	this.prices = prices;
	function prices(d){ return(d[0]); }


	this.timeLabel = function(){ return("Date: "+mMouseX); }
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
			else return("Bid Depth: "+gBids[depthIndex][1]);
		}else
		{
			
			return("Ask Depth: "+gAsks[depthIndex][1]);
		}
	}
	// the average price to buy this many
	this.depthPriceLabel = function()
	{
		// how many
		
		return("depthPrice");
	}
	
	this.volumeLabel = function(){  return("Volume: "+mMouseX); }
}

var gMegaChart=new MegaChart();

/* Sizing and scales. */
var w = 800,
    h = 600,
    
    gAsks=[],
    gBids=[],
    depthAxis=pv.Scale.linear(0, 1000).range(0, w/2),
    x = pv.Scale.linear(0, 50).range(0, w),
    //y = pv.Scale.linear(gMegaChart.getMinPrice, .70 ).range(0, h);
    //y = pv.Scale.linear(function(){ return(gMinPrice);}, function(){ return(gMaxPrice);} ).range(0, h);
    y = pv.Scale.linear(0, 1).range(0, h);

/* The root panel. */
var vis = new pv.Panel()
    .width(w)
    .height(h)
    .bottom(20)
    .left(30)
    .right(10)
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
    .text(x.tickFormat);

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

/// Legend
vis.add(pv.Label)
.left(10)
.bottom(10)
.text(gMegaChart.depthLabel);

vis.add(pv.Label)
.left(10)
.bottom(30)
.text(gMegaChart.depthPriceLabel);

vis.add(pv.Label)
.left(10)
.bottom(50)
.text(gMegaChart.priceLabel);

vis.add(pv.Label)
.left(10)
.bottom(70)
.text(gMegaChart.volumeLabel);

vis.add(pv.Label)
.left(10)
.bottom(90)
.text(gMegaChart.timeLabel);

///////////

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
	//alert(gMegaChart.getMinPrice()+" "+gMaxPrice+" "+gMaxDepth);
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

