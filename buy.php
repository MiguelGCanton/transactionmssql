<?php
/*
$customerID = 29825;
$s = '776,1|776,2|776,3';
*/

$customerID = $_POST['customerId'];
$s = $_POST['lista'];
/*
$array = explode('|', $s);
print_r($array);*/
/* INSERT INTO [Sales].[SalesOrderHeader]
([RevisionNumber]
,[OrderDate]
,[DueDate]
,[ShipDate]
,[Status]
,[OnlineOrderFlag]
,[PurchaseOrderNumber]
,[AccountNumber]
,[CustomerID]
,[SalesPersonID]
,[TerritoryID]
,[BillToAddressID]
,[ShipToAddressID]
,[ShipMethodID]
,[CreditCardID]
,[CreditCardApprovalCode]
,[CurrencyRateID]
,[SubTotal]
,[TaxAmt]
,[Freight]
,[Comment]
,[rowguid]
)
VALUES
(8
,GETDATE()
,GETDATE()
,GETDATE()
,2
,1
,'SO1'
,'10-4020-000676'
,$customerID
,279
,5
,985
,985
,5
,16281
,'105041Vi84182'
,NULL
,12
,12
,21
,NULL
,NEWID())*/

// 29825, 1,12|2,23|4,123 



function createConnection(){
    $conn = new PDO( "sqlsrv:Server=GCANTON\SQLEXPRESS;Database=AdventureWorks2012", NULL, NULL);   
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  
    return $conn;
}
$conn = createConnection();
 
 echo "Connected to SQL Server\n";  
function addSaleHeader($customerID, $total,$conn){
    
    $query = " SET NOCOUNT ON;
    INSERT INTO [Sales].[SalesOrderHeader]
    ([RevisionNumber]
    ,[OrderDate]
    ,[DueDate]
    ,[ShipDate]
    ,[Status]
    ,[OnlineOrderFlag]
    ,[PurchaseOrderNumber]
    ,[AccountNumber]
    ,[CustomerID]
    ,[SalesPersonID]
    ,[TerritoryID]
    ,[BillToAddressID]
    ,[ShipToAddressID]
    ,[ShipMethodID]
    ,[CreditCardID]
    ,[CreditCardApprovalCode]
    ,[CurrencyRateID]
    ,[SubTotal]
    ,[TaxAmt]
    ,[Freight]
    ,[Comment]
    ,[rowguid]
    )
    VALUES
    (8
    ,GETDATE()
    ,GETDATE()
    ,GETDATE()
    ,1
    ,1
    ,'SO1'
    ,'10-4020-000676'
    ,$customerID
    ,279
    ,5
    ,985
    ,985
    ,5
    ,16281
    ,'105041Vi84182'
    ,NULL
    ,12
    ,12
    ,21
    ,NULL
    ,NEWID());
     SELECT SCOPE_IDENTITY() as id";

   
    $stmt = $conn->query( $query );  
    while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ){   
        $id = $row['id']  ;
    } 

    return $id;
}  

function processingShoppingList($shoppingList, $customerID, $idSalesHeader, $conn){
    foreach($shoppingList as $item){
       addSalesByElement($customerID, $idSalesHeader, $conn, $item[0], $item[1], $item['price']);
    }
}

function addSalesByElement($customerID, $idSalesHeader, $conn, $productId, $quantity, $standartCost){
    $query = "INSERT INTO [Sales].[SalesOrderDetail]
           ([SalesOrderID]
           ,[CarrierTrackingNumber]
           ,[OrderQty]
           ,[ProductID]
           ,[SpecialOfferID]
           ,[UnitPrice]
           ,[UnitPriceDiscount]
           ,[rowguid])
     VALUES
           ($idSalesHeader
           ,'4911-403C-98'
           ,$quantity
           ,776
           ,1
           ,$standartCost
           ,0
           ,NEWID())
    ";
    echo "<br>$query";
    $stmt = $conn->query($query);

}

/*
INSERT INTO [Sales].[SalesOrderDetail]
           ([SalesOrderID]
           ,[CarrierTrackingNumber]
           ,[OrderQty]
           ,[ProductID]
           ,[SpecialOfferID]
           ,[UnitPrice]
           ,[UnitPriceDiscount]
           ,[rowguid])
     VALUES
           (43659
           ,'4911-403C-98'
           ,12
           ,776
           ,1
           ,12
           ,0
           ,NEWID())
GO
*/ 

function getTotal($shoppingList, $conn ){ 
    $total = 0;
    foreach ($shoppingList as $item) {
        $query = "select StandardCost from Production.Product 
                where ProductID = $item[0]"; 
        $stmt = $conn->query($query);        
        while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ){   
            $costo = $row['StandardCost'] * $item[1];
            $total += $costo; 
        } 
    }

    return $total;
}

function getStandartCost($id, $conn ){ 

    $query = "select StandardCost from Production.Product 
            where ProductID = $id"; 
    $stmt = $conn->query($query);        
    $row = $stmt->fetch( PDO::FETCH_ASSOC ) ;  
    return $row['StandardCost'];
}

function parseBuyList($stringBuyList, $conn){

    $productList = array();
    $shoppingList = explode('|',$stringBuyList);
    foreach ( $shoppingList as $element ){
        $temp = explode(',', $element);
        $temp['price'] = getStandartCost($temp[0],$conn);
        $productList[] = $temp;
        
    }
   print_r($productList);
    return $productList;
}
$conn->beginTransaction();

try{
    $shoppingList = parseBuyList($s, $conn);
    echo '<br>shopping list';
    $total = getTotal($shoppingList,$conn);
    echo '<br>total';
    $idSalesHeader = addSaleHeader($customerID, $total, $conn);
    echo '<br>agregando elementos a la lista de compras';
    processingShoppingList($shoppingList, $customerID, $idSalesHeader, $conn);

    $conn->commit();

}catch(PDOException $e){
    print_r($e);
    echo 'un error ha ocurrido, la operacion se ha detenido';
    $conn->rollBack();
}

?>

