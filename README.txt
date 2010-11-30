
Users should be identified by email
	- we need a new verified email field
		- they can verify their email by accepting a payment from someone
			- or by pushing a button somewhere?
			- or they have to when they make an account
				We send an email with a token. They have to click the link.
				
When they get an email payment they can associate that email with an existing account

An account can have multiple confirmed emails
	- it should have one primary email
	
An account needs a common name for displaying in the account history.

Do they need to be able to view the details of a transaction?

Makes a new account on claiming the SendEmail since they could just choose to attach the email to an existing account.

Drop an email from the account?






Merchant Services:
	Widget 
	startTxn.php
	mtgox.com/users/confirm
	customerConfirm.php
	ipn.php
	checkTxn.php
	process incoming
	-Merchant Page
	getPayments.php
	-get widget page
	
	docs



Shouldn't trust any paypal stuff for 20 days.
	Slowly allow them to withdraw more funds
	
	



http://dev.amnuts.com/slider/

Date formating:
http://blog.stevenlevithan.com/archives/date-time-format

Always using the sell price in the DB


A bids 1 for a BTC
B asks 1 for a BTC this means he is really asking 1*SPREAD


Trade prices are in between what the buyer and seller paid

Ticker Prices are the prices you have to pay to make the trade happen


Main:
	Add your Funds to the top of the screen

Trade:
	Expandable
	Total Price
		
	Orders
		sortable columns
		
	Ticker
		update the ticker 
		vol over last 24 hours
	
History:
	-Graph
	various time scales
	volume
	price
	
	
Your Trades:
	Last N trades
	Paging through them
	Sorting
	
Add Funds:
	Paypal
	Mailing
	except bitcoins
	
Withdraw Funds:
	Form that mails us
	
Support:
	Faq
	email
	
Settings:
	Email notification on trade
	Change password
	
	




-----
User (UserID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, Username, Password, Email, USD, BTC );

Trades (TradeID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, BuyerID INT UNSIGNED, SellerID INT UNSIGNED, Price Float, Date INT UNSIGNED); 

Ask (OrderID, UserID INT UNSIGNED, Price Float, Status Date INT UNSIGNED);
Bid (OrderID, UserID INT UNSIGNED, Price Float, Date INT UNSIGNED);
 
AddUSD (UserID, FromEmail, Amount, Method, Date);
AddBTC	(UserID, RecvAddr, status, Amount, Date);  // 0 not received, 1 ok, 2 old
RemUSD (UserID, ToEmail, Amount, Method, Date);
RemBTC (UserID, Dest, Amount, Date);
Ticker (High, Low, Vol, HighBuy, LowSell, HighDate, LowDate);
History (High,Low,Vol,Date);


----

Insert into Activity (userID,deltaBTC,deltaUSD,type,typeID,typeData,btc,usd,date) values (1,100,1,2,0,'',0,0,1279424523);

insert into History (High,Low,Open,Close,Volume,Date) values (0,0,0,0,0,1279424586);
insert into Ticker values (0,0,0,0,0,0);

insert into AddBTC (UserID,RecvAddr,status,Amount,Date) values (1,'1Fw5tAaz5rKacwCrzF1LEEc6Zp4amLhYhq',0,1.5,1279322032);


ALTER TABLE Users add Column TradeNotify TINYINT default 0 after Email;
ALTER TABLE Users add Column MerchOn TINYINT default 0 after TradeNotify;
ALTER TABLE Users add Column MerchNotifyURL varchar(255) after MerchOn;
ALTER TABLE Users add Column FundsHeld INT default 0 after BTC;
ALTER TABLE Activity add Column actID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY before userID);


## INT switch
Update users set USD=USD*10,BTC=BTC*10;
Update Asks set Amount=Amount*10;
Update Bids set Amount=Amount*10;
Update Trades set Amount=Amount*10;
Update AddBTC set Amount=Amount*10;
Update MerchantOrders set Amount=Amount*10,AmountRecv=AmountRecv*10;
Update BTCRecord set Amount=Amount*10;

alter table Users change USD USD INT default 0;
alter table Users change BTC BTC INT default 0;
alter table Asks change Amount Amount INT;
alter table Bids change Amount Amount INT;
alter table Trades change Amount Amount INT;
alter table AddBTC change Amount Amount INT;
alter table MerchantOrders change Amount Amount INT;
alter table MerchantOrders change AmountRecv AmountRecv INT;
alter table BTCRecord change Amount Amount INT;
alter table Orders add Column amountHeld INT default 0;



##
alter table Users add Column payAPIOn TINYINT default 0 after MerchOn;
alter table Users add Column merchToken VARCHAR(20) after MerchNotifyURL;

alter table Users add Column lastLogIP varchar(15) after paypalTrust;
alter table Users add Column signUpIP varchar(15) after paypalTrust;

##########
alter table Users add Column SendNotify TINYINT default 1 after TradeNotify;
create table EmailMap (EmailID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, UserID INT UNSIGNED, Email VARCHAR(60), 
	Status TINYINT, Date INT UNSIGNED ) ENGINE = INNODB;
create table SendMoney (SendID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, FromID INT UNSIGNED, 
	Currency TINYINT, Amount INT, ToEmail VARCHAR(60), Token VARCHAR(10), Note VARCHAR(255), 
	Status TINYINT, Date INT UNSIGNED) ENGINE = INNODB;
	
###########

ALTER TABLE MerchantOrders add Column notifyURL VARCHAR(255) after Custom;

Alter table Users add column TradeFee float default 0.0065 after paypalTrust;

alter table Asks add column DarkStatus TINYINT default 0 after Status;
alter table Bids add column DarkStatus TINYINT default 0 after Status;

###
Alter table Users add column marginBalance INT default 0 after BTC;

