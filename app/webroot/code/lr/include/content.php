<?

require("include/config.php");

function showHeader($title) {

global $conf_merchantAccountNumber;
global $conf_merchantStoreName;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Sample Store - <?=$title?></title>
</head>
<body>

<style type="text/css">
  root, body {
    background: #ffffff;

    margin: 0;
  }

  body, td, th {
    font-family: "Tahoma", Arial, Helvetica, sans-serif;
    font-size: 10pt;
    color:#333333;
  }
  
  .underline-hint {
    color:#666666;
    font-size: 9pt;
  }
  
  h1, h2, h3 {
    color: #333333;
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  }
  
  h1 {  
    font-size: 13pt;
  }
  
  h2 {  
    font-size: 12pt;
  }

  h3 {  
    font-size: 10pt;
  }
  
  table.form td {
    vertical-align: top;
  }
  
  .form .field-name,
  .form .field-value {
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 0;    
    padding-right: 10px;
  }
  
  pre.code {  
    padding: 8pt;
    background:#eeeeee;
  }
  
  .content h1,
  .content h2,  
  .content h3,  
  .content p, 
  .content table {
    margin: 0.5em 0 0.5em 0;
  }
  
  div.success,
  div.error {
    font-size: 8pt;
    padding: 3px;
  }
  
  div.success {
    background-color: #339933;
    color: #FFFFFF;
  }
  
  div.error {
    background-color: #CC0033;
    color: #FFFFFF;
  }

  table.item div.foto {
    width: 100px; 
    height:120px; 
    border: solid 1px #0066CC; 
    font-size: 20px; 
    color: #333366; 
    vertical-align: middle; 
    text-align: center;
  }
  
  table.item td {
    vertical-align: top;
  }
  
  table.item span.price {
    font-weight: bold;
    font-size: 16pt;
  }
  
  table.item p.description {
    font-size: 8pt;
    color:#666666;
  }

  table.item td.description-block,  
  table.item td.sci-description-block,
  table.item td.foto-block,
  td.buy-button-block {
    padding-top: 5px;
    padding-bottom: 5px;
  }
  
  table.item td.buy-button-block {
    text-align: center;
    padding-left: 0;
    padding-right: 0;        
  }

  table.item td.foto-block {
    width: 100px;
    padding-left: 0;
  }
    
  table.item td.description-block {
    padding-left: 10px;  
    padding-right: 10px;      
  }    
  
  table.item td.description-block h1 {
    margin-top: 0;
    color: #0066CC;
  }
  
    
  table.item td.sci-description-block {
    background:#eeeeee;
    border-left: solid 1px #666666;
    width: 40%;
    padding-left: 10px;
    padding-right: 10px;    
  }
  
  
</style>

<table style="width: 100%" cellspacing="0">
  <tr>
    <td style="vertical-align: top; padding: 10px 10px 10px 30px; background: #CCCC99;">
      <table style="width: 100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
          <td style="width: 33%">
            <h1>
              <a href="http://www.libertyreserve.com" style="font-size: 120%; color:#CE0701; text-decoration: none;">Liberty Reserve</a> Sample Store
            </h1>
          </td>
          <td style="width: 20%; vertical-align: top;">
            Merchant's account number:<br />
            <big><?=$conf_merchantAccountNumber?></big>
          </td>
          <td style="width: 20%; vertical-align: top;">
            Store name:<br />
            <big><?=$conf_merchantStoreName?></big>
          </td>
          <td>
          </td>
        </tr>
      </table></td>
  </tr>
  <tr class="content">
    <td style="vertical-align: top; padding: 10px 30px 10px 30px">

<?
}


function showFooter() {
?>
  
    </td>
  </tr>
</table>

</body>
</html>  
  
<?  
}

?>

