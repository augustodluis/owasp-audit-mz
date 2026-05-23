<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; color:#2D2D2D; max-width: 640px; margin:0 auto; padding: 16px;">

<h2 style="color:#C8102E; margin: 0 0 12px;">Relatorio de Auditoria de Seguranca</h2>

<p>Caro destinatario,</p>

<p>Em anexo encontra o relatorio completo da auditoria automatizada executada pela plataforma
<strong>OWASP-AUDIT-MZ</strong>.</p>

<table cellpadding="6" cellspacing="0" style="border-collapse: collapse; width:100%;">
    <tr><td style="background:#f6f6f6;"><strong>Identificador</strong></td><td style="background:#f6f6f6;">#{{ $audit->id }}</td></tr>
    <tr><td><strong>Alvo</strong></td><td><code>{{ $audit->target_url }}</code></td></tr>
    <tr><td><strong>Inicio</strong></td><td>{{ $audit->started_at }}</td></tr>
    <tr><td><strong>Conclusao</strong></td><td>{{ $audit->finished_at }}</td></tr>
    <tr><td><strong>Paginas descobertas</strong></td><td>{{ $audit->pages->count() }}</td></tr>
</table>

<h3 style="margin: 20px 0 8px;">Sumario por risco</h3>

<table cellpadding="8" cellspacing="0" style="border-collapse: collapse; width:100%;">
    <tr>
        <td style="background:#C8102E; color:#fff; text-align:center;">
            <strong>Alto</strong><br><span style="font-size:22px;">{{ $totals['High'] }}</span>
        </td>
        <td style="background:#f0ad4e; color:#fff; text-align:center;">
            <strong>Medio</strong><br><span style="font-size:22px;">{{ $totals['Medium'] }}</span>
        </td>
        <td style="background:#5bc0de; color:#fff; text-align:center;">
            <strong>Baixo</strong><br><span style="font-size:22px;">{{ $totals['Low'] }}</span>
        </td>
        <td style="background:#777; color:#fff; text-align:center;">
            <strong>Informativo</strong><br><span style="font-size:22px;">{{ $totals['Informational'] }}</span>
        </td>
    </tr>
</table>

<p style="margin-top: 20px;">O detalhe tecnico de cada vulnerabilidade, incluindo evidencias, exemplos de codigo
vulneravel, correccoes recomendadas e referencias OWASP, encontra-se no PDF anexo.</p>

<p style="font-size: 12px; color:#777; border-top: 1px solid #ddd; padding-top: 12px; margin-top: 24px;">
    Este e-mail foi enviado automaticamente pela plataforma OWASP-AUDIT-MZ.<br>
    Auditoria executada por: {{ $audit->user->name }} ({{ $audit->user->email }})
</p>

</body>
</html>
