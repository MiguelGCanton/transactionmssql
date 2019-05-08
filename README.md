# transactionmssql
Actividad de agregar una compra a la base de datos adventureWorks2012, usando una api rest con php y usando transacciones


##instalacion 

aniadir en el archivo php.ini

extension=pdo_sqlsrv_72_ts
extension=sqlsrv_72_ts

y los archivos 
pdo_sqlsrv_72_ts
y sqlsrv_72_ts

en la carpeta C:\xampp\php\ext

despues reiniciar el servidor

##estructura de la api rest

recibe como parametros

en una estructura de form

customerId de tipo entero
lista con los productos a comprar con esta estructura:
'IDPRODUCTO1,CANTIDAD|IID-PRODUCTO2,2|...' Ej. '814,5|818,1'

##pruebas realizadas con los siguientes registros

customerId = 29825
lista = 776,1|776,2|776,3




