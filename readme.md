## Servicio Rest Backend - Billetera Virtual

Servicio configurado para realizar operaciones a nivel de base de datos, a continuacion se relaciona el conjunto de funcionalidades:

- Registrar cliente
- Recargar billetera de cliente
- Solicitud de pago
- Confirmar pago
- Consultar saldo

Lista de codigos de error: Registrar Cliente
- Mensaje: El cliente no fue registrado, Cod: 100
- Mensaje: Algunos datos no son validos para crear el cliente, Cod: 200

Lista de codigos de error: Recargar billetera de cliente
- Mensaje: Lo sentimos, no se realizo la carga en su billetera, Cod: 300
- Mensaje: Recarga no procesada, esta billetera actualmente no existe, Cod: 400

Lista de codigos de error: Solicitud de pago
- Mensaje: Lo sentimos, no se pudo crear la solicitud de pago a su billetera, Cod: 500
- Mensaje: Solicitud de pago no procesada, esta billetera actualmente no existe, Cod: 600
- Mensaje: Esta billetera tiene fondos insuficientes - pre-validación, Cod: 700

Lista de codigos de error: Confirmar pago
- Mensaje: Lo sentimos, no se realizo la confirmación del pago, Cod: 800
- Mensaje: Este pago por confirmar no existe, Cod: 900
- Mensaje: Esta solicitud de pago ya fue confirmada, Cod: 110
- Mensaje: Esta solicitud de pago ya expiro, por favor genere una nueva, Cod: 120
- Mensaje: Esta billetera tiene fondos insuficientes - post-validación, Cod: 130

Lista de codigos de error: Consultar saldo
- Mensaje: Lo sentimos, no pudimos consultar su saldo, Cod: 140
- Mensaje: Esta billetera actualmente no existe, Cod: 150


Estados de Pago
0 -> pendiente por confirmar
1 -> confirmado

## Datos de Configuración

**Información del servicio**
- Tecnologias: PHP,MySQL
- Dirección IP: 67.207.81.224
- Servicio de nube: Digitalocean

**Crendeciales Email**
- correo: walletnotify01@gmail.com
- pass: Walletnotify01*