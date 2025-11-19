<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo #{{ $payment->id }}</title>
    <style>
        body { font-family: sans-serif; padding: 40px; max-width: 800px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 2px solid #eee; margin-bottom: 30px; }
        .info-table { width: 100%; margin-bottom: 30px; }
        .info-table td { padding: 5px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .items-table th { background-color: #f8f9fa; }
        .total { text-align: right; font-size: 24px; font-weight: bold; color: #333; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #777; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h1>CLÍNICA OFTALMOLÓGICA APRECIA</h1>
        <p>Comprobante de Pago</p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Nro. Recibo:</strong> {{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</td>
            <td><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td><strong>Paciente:</strong> {{ $payment->patient->name }}</td>
            <td><strong>CI:</strong> {{ $payment->patient->carnet_identidad }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Método de Pago</th>
                <th>Importe</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $payment->service->name }}</td>
                <td>{{ ucfirst($payment->payment_method) }}</td>
                <td>Bs. {{ number_format($payment->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        Total Pagado: Bs. {{ number_format($payment->amount, 2) }}
    </div>

    <div class="footer">
        <p>Gracias por confiar en nosotros.</p>
        <p>Clínica Aprecia - Salud Visual</p>
    </div>
</body>
</html>