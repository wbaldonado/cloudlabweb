<?php

namespace App\Support;

/**
 * Built-in email templates for CloudLab Latam campaigns.
 * Placeholders: {{first_name}}, {{name}}, {{logo_url}}, {{site_url}}, {{cta_url}}, {{cta_label}}
 */
class CampaignStarterTemplates
{
    /**
     * @return list<array{id:string,name:string,subject:string,cta_label:string,html:string}>
     */
    public static function all(): array
    {
        return [
            self::invitationServices(),
            self::freeConsultation(),
            self::cloudMigration(),
        ];
    }

    public static function find(string $id): ?array
    {
        foreach (self::all() as $tpl) {
            if ($tpl['id'] === $id) {
                return $tpl;
            }
        }

        return null;
    }

    protected static function invitationServices(): array
    {
        return [
            'id' => 'invitation-services',
            'name' => 'Invitación — Servicios CloudLab',
            'subject' => '{{first_name}}, transforma tu negocio con IA y cloud',
            'cta_label' => 'Conoce CloudLab Latam',
            'html' => <<<'HTML'
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;font-family:Arial,Helvetica,sans-serif;background:#ffffff">
  <tr>
    <td style="padding:32px 24px 16px;text-align:center;background:#05070a">
      <img src="{{logo_url}}" alt="CloudLab Latam" width="160" style="max-width:160px;height:auto;display:inline-block">
    </td>
  </tr>
  <tr>
    <td style="padding:32px 24px 8px">
      <h1 style="margin:0 0 16px;font-size:24px;line-height:1.3;color:#05070a">Hola {{first_name}},</h1>
      <p style="margin:0 0 16px;font-size:16px;line-height:1.6;color:#374151">
        En <strong>CloudLab Latam</strong> ayudamos a empresas como la tuya a crecer con
        <strong>inteligencia artificial</strong>, <strong>cloud computing</strong> y <strong>analytics</strong>.
      </p>
      <p style="margin:0 0 24px;font-size:16px;line-height:1.6;color:#374151">
        Automatiza procesos, reduce costos y toma mejores decisiones con datos. ¿Te gustaría conocer cómo lo hacemos?
      </p>
    </td>
  </tr>
  <tr>
    <td style="padding:8px 24px 32px;text-align:center">
      <a href="{{cta_url}}" style="display:inline-block;background:#2563eb;color:#ffffff;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:16px">{{cta_label}}</a>
    </td>
  </tr>
  <tr>
    <td style="padding:0 24px 32px">
      <p style="margin:0;font-size:14px;line-height:1.5;color:#6b7280;text-align:center">
        Visítanos en <a href="{{site_url}}" style="color:#2563eb">{{site_url}}</a>
      </p>
    </td>
  </tr>
</table>
HTML,
        ];
    }

    protected static function freeConsultation(): array
    {
        return [
            'id' => 'free-consultation',
            'name' => 'Consulta gratuita',
            'subject' => 'Agenda una consulta gratuita con CloudLab Latam',
            'cta_label' => 'Agendar consulta gratuita',
            'html' => <<<'HTML'
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;font-family:Arial,Helvetica,sans-serif">
  <tr>
    <td style="padding:24px;text-align:center;background:linear-gradient(135deg,#05070a 0%,#1e3a5f 100%);border-radius:12px 12px 0 0">
      <img src="{{logo_url}}" alt="CloudLab Latam" width="140" style="max-width:140px;height:auto;margin-bottom:16px">
      <h1 style="margin:0;font-size:22px;color:#ffffff;line-height:1.3">Consulta gratuita de 30 minutos</h1>
    </td>
  </tr>
  <tr>
    <td style="padding:28px 24px;background:#f9fafb;border-left:1px solid #e5e7eb;border-right:1px solid #e5e7eb">
      <p style="margin:0 0 16px;font-size:16px;line-height:1.6;color:#374151">Hola {{first_name}},</p>
      <p style="margin:0 0 16px;font-size:16px;line-height:1.6;color:#374151">
        Queremos invitarte a una <strong>sesión sin costo</strong> para revisar tus objetivos de negocio
        y mostrarte soluciones concretas en cloud, IA y automatización.
      </p>
      <ul style="margin:0 0 24px;padding-left:20px;font-size:15px;line-height:1.8;color:#374151">
        <li>Diagnóstico de oportunidades</li>
        <li>Roadmap personalizado</li>
        <li>Sin compromiso</li>
      </ul>
    </td>
  </tr>
  <tr>
    <td style="padding:0 24px 32px;text-align:center;background:#f9fafb;border:1px solid #e5e7eb;border-top:0;border-radius:0 0 12px 12px">
      <a href="{{cta_url}}" style="display:inline-block;background:#059669;color:#ffffff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:700;font-size:16px">{{cta_label}}</a>
      <p style="margin:20px 0 0;font-size:13px;color:#6b7280">El botón es trackeado — verás cuántos contactos hacen clic.</p>
    </td>
  </tr>
</table>
HTML,
        ];
    }

    protected static function cloudMigration(): array
    {
        return [
            'id' => 'cloud-migration',
            'name' => 'Migración a la nube',
            'subject' => '¿Tu empresa lista para la nube? CloudLab Latam te acompaña',
            'cta_label' => 'Ver soluciones en la web',
            'html' => <<<'HTML'
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;font-family:Arial,Helvetica,sans-serif;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden">
  <tr>
    <td style="padding:28px 24px 12px;text-align:center">
      <img src="{{logo_url}}" alt="CloudLab Latam" width="150" style="max-width:150px;height:auto">
    </td>
  </tr>
  <tr>
    <td style="padding:8px 24px 20px">
      <h2 style="margin:0 0 12px;font-size:20px;color:#05070a;text-align:center">Migra a la nube con confianza</h2>
      <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#374151">Estimado/a {{name}},</p>
      <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#374151">
        La migración cloud ya no es opcional: reduce infraestructura, mejora seguridad y escala según demanda.
        En CloudLab Latam diseñamos, migramos y operamos tu entorno en AWS, Azure o Google Cloud.
      </p>
    </td>
  </tr>
  <tr>
    <td style="padding:0 24px 28px;text-align:center">
      <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto">
        <tr>
          <td style="border-radius:8px;background:#05070a">
            <a href="{{cta_url}}" style="display:inline-block;padding:13px 26px;color:#ffffff;text-decoration:none;font-weight:600;font-size:15px">{{cta_label}}</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
HTML,
        ];
    }
}
