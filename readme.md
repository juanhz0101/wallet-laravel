## Servicio Web - Billetera Virtual

Servicio Web configurado para realizar operaciones a nivel de base de datos, a continuacion se relaciona el conjunto de funcionalidades:

- Registrar cliente
- Recargar billetera de cliente
- Solicitud de pago

Lista de codigos de error: Registrar Cliente
- Mensaje: El cliente no fue registrado, Cod: 01
- Mensaje: Algunos datos no son validos para crear el cliente, Cod: 02

Lista de codigos de error: Recargar billetera de cliente
- Mensaje: Lo sentimos, no se realizo la carga en su billetera, Cod: 03
- Mensaje: Esta billetera actualmente no existe, Cod: 04

Lista de codigos de error: Solicitud de pago
- Mensaje: Esta billetera tiene fondos insuficientes - pre-validación, Cod: 05

Estados de Pago
0 -> pendiente por confirmar
1 -> confirmado

##Datos de Configuración

Crendeciales Email
- correo: walletnotify01@gmail.com
- pass: Walletnotify01*