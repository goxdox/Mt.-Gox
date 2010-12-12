/*
**************  MEGA CHART!!!!!!!!!!!!!!! *******
 
What we want from the chart:
	
	start history time at correct dates
	
	Hook it up to realtime feed
	
	Order Depth
		As dots
		-Cumaltive
		
	Your open orders
		Cancel or change open orders
		Allow you to change the bounds of the Y axis
	
	Mouse move should:
			average price for that depth
		
	Click should:
		open buy/sell order dialog
		
		
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
	
	var mLastFetchTime=0;
	var mPeriodLength=0;
	
	
	var mDepthMode=0;
	
	var mShowDepth=true;
	var mShowVolume=true;
	var mShowOrders=true;
	var mShowPrice=true;
	var mShowCandles=true;
	
	var mHoldPriceRange=false;
	
	// options
	this.setDepthMode=function(x){ mDepthMode=x; }
	this.getDepthMode=function(){ return(mDepthMode); }
	this.setShowDepth=function(x){ mShowDepth=x; }
	this.getShowDepth=function(){ return(mShowDepth); }
	this.setShowVolume=function(x){ mShowVolume=x; }
	this.getShowVolume=function(){ return(mShowVolume); }
	this.setShowOrders=function(x){ mShowOrders=x; }
	this.getShowOrders=function(){ return(mShowOrders); }
	this.setShowPrice=function(x){ mShowPrice=x; }
	this.getShowPrice=function(){ return(mShowPrice); }
	this.setShowCandles=function(x){ mShowCandles=x; }
	this.getShowCandles=function(){ return(mShowCandles); }
	///
	
	this.setMouseX = function(x){ mMouseX=x; }
	this.setMouseY = function(x){ mMouseY=x; }
	this.setMaxDepth = function(x){ mMaxDepth=x; }

	
	this.getMouseX = function(){ return(mMouseX); }
	this.getMouseY = function(){ return(mMouseY); }
	this.getMaxDepth = function(){ return(mMaxDepth); }
	
	this.getTickWidth = function(){ return(w/(500*(i.dx/w))); }
	
	
	this.prices = prices;
	this.date = date;
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
		plotIndex = pv.search(gPlot.map(date), mMouseX)-1;
		plotIndex = plotIndex < 0 ? (-plotIndex - 2) : plotIndex;
		//alert(plotIndex+" "+mMouseX)
		if(plotIndex==-1 || plotIndex>=gPlot.length) return("Volume: 0");
		else return("Volume: "+i4_addCommas(i4_round(gPlot[plotIndex][4],0)));
	}
	
	this.ohlcLabel = function()
	{
		plotIndex = pv.search(gPlot.map(date), mMouseX)-1;
		plotIndex = plotIndex < 0 ? (-plotIndex - 2) : plotIndex;
		//alert(plotIndex+" "+mMouseX)
		if(plotIndex==-1 || plotIndex>=gPlot.length) return("");
		else return("o:"+i4_round(gPlot[plotIndex][0],3)+
					" h:"+i4_round(gPlot[plotIndex][1],3)+
					" l:"+i4_round(gPlot[plotIndex][2],3)+
					" c:"+i4_round(gPlot[plotIndex][3],3));
	}
	
	this.getOrderColor=function(order)
	{
		//alert(order);
		var alpha=1;
		var red=0;
		var green=0;
		var blue=0;
		if(order.status==2) alpha=.5;
		
		if(order.type==2)
		{
			red=255;
			
		}else 
		{
			blue=255;
		}
		
		if(order.dark==1) 
		{
			red -= 50;
			green -= 50;
			blue -= 50;
		}else if(order.dark==2)
		{
			red -= 100;
			green -= 100;
			blue -= 100;
		}
		
		if(red<0) red=0;
		if(green<0) green=0;
		if(blue<0) blue=0;
		
		
		return("rgba("+red+','+green+','+blue+','+alpha+")")
	}
	
	this.setPriceRange = function(low,high)
	{
		$("#highPriceMC").text(high);
		$("#lowPriceMC").text(low);
		focusY.domain(high,low).nice();
	}
	
	this.resetPriceRange = function(low,high)
	{
		if(!mHoldPriceRange)
		{
			$( "#priceSlider" ).slider( "values" , 0 , [low*100] );
			$( "#priceSlider" ).slider( "values" , 1 , [high*100] );
			
			gMegaChart.setPriceRange(low,high);
		}
	}
	
	this.timer = function()
	{
		if(mLastFetchTime)
		{
			if(mPeriodLength)
			{
				if((new Date().getTime() / 1000)>mLastFetchTime+mPeriodLength)
				{
					mLastFetchTime=0;
					fetchPrice();
				}
			}else
			{ // reltime so just move the end point over
			}
		}
	}
	
	this.updateOrders= function(result)
	{
		//alert("orders "+result[0].price);
		gOrders=result;
		vis.render();
	}
	
	this.updateHistory= function(result)
	{
		/*	
			var result={"period":86400,"date":1246838400,"plot":[[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],
			                                                     [0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],
			                                                     [0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],
			                                                     [0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],
			                                                     [0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],
			                                                     [0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],
			                                                     [0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],
			                                                     [0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],
			                                                     [0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0,0,0,0,0],[0.0495,0.0495,0.0495,0.0495,20],[0.0594,0.0859,0.0594,0.0858,75],[0.0909,0.0931,0.0772,0.0808,574],[0.0818,0.0818,0.0743,0.0747,262],[0.0742,0.0792,0.0663,0.0792,575],[0.0792,0.0818,0.0505,0.0574,2160],[0.0505,0.0677,0.0505,0.0626,2403],[0.0616,0.0616,0.0505,0.0545,496],[0.0554,0.0594,0.0505,0.0505,1551],[0.05,0.056,0.05,0.056,877],[0.053,0.0605,0.053,0.06,3374],[0.06,0.062,0.054,0.0589,4390],[0.0597,0.0699,0.0571,0.0699,8058],[0.0698,0.0698,0.0582,0.0627,3021],[0.065,0.0689,0.056,0.0679,4022],[0.061,0.065,0.06,0.0611,2601],[0.0627,0.0633,0.06,0.06,3599],[0.06,0.065,0.059,0.06,9821],[0.0623,0.0623,0.057,0.057,3494],[0.0581,0.061,0.058,0.061,5034],[0.062,0.0624,0.0607,0.0623,1395],[0.0608,0.0622,0.059,0.059,2619],[0.059,0.061,0.059,0.0609,2201],[0.0609,0.0735,0.0593,0.071,13631],[0.068,0.0709,0.0665,0.07,1310],[0.06,0.0754,0.06,0.067,14061],[0.068,0.07,0.0614,0.07,2062],[0.0665,0.068,0.0645,0.0645,3592],[0.0655,0.0695,0.0645,0.067,4404],[0.067,0.067,0.065,0.0653,4463],[0.0632,0.0679,0.062,0.0655,10731],[0.0655,0.0769,0.0624,0.07,13186],[0.07,0.0733,0.067,0.068,2954],[0.0679,0.0679,0.0667,0.0667,741],[0.0667,0.0667,0.065,0.0655,4200],[0.0655,0.0669,0.0644,0.0664,10444],[0.065,0.0664,0.0612,0.066,18649],[0.066,0.0669,0.063,0.0649,4297],[0.0649,0.0665,0.0649,0.065,6712],[0.065,0.0665,0.0641,0.0648,4229],[0.0648,0.0658,0.064,0.064,3870],[0.064,0.065,0.063,0.065,9010],[0.065,0.065,0.0641,0.0641,6174],[0.0647,0.0648,0.064,0.064,3173],[0.0646,0.069,0.0321,0.065,34193],[0.0641,0.0649,0.06,0.06,14887],[0.062,0.0629,0.0596,0.0629,7165],[0.061,0.0634,0.0601,0.0634,8151],[0.061,0.063,0.0608,0.0608,892],[0.0612,0.0624,0.0612,0.0624,3301],[0.0625,0.064,0.0605,0.0616,8459],[0.0627,0.0627,0.0616,0.0616,910],[0.0616,0.062,0.0603,0.061,3457],[0.061,0.062,0.061,0.062,2345],[0.0611,0.0624,0.0611,0.0611,1734],[0.061,0.0618,0.0601,0.0618,4940],[0.0619,0.065,0.0619,0.0637,7749],[0.0621,0.0621,0.0615,0.0615,794],[0.062,0.064,0.0607,0.0622,10076],[0.062,0.175,0.061,0.062,14015],[0.0625,0.0625,0.0604,0.0604,3652],[0.0618,0.0619,0.0618,0.0618,730],[0.0609,0.0609,0.059,0.059,7262],[0.059,0.061,0.0576,0.061,7093],[0.0609,0.0627,0.06,0.0627,12852],[0.0621,0.0634,0.062,0.0621,14308],[0.0631,0.0633,0.0623,0.0627,5736],[0.0624,0.0624,0.0615,0.0622,11560],[0.0621,0.063,0.0615,0.0623,15505],[0.0621,0.0624,0.0621,0.0622,684],[0.0622,0.0624,0.0617,0.062,2153],[0.062,0.0623,0.0619,0.062,12058],[0.0621,0.0623,0.0619,0.0622,10752],[0.062,0.0627,0.0617,0.0619,7086],[0.062,0.062,0.0615,0.0618,23476],[0.0617,0.0619,0.0615,0.0619,7917],[0.0619,0.062,0.0619,0.062,1717],[0.0619,0.0619,0.0613,0.0614,13060],[0.0614,0.0614,0.061,0.0611,7173],[0.0611,0.062,0.0608,0.0613,33995],[0.0613,0.063,0.0609,0.0614,27527],[0.0615,0.0635,0.0612,0.0628,33433],[0.0631,0.067,0.0628,0.067,43693],[0.067,0.088,0.01,0.0868,139287],[0.0841,0.12,0.068,0.0938,187847],[0.093,0.1301,0.08,0.0965,50684],[0.091,0.103,0.091,0.095,14093],[0.094,0.099,0.082,0.0949,25661],[0.092,0.105,0.092,0.105,47557],[0.1045,0.1045,0.065,0.102,37217],[0.1,0.119,0.092,0.105,24643],[0.1,0.103,0.1,0.101,6285],[0.102,0.1045,0.1,0.102,18222],[0.103,0.103,0.097,0.1024,25665],[0.1,0.1019,0.097,0.097,6235],[0.1,0.103,0.094,0.099,31855],[0.0972,0.109,0.097,0.107,44867],[0.1015,0.109,0.1015,0.1025,34604],[0.108,0.109,0.1045,0.1055,4423],[0.108,0.19,0.108,0.115,13800],[0.0901,0.19,0.0901,0.132,30300],[0.133,0.18,0.133,0.1503,19219],[0.151,0.19,0.151,0.1877,65606],[0.1845,0.191,0.1731,0.1731,21525],[0.1799,0.19,0.173,0.19,28687],[0.1876,0.199,0.1875,0.1989,26709],[0.194,0.1989,0.171,0.1925,40338],[0.1925,0.1955,0.172,0.1955,21259],[0.192,0.195,0.1905,0.1938,5853],[0.1938,0.275,0.125,0.1931,61473],[0.1988,0.236,0.1934,0.23,29698],[0.2101,0.2639,0.2101,0.26,36622],[0.262,0.5,0.2402,0.39,32756],[0.47,0.47,0.286,0.34,77219],[0.3459,0.37,0.2261,0.243,118204],[0.251,0.323,0.199,0.2163,47983],[0.2101,0.24,0.14,0.2362,30387],[0.2299,0.24,0.21,0.2231,5032],[0.2289,0.29,0.223,0.2682,40720],[0.2799,0.3,0.2683,0.276,21916],[0.276,0.299,0.2702,0.279,16844],
			                                                     [0.279,0.2828,0.2682,0.2682,8508],[0.2683,0.275,0.223,0.223,33931],[0.223,0.29,0.2116,0.2299,32872]]};
			*/                                                 
		var maxVolume=0;
		var minPrice=1000;
		var maxPrice=0;
		gPlot=result.plot;
		//var dateStr="";
		if(result.period)
		{
			for(var n=0; n<gPlot.length; n++)
			{
				gPlot[n][5]=result.date-((gPlot.length-n)*result.period);
				//dateStr += ", "+gPlot[n][5];
				if(gPlot[n][1]>maxPrice)maxPrice=gPlot[n][1];
				if(gPlot[n][3]>0 && gPlot[n][2]<minPrice) minPrice=gPlot[n][2];
				if(gPlot[n][4]>maxVolume) maxVolume=gPlot[n][4];
			}
			//$('#error').text(dateStr);
			focusX.domain(result.date-gPlot.length*result.period,result.date);
			contextX.domain(result.date-gPlot.length*result.period,result.date);
		}else
		{	// realtime
			if(result.now) 
			gMegaChart.setShowCandles(false);
			
			for(var n=0; n<gPlot.length; n++)
			{
				if(gPlot[n][0]>maxPrice) maxPrice=gPlot[n][0];
				if(gPlot[n][0]<minPrice) minPrice=gPlot[n][0];
				if(gPlot[n][4]>maxVolume) maxVolume=gPlot[n][4];
			}
			
			focusX.domain(result.date-24*60*60,result.date);
			contextX.domain(result.date-24*60*60,result.date);
			//$('#error').text(result.date-24*60*60+" "+result.date+" "+gMegaChart.getMaxPrice()+" "+gMegaChart.getMinPrice());
			
		}
		
		gMegaChart.resetPriceRange(minPrice,maxPrice);
		
		contextY.domain(maxPrice,minPrice).nice();
		//$('#error').text(result.date-gPlot.length*result.period+" "+result.date+" "+gMegaChart.getMaxPrice()+" "+gMegaChart.getMinPrice()+" "+focusY(.3));
		volumeAxis.domain(0,maxVolume);
		
		
		vis.render();	
	}
	
	this.init = function()
	{
		$( "#priceSlider" ).slider({
			orientation: "vertical",
			range: true,
			values: [ 0, 100 ],
			slide: function( event, ui ) 
			{
				gMegaChart.setPriceRange(ui.values[ 0 ]/100, ui.values[ 1 ]/100 );
				mHoldPriceRange=true;
				vis.render();
				//alert("hi");
			}
		});
		$( "#priceSlider" ).slider( "values" , 0 , [0] );
		$( "#priceSlider" ).slider( "values" , 1 , [.5] );
		
		//window.setInterval(gMegaChart.timer, 5000);
	}
}

var gMegaChart=new MegaChart();


/* Sizing and scales. */
var w = $("#megaChart").width()-35,
    h1 = $("#megaChart").height()-20-5-60,    
    h2 = 30,
    i = {x: w-300, dx:300},
    gAsks=[],
    gBids=[],
    gPlot=[],
    gOrders=[],
    volumeAxis=pv.Scale.linear(0, 1000).range(0, h1/3),
    depthAxis=pv.Scale.linear(0, 1000).range(0, w/2),
    contextY=pv.Scale.linear(0, 1).range(0, h2),
    contextX=pv.Scale.linear(0, 50).range(-15, w-58),
    focusX = pv.Scale.linear(0, 50).range(-15, w-58),
    focusY = pv.Scale.linear(0, 1).range(0, h1);



/* The root panel. */
var vis = new pv.Panel()
	.canvas('megaChart')
    .bottom(20)
    .left(30)
    .right(5)
    .top(5);

var focus=vis.add(pv.Panel)
	.def("init", function() {
		 var d1 = contextX.invert(i.x),
		     d2 = contextX.invert(i.x + i.dx),
		     i1= pv.search.index(gPlot, d1, gMegaChart.date) - 1,
		     i2= pv.search.index(gPlot, d2, gMegaChart.date) + 1,
		     dd = gPlot.slice( Math.max(0, i1), i2);
		 focusX.domain(d1, d2);
		 //if(dd.length) $('#status').text("("+i.x+","+i.dx+")"+d1+" , "+d2+" , "+i1+" , "+i2+" , "+gPlot[0][5]);
		 
		 var max=pv.max(dd,f5);
		 var min=pv.min(dd,f5);
		 if(max==min) max=min+.01;
		 gMegaChart.resetPriceRange(min,max);
		 //if(dd.length) $('#status').text(""+dd[0][1]+" "+dd.length+" "+dd[dd.length-1][1]+" "+max+" "+min);
		 return dd;
	})
	.cursor('crosshair')
    .events("all")
    .event("mousemove" , moveMouse )
    .event("mousedown", placeOrder )
    .top(0)
    .height(h1);


/* X-axis ticks. */
focus.add(pv.Rule)
    .data(function(){ return(focusX.ticks()); })
    .visible(f1)
    .left(focusX)
    .strokeStyle("#eee")
  .add(pv.Rule)
    .bottom(-5)
    .height(5)
    .strokeStyle("#000")
  .anchor("bottom").add(pv.Label)
   .text(function(t){ return(dateFormat(t*1000,"mm/dd HH:MM:ss"));});
  //  .text( dateFormat(x.tickFormat*1000,"mm/dd HH:MM:ss") );
  
// Y-axis ticks. 
focus.add(pv.Rule)
    .data(function(){ return(focusY.ticks(6)); })
    .top(focusY)
    .strokeStyle(f2)
  .anchor("left").add(pv.Label)
    .text(focusY.tickFormat);

var overlayPanel=vis.add(pv.Panel)
	.top(0)
	.left(0)
	.height(h1)
	.width(w);

// Y-axis cursor 
overlayPanel.add(pv.Rule)
    .top(function(){ return(focusY(gMegaChart.getMouseY())); })
    .strokeStyle("rgba(255,0,0,.5)")
  .anchor("left");

//X-axis cursor 
overlayPanel.add(pv.Rule)
    .left(function(){ return(focusX(gMegaChart.getMouseX())); })
    .strokeStyle("rgba(255,0,0,.5)")
  .anchor("top");


// Asks
focus.add(pv.Area)
.visible(gMegaChart.getShowDepth)
.overflow("hidden")
.data(function() {return(gAsks);})
.interpolate("step-before")
.left(1)
.width(function(d){ return(depthAxis(d[1])); })
.top(function(d){ return(focusY(d[0]));} )
.fillStyle("rgba(121,173,210,.5)")
.anchor("right").add(pv.Line)
.lineWidth(1);

// Bids
focus.add(pv.Area)
.visible(gMegaChart.getShowDepth)
.overflow("hidden")
.data(function() {return(gBids);})
.interpolate("step-after")
.left(1)
.width(function(d){ return depthAxis(d[1]);} )
.top(function(d){ return focusY(d[0]); })
.fillStyle("rgba(255,173,210,.5)")
.anchor("right").add(pv.Line)
.lineWidth(1);


// candles
focus.add(pv.Rule)
.visible(gMegaChart.getShowCandles)
.overflow("hidden")
.data(function(){ return(focus.init()); })
.left(function(d){ return focusX(d[5]); })
.top(function(d){ return focusY(Math.min(d[1], d[2]));})
.height(function(d){ return(-(Math.abs(focusY(d[1]) - focusY(d[2])))); } )
.strokeStyle(function(d){ return(d[0] < d[3] ? "#00FF00" : "#FF0000"); } )
.add(pv.Rule)
.top(function(d){ return focusY(Math.min(d[0], d[3])); } )
.height(function(d){ return(-(1+Math.abs(focusY(d[0]) - focusY(d[3])))); } )
.lineWidth(gMegaChart.getTickWidth);

// Price Line
focus.add(pv.Line)
.visible(gMegaChart.getShowPrice)
.overflow("hidden")
.data(function() {return(gPlot);})
.left(function(d) {return focusX(d[5]);} )
.top(function(d){ return focusY(d[0]); })
.strokeStyle("steelblue")
.lineWidth(2);

//order balls
focus.add(pv.Dot)
.visible(gMegaChart.getShowOrders)
//.overflow("hidden")
.data(function() {return(gOrders);})
.left(w-50)
.top(function(d){ return focusY(d.price); })
.strokeStyle(gMegaChart.getOrderColor)
.fillStyle(gMegaChart.getOrderColor);

//volume bars
focus.add(pv.Rule)
.visible(gMegaChart.getShowVolume)
.overflow("hidden")
.data(function() {return(gPlot);})
.left(function(d){ return focusX(d[5]); })
.bottom(1)
.height(function(d){ return volumeAxis(d[4]); } )
.strokeStyle("rgba(0,255,255,.5)")
.lineWidth(gMegaChart.getTickWidth);

/// Legend
overlayPanel.add(pv.Label)
.left(10)
.bottom(10)
.text(gMegaChart.ohlcLabel);

overlayPanel.add(pv.Label)
.left(10)
.bottom(30)
.text(gMegaChart.depthPriceLabel);

overlayPanel.add(pv.Label)
.left(10)
.bottom(50)
.text(gMegaChart.depthLabel);

overlayPanel.add(pv.Label)
.left(10)
.bottom(70)
.text(gMegaChart.volumeLabel);

overlayPanel.add(pv.Label)
.left(10)
.bottom(90)
.text(gMegaChart.timeLabel);

overlayPanel.add(pv.Label)
.left(10)
.bottom(110)
.text(gMegaChart.priceLabel);

/////////////////////////////////

// Context panel (zoomed out). 
var context = vis.add(pv.Panel)
    .bottom(0)
    .height(h2);

// X-axis ticks. 
context.add(pv.Rule)
    .data(function(){ return(contextX.ticks()); })
    .left(contextX)
    .strokeStyle("#eee")
  .anchor("bottom").add(pv.Label)
    .text(function(t){ return(dateFormat(t*1000,"mm/dd HH:MM:ss"));});

// Y-axis ticks. 
context.add(pv.Rule)
    .bottom(0);

// Context area chart. 
context.add(pv.Area)
    .data(function() {return(gPlot);})
    .left(function(d){ return contextX(d[5]); })
    .bottom(1)
    .height(function(d){ return(h2-contextY(d[0])); })
    .fillStyle("lightsteelblue")
  .anchor("top").add(pv.Line)
    .strokeStyle("steelblue")
    .lineWidth(2);

// The selectable, draggable focus region. 
context.add(pv.Panel)
	.data([i])
	.cursor("crosshair")
	.events("all")
	.event("mousedown", pv.Behavior.select())
	.event("select", focus)
	.add(pv.Bar)
		.left(function(d){return d.x; })
		.width(function(d){ return d.dx; })
		.fillStyle("rgba(255, 128, 128, .4)")
		.cursor("move")
		.event("mousedown", pv.Behavior.drag())
		.event("drag", focus);

///////////

function updateOptions()
{
	vis.render();
}



function updateDepth(asks,bids)
{
	var minPrice=1000;
	var maxPrice=0;
	
	gAsks=[];//[asks[0][0],0];
	var total=0;
	for(var n=0; n<asks.length; n++)
	{
		total+=asks[n][1];
		gAsks.push([asks[n][0],total]);
		if(asks[n][0]>maxPrice) maxPrice=asks[n][0];
	}
	gAsks.push([maxPrice,total]);
	gMegaChart.setMaxDepth(total);
	
	
	gBids=[]; //[bids[0][0],0];
	total=0;
	for(var n=0; n<bids.length; n++)
	{
		total+=bids[n][1];
		gBids.push([bids[n][0],total]);
		if(bids[n][0]<minPrice) minPrice=bids[n][0];
	}
	gBids.push([minPrice,total]);
	if(total>gMegaChart.getMaxDepth()) gMegaChart.setMaxDepth(total);
	gBids.reverse();
	
	depthAxis.domain(0,gMegaChart.getMaxDepth());
	gMegaChart.resetPriceRange(minPrice,maxPrice);
	
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
	gMegaChart.setMouseY(focusY.invert(vis.mouse().y));
	gMegaChart.setMouseX(focusX.invert(vis.mouse().x));
	
	//alert(gMegaChart.mMouseX);
	
	//yLine.data([mouseY]);
	overlayPanel.render();
	//idx = x.invert(vis.mouse().x) >> 0; update();
}

function f1(d) { return(d > 0); }
function f2(d) { return( d ? "#eee" : "#000"); }
function f3(d) { return( focusX(d[0]) ); }
function f4(d) { return( focusY(d[1]) ); }
function f5(d) { return(d[0]); }

