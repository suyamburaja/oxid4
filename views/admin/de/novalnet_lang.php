<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License
 * that is bundled with this package in the file freeware_license_agreement.txt
 *
 * @author Novalnet <technic@novalnet.de>
 * @copyright Novalnet
 * @license GNU General Public License
 * @link https://www.novalnet.de
 *
 */
$sLangName = 'Deutsch';

$aLang = array(
    'charset'                       => 'UTF-8',
    'NOVALNET_MENU'                 => 'Novalnet',
    'NOVALNET_CONFIG_MENU'          => 'Novalnet-Zahlungseinstellungen',
    'NOVALNET_ADMIN_CONFIG_MESSAGE' => 'Um zusätzliche Einstellungen vorzunehmen, loggen Sie sich in das <a class="novalnet_config_link" target="_blank" href="https://admin.novalnet.de">Novalnet-Administrationsportal</a> ein.<br/>Um sich in das Portal einzuloggen, benötigen Sie einen Account bei Novalnet. Falls Sie diesen noch nicht haben, kontaktieren Sie bitte <a href="mailto:sales@novalnet.de">sales@novalnet.de</a> (Tel: +49 (089) 923068320)<br/><br/>Um die Zahlungsart PayPal zu verwenden, geben Sie bitte Ihre PayPal-API-Daten in das <a class="novalnet_config_link" target="_blank" href="https://admin.novalnet.de">Novalnet-Händleradministrationsportal</a> ein',
    'NOVALNET_ADMIN_CONFIG_TITLE'   => 'Loggen Sie sich hier mit Ihren Novalnet Händler-Zugangsdaten ein. Um neue Zahlungsarten zu aktivieren, kontaktieren Sie bitte <a href="mailto:support@novalnet.de" style="color:#fff;">support@novalnet.de</a>',
    'NOVALNET_LINK_URL'             => 'https://www.novalnet.de',
    'NOVALNET_GLOBAL_CONFIGURATION' => 'Novalnet Haupteinstellungen',
    'NOVALNET_CREDITCARD'           => 'Novalnet Kreditkarte',
    'NOVALNET_SEPA'                 => 'Novalnet Lastschrift SEPA',
    'NOVALNET_INVOICE'              => 'Novalnet Kauf auf Rechnung',
    'NOVALNET_PREPAYMENT'           => 'Novalnet Vorauskasse',
    'NOVALNET_PAYPAL'               => 'Novalnet PayPal',
    'NOVALNET_INSTANTBANK'          => 'Novalnet Sofort',
    'NOVALNET_IDEAL'                => 'Novalnet iDEAL',
    'NOVALNET_EPS'                  => 'Novalnet eps',    
    'NOVALNET_GIROPAY'              => 'Novalnet giropay',
    'NOVALNET_PRZELEWY24'           => 'Novalnet Przelewy24',

    'NOVALNET_PRODUCT_ACTIVATION_KEY_TITLE'       => 'Aktivierungsschlüssel des Produkts',
    'NOVALNET_VENDOR_ID_TITLE'                    => 'Händler-ID',
    'NOVALNET_AUTHCODE_TITLE'                     => 'Authentifizierungscode',
    'NOVALNET_PRODUCT_ID_TITLE'                   => 'Projekt-ID',
    'NOVALNET_TARIFF_ID_TITLE'                    => 'Tarif-ID',
    'NOVALNET_ACCESS_KEY_TITLE'                   => 'Zahlungs-Zugriffsschlüssel',
    'NOVALNET_TEST_MODE_MAIL_TITLE'               => 'E-Mail-Benachrichtigung für Testbuchungen aktivieren',
    'NOVALNET_MANUAL_CHECK_LIMIT_TITLE'           => 'Limit für onhold-Buchungen setzen (in der kleinsten Währungseinheit, z.B. 100 Cent = entsprechen 1.00 EUR)',
    'NOVALNET_AUTO_REFILL_TITLE'                  => 'Automatisches Eintragen aktivieren',
    'NOVALNET_PROXY_SERVER_TITLE'                 => 'Proxy-Server',
    'NOVALNET_GATEWAY_TIMEOUT_TITLE'              => 'Zeitlimit der Schnittstelle (in Sekunden)',
    'NOVALNET_REFERRER_ID_TITLE'                  => 'Partner-ID',
    'NOVALNET_LOGO_CONFIGURATION_TITLE'           => 'Steuerung der angezeigten Logos',
    'NOVALNET_CHECKOUT_NOVALNET_LOGO_TITLE'       => 'Novalnet-Logo anzeigen',
    'NOVALNET_CHECKOUT_PAYMENT_LOGO_TITLE'        => 'Logo der Zahlungsart anzeigen',
    'NOVALNET_SUBSCRIPTION_CONFIGURATION_TITLE'   => 'Verwaltung dynamischer Abonnements',
    'NOVALNET_TARIFF_PERIOD_TITLE'                => 'Zeitraum des Tarifs',
    'NOVALNET_TARIFF_PERIOD2_AMOUNT_TITLE'        => 'Betrag für den folgenden Abonnementzyklus (in der kleinsten Währungseinheit, z.B. 100 Cent = entsprechen 1.00 EUR)',
    'NOVALNET_TARIFF_PERIOD2_TITLE'               => 'Zeitraum für den folgenden Abonnementzyklus',
    'NOVALNET_CALLBACKSCRIPT_CONFIGURATION_TITLE' => 'Verwaltung des Händlerskripts',
    'NOVALNET_CALLBACK_TEST_MODE_TITLE'           => 'Deaktivieren Sie die IP-Adresskontrolle (nur zu Testzwecken) ',
    'NOVALNET_CALLBACK_TEST_MODE_DESCRIPTION'     => 'Diese Option ermöglicht eine manuelle Ausführung. Bitte deaktivieren Sie diese Option, bevor Sie Ihren Shop in den LIVE-Modus schalten, um nicht autorisierte Zugriffe von externen Parteien (außer von Novalnet) zu vermeiden.',
    'NOVALNET_CALLBACK_ENABLE_MAIL_TITLE'         => 'Email-Benachrichtigung für Callback aktivieren',
    'NOVALNET_CALLBACK_TO_ADDRESS_TITLE'          => 'Emailadresse (An)',
    'NOVALNET_CALLBACK_BCC_ADDRESS_TITLE'         => 'Emailadresse (Bcc)',
    'NOVALNET_NOTIFY_URL_TITLE'                   => 'URL für Benachrichtigungen',

	'NOVALNET_PAYMENT_LOGO_DESC' => 'Das Logo der Zahlungsart wird auf der Checkout-Seite angezeigt',
    'NOVALNET_PRODUCT_ACTIVATION_KEY_DESCRIPTION' => 'Novalnet-Aktivierungsschlüssel für das Produkt eingeben. Um diesen Aktivierungschlüssel für das Produkt zu erhalten, gehen Sie zum <a class="novalnet_config_link" target="_blank" style="color:#0080c9;cursor:pointer;font-size:11px;font-weight:bold;" href="https://admin.novalnet.de">Novalnet-Admin-Portal</a> - Projekte: Informationen zum jeweiligen Projekt - Parameter Ihres Shops: API Signature (Aktivierungsschlüssel des Produkts).',
    'NOVALNET_TARIFF_ID_DESCRIPTION'              => 'Novalnet-Tarif-ID auswählen',
    'NOVALNET_TEST_MODE_MAIL_DESCRIPTION'         => 'Sie erhalten ab jetzt E-Mail-Benachrichtigungen zu jeder Testbestellung im Webshop',
    'NOVALNET_MANUAL_CHECK_LIMIT_DESCRIPTION'     => 'Falls der Bestellbetrag das angegebene Limit übersteigt, wird die Transaktion ausgesetzt, bis Sie diese selbst bestätigen.',
    'NOVALNET_PAYPAL_MANUAL_CHECK_LIMIT_DESCRIPTION' => 'Falls der Bestellbetrag das angegebene Limit übersteigt, wird die Transaktion ausgesetzt, bis Sie diese selbst bestätigen. Um diese Option zu verwenden, müssen Sie die Option Billing Agreement (Zahlungsvereinbarung) in Ihrem PayPal-Konto aktiviert haben.Kontaktieren Sie dazu bitte Ihren Kundenbetreuer bei PayPal.',
    'NOVALNET_AUTO_REFILL_DESCRIPTION'            => 'Die Zahlungsdetails werden automatisch während des Checkout-Vorgangs in das Zahlungsformular eingetragen',
    'NOVALNET_PROXY_SERVER_DESCRIPTION'           => 'Geben Sie die IP-Adresse Ihres Proxyservers zusammen mit der Nummer des Ports ein und zwar in folgendem Format: IP-Adresse : Nummer des Ports (falls notwendig)',
    'NOVALNET_GATEWAY_TIMEOUT_DESCRIPTION'        => 'Falls die Verarbeitungszeit der Bestellung das Zeitlimit der Schnittstelle überschreitet, wird die Bestellung nicht ausgeführt',
    'NOVALNET_REFERRER_ID_DESCRIPTION'            => 'Geben Sie die Partner-ID der Person / des Unternehmens ein, welche / welches Ihnen Novalnet empfohlen hat',
    'NOVALNET_LOGO_CONFIGURATION_DESCRIPTION'     => 'Sie können die Anzeige der Logos auf der Checkout-Seite aktivieren oder deaktivieren',
    'NOVALNET_CHECKOUT_NOVALNET_LOGO_DESCRIPTION' => 'Das Novalnet-Logo wird auf der Checkout-Seite angezeigt',
    'NOVALNET_CHECKOUT_PAYMENT_LOGO_DESCRIPTION'  => 'Das Logo der Zahlungsart wird auf der Checkout-Seite angezeigt',
    'NOVALNET_TARIFF_PERIOD_DESCRIPTION'          => 'Zeitraum des ersten Abonnementzyklus (z.B. 1d/1m/1y)',
    'NOVALNET_TARIFF_PERIOD2_AMOUNT_DESCRIPTION'  => 'Betrag für den folgenden Abonnementzyklus',
    'NOVALNET_TARIFF_PERIOD2_DESCRIPTION'         => 'Zeitraum des folgenden Abonnementzyklus (z.B. 1d/1m/1y)',
    'NOVALNET_CALLBACK_DEBUG_MODE_DESCRIPTION'    => 'Setzen Sie den Debugmodus, um das Händlerskript im Debugmodus aufzurufen',
    'NOVALNET_CALLBACK_TO_ADDRESS_DESCRIPTION'    => 'Emailadresse des Empfängers',
    'NOVALNET_CALLBACK_BCC_ADDRESS_DESCRIPTION'   => 'Emailadresse des Empfängers für Bcc',
    'NOVALNET_NOTIFY_URL_DESCRIPTION'             => 'Der URL für Benachrichtigungen dient dazu, Ihre Datenbank / Ihr System auf einem aktuellen Stand zu halten und den Novalnet-Transaktionsstatus abzugleichen',

    'NOVALNET_TEST_MODE_TITLE'                 => 'Testmodus aktivieren',
    'NOVALNET_BUYER_NOTIFICATION_TITLE'        => 'Benachrichtigung des Käufers',
    'NOVALNET_REFERENCE_ONE_TITLE'             => '1. Referenz zur Transaktion',
    'NOVALNET_REFERENCE_TWO_TITLE'             => '2. Referenz zur Transaktion',
    'NOVALNET_FRAUD_MODULE_TITLE'              => 'Betrugsprüfung aktivieren',
    'NOVALNET_FRAUD_MODULE_AMOUNT_LIMIT_TITLE' => 'Mindestwarenwert für Betrugsprüfungsmodul (in der kleinsten Währungseinheit, z.B. 100 Cent = entsprechen 1.00 EUR)',
    'NOVALNET_CREDITCARD_3D_ACTIVE_TITLE'      => '3D-Secure aktivieren',
    'NOVALNET_CREDITCARD_3D_FRAUD_ACTIVE_TITLE'=> '3D-Secure-Zahlungen unter vorgegebenen Bedingungen durchführen',
    'NOVALNET_CREDITCARD_AMEX_TITLE'           => 'AMEX-Logo anzeigen',
    'NOVALNET_CREDITCARD_MAESTRO_TITLE'        => 'Maestro-Logo anzeigen',
    'NOVALNET_CREDITCARD_CARTASI_TITLE'        => 'CartaSi-Logo anzeigen',
    'NOVALNET_SHOP_TYPE_TITLE'                 => 'Einkaufstyp',
    'NOVALNET_FORM_MODE_TITLE'                 => 'Formularmodus',
    'NOVALNET_CREDITCARD_YEAR_LIMIT_TITLE'     => 'Limit für das Ablaufjahr',
    'NOVALNET_SEPA_DUE_DATE_TITLE'             => 'Abstand (in Tagen) bis zum SEPA-Einzugsdatum',
    'NOVALNET_SEPA_PAYMENT_AUTOFILL_TITLE'     => 'Automatisches Eintragen der Zahlungsdaten aktivieren',
    'NOVALNET_INVOICE_DUE_DATE_TITLE'          => 'Fälligkeitsdatum (in Tagen)',
    'NOVALNET_PAYMENT_REFERENCE_ONE_TITLE'     => 'Verwendungszweck 1: (Novalnet Rechnungsnummer)',
    'NOVALNET_PAYMENT_REFERENCE_TWO_TITLE'     => 'Verwendungszweck 2: (TID)',
    'NOVALNET_PAYMENT_REFERENCE_THREE_TITLE'   => 'Verwendungszweck 3: (Bestellnummer)',
    'NOVALNET_GUARANTEE_CONFIGURATION_TITLE'   => 'Einstellungen für die Zahlungsgarantie',
    'NOVALNET_GUARANTEE_PAYMENT_TITLE'         => 'Zahlungsgarantie aktivieren',
    'NOVALNET_GUARANTEE_MINIMUM_AMOUNT_TITLE'  => 'Mindestbestellbetrag (in der kleinsten Währungseinheit, z.B. 100 Cent = entsprechen 1.00 EUR)',
    'NOVALNET_GUARANTEE_PAYMENT_FORCE_TITLE'   => 'Zahlung ohne Zahlungsgarantie erzwingen',

    'NOVALNET_TEST_MODE_DESCRIPTION'                 => 'Die Zahlung wird im Testmodus durchgeführt, daher wird der Betrag für diese Transaktion nicht eingezogen',
    'NOVALNET_BUYER_NOTIFICATION_DESCRIPTION'        => 'Der eingegebene Text wird auf der Checkout-Seite angezeigt',
    'NOVALNET_REFERENCE_DESCRIPTION'                 => 'Diese Referenz wird auf Ihrem Kontoauszug erscheinen',
    'NOVALNET_FRAUD_MODULE_DESCRIPTION'              => 'Um den Käufer einer Transaktion zu authentifizieren, werden die PIN automatisch generiert und an den Käufer geschickt. Dieser Dienst wird nur für Kunden aus DE, AT und CH angeboten',
    'NOVALNET_FRAUD_MODULE_AMOUNT_LIMIT_DESCRIPTION' => 'Geben Sie den Mindestwarenwert ein, von dem ab das Betrugsprüfungsmodul aktiviert sein soll',
    'NOVALNET_CREDITCARD_3D_ACTIVE_DESCRIPTION'      => '3D-Secure wird für Kreditkarten aktiviert. Die kartenausgebende Bank fragt vom Käufer ein Passwort ab, welches helfen soll, betrügerische Zahlungen zu verhindern. Dies kann von der kartenausgebenden Bank als Beweis verwendet werden, dass der Käufer tatsächlich der Inhaber der Kreditkarte ist. Damit soll das Risiko von Chargebacks verringert werden',
    'NOVALNET_CREDITCARD_3D_FRAUD_ACTIVE_DESCRIPTION' => 'Wenn 3D-Secure in dem darüberliegenden Feld nicht aktiviert ist, sollen 3D-Secure-Zahlungen nach den Einstellungen zum Modul im Novalnet-Admin-Portal unter "3D-Secure-Zahlungen durchführen (gemäß vordefinierten Filtern und Einstellungen)" durchgeführt werden.<br>Wenn die vordefinierten Filter und Einstellungen des Moduls "3D-Secure durchführen" zutreffen, wird die Transaktion als 3D-Secure-Transaktion durchgeführt, ansonsten als Nicht-3D-Secure-Transaktion.<br>Beachten Sie bitte, dass das Modul "3D-Secure-Zahlungen durchführen (gemäß vordefinierten Filtern und Einstellungen)" im Novalnet-Admin-Portal konfiguriert sein muss, bevor es hier aktiviert wird.<br>Für weitere Informationen sehen Sie sich bitte die Beschreibung dieses Betrugsprüfungsmoduls an (unter dem Reiter "Betrugsprüfungsmodule" unterhalb des Menüpunkts "Projekte" für das ausgewähte Projekt im Novalnet-Admin-Portal) oder kontaktieren Sie das Novalnet-Support-Team.',
    'NOVALNET_CREDITCARD_AMEX_DESCRIPTION'           => 'AMEX-Logo auf der Checkout-Seite anzeigen',
    'NOVALNET_CREDITCARD_MAESTRO_DESCRIPTION'        => 'Maestro-Logo auf der Checkout-Seite anzeigen',
    'NOVALNET_CREDITCARD_CARTASI_DESCRIPTION'        => 'CartaSi-Logo auf der Checkout-Seite anzeigen',
    'NOVALNET_SHOP_TYPE_DESCRIPTION'                 => 'Einkaufstyp auswählen',
    'NOVALNET_FORM_MODE_DESCRIPTION'                 => 'Auf der Grundlage dieser Auswahl wird das Kreditkartenformular angezeigt',
    'NOVALNET_CREDITCARD_YEAR_LIMIT_DESCRIPTION'     => 'Geben Sie die Zahl für das maximale Limit des Kreditkarten-Ablaufjahres ein. Falls dieses Feld leer ist, wird ein Limit von 25 Jahren ab dem aktuellen Jahr als Standard gesetzt',
    'NOVALNET_SEPA_DUE_DATE_DESCRIPTION'             => 'Geben Sie die Anzahl der Tage ein, nach denen die Zahlung verarbeitet werden soll (muss größer als 6 Tage sein)',
    'NOVALNET_SEPA_PAYMENT_AUTOFILL_DESCRIPTION'     => 'Für registrierte Benutzer werden die SEPA-Lastschriftdetails automatisch in das Zahlungsformular eingetragen',
    'NOVALNET_INVOICE_DUE_DATE_DESCRIPTION'          => 'Geben Sie die Anzahl der Tage ein, binnen derer die Zahlung bei Novalnet eingehen soll (muss größer als 7 Tage sein). Falls dieses Feld leer ist, werden 14 Tage als Standard-Zahlungsfrist gesetzt',
    'NOVALNET_GUARANTEE_CONFIGURATION_DESCRIPTION'   => 'Grundanforderungen für die Zahlungsgarantie
                                                         <ul>
                                                             <li>Zugelassene Staaten: AT, DE, CH</li>
                                                             <li>Zugelassene Währung: EUR</li>
                                                             <li>Mindestbetrag der Bestellung >= 9,99 EUR</li>
                                                             <li>Mindestalter des Endkunden >= 18 Jahre</li>
                                                             <li>Rechnungsadresse und Lieferadresse müssen übereinstimmen</li>
                                                             <li>Geschenkgutscheine / Coupons sind nicht erlaubt</li>
                                                         </ul>',
    'NOVALNET_GUARANTEE_MINIMUM_AMOUNT_DESCRIPTION'  => 'Diese Einstellung überschreibt die Standardeinstellung für den Mindest-Bestellbetrag. Anmerkung: der Mindest-Bestellbetrag sollte größer oder gleich 9,99 EUR sein',
    'NOVALNET_GUARANTEE_PAYMENT_FORCE_DESCRIPTION'   => 'Falls die Zahlungsgarantie aktiviert ist (wahr), die oben genannten Anforderungen jedoch nicht erfüllt werden, soll die Zahlung ohne Zahlungsgarantie verarbeitet werden',

    'NOVALNET_OPTION_NONE'               => 'Keiner',
    'NOVALNET_FRAUD_MODULE_OPTION_CALL'  => 'PIN-by-Callback',
    'NOVALNET_FRAUD_MODULE_OPTION_SMS'   => 'PIN-by-SMS',
    'NOVALNET_ONE_CLICK_SHOP'            => 'Kauf mit einem Klick',
    'NOVALNET_ZERO_AMOUNT_BOOK'          => 'Transaktionen mit Betrag 0',
    'NOVALNET_IFRAME'                    => 'IFrame-Formular',
    'NOVALNET_LOCAL_FORM'                => 'Lokales Formular',
    'NOVALNET_REDIRECT_FORM'             => 'Umleitung',

    'NOVALNET_MANAGE_TRANSACTION_TITLE' => 'Ablauf der Buchung steuern',
    'NOVALNET_MANAGE_TRANSACTION_LABEL' => 'Wählen Sie bitte einen Status aus',
    'NOVALNET_PLEASE_SELECT'            => '--Auswählen--',
    'NOVALNET_CONFIRM'                  => 'Bestätigen',
    'NOVALNET_CANCEL'                   => 'Stornieren',
    'NOVALNET_UPDATE'                   => 'Ändern',

    'NOVALNET_UPDATE_AMOUNT_TITLE'      => 'Betrag ändern',
    'NOVALNET_UPDATE_AMOUNT_LABEL'      => 'Betrag der Transaktion ändern',
    'NOVALNET_CENTS'                    => '(in der kleinsten Währungseinheit,<br> z.B. 100 Cent = entsprechen 1.00 EUR)',

    'NOVALNET_REFUND_AMOUNT_TITLE'                => 'Ablauf der Rückerstattung',
    'NOVALNET_REFUND_AMOUNT_LABEL'                => 'Geben Sie bitte den erstatteten Betrag ein',
    'NOVALNET_REFUND_REFERENCE_LABEL'             => 'Referenz für die Rückerstattung',
    'NOVALNET_AMOUNT_REFUNDED_PARENT_TID_MESSAGE' => '<br><br>Die Rückerstattung wurde für die TID: %s mit dem Betrag %s durchgeführt',
    'NOVALNET_AMOUNT_REFUNDED_CHILD_TID_MESSAGE'  => '. Ihre neue TID für den erstatteten Betrag: %s',
    'NOVALNET_STATUS_UPDATE_CONFIRMED_MESSAGE_WITH_DUEDATE' => '<br><br>Die Transaktion mit der TID: %s wurde erfolgreich bestätigt und das Fälligkeitsdatum auf %s gesetzt<br>',
    'NOVALNET_STATUS_UPDATE_CONFIRMED_MESSAGE'    => '<br><br>Die Buchung wurde am %s um %s Uhr bestätigt',
    'NOVALNET_STATUS_UPDATE_CANCELED_MESSAGE'     => '<br><br>Die Transaktion wurde am %s um %s Uhr storniert',
    'NOVALNET_AMOUNT_UPDATED_MESSAGE'             => '<br><br><br>Der Betrag der Transaktion %s wurde am %s um %s Uhr erfolgreich geändert',

    'NOVALNET_TRANSACTION_DETAILS'            => 'Novalnet-Transaktionsdetails<br>',
    'NOVALNET_TRANSACTION_ID'                 => 'Novalnet Transaktions-ID: ',
    'NOVALNET_TEST_ORDER'                     => '<br>Testbestellung',
    'NOVALNET_UPDATE_AMOUNT_DUEDATE_TITLE'    => 'Betrag / Fälligkeitsdatum ändern',
    'NOVALNET_UPDATE_DUEDATE_LABEL'           => 'Fälligkeitsdatum der Transaktion',
    'NOVALNET_INVOICE_COMMENTS_TITLE'         => '<br>Überweisen Sie bitte den Betrag an die unten aufgeführte Bankverbindung unseres Zahlungsdienstleisters Novalnet',
    'NOVALNET_DUE_DATE'                       => '<br>Fälligkeitsdatum: ',
    'NOVALNET_ACCOUNT'                        => '<br>Kontoinhaber: ',
    'NOVALNET_AMOUNT'                         => '<br>Betrag: ',
    'NOVALNET_INVOICE_SINGLE_REF_DESCRIPTION' => '<br>Bitte verwenden Sie nun der unten angegebenen Verwendungszweck für die Überweisung, da nur so Ihr Geldeingang zugeordnet werden kann:',
    'NOVALNET_INVOICE_MULTI_REF_DESCRIPTION'  => '<br>Bitte verwenden Sie einen der unten angegebenen Verwendungszwecke für die Überweisung, da nur so Ihr Geldeingang zugeordnet werden kann:',
    'NOVALNET_INVOICE_SINGLE_REFERENCE'       => '<br>Verwendungszweck: ',
    'NOVALNET_INVOICE_MULTI_REFERENCE'        => '<br>Verwendungszweck %s: ',
    'NOVALNET_ORDER_NO'                       => 'Bestellnummer ',

    'NOVALNET_INVALID_STATUS'                         => 'Wählen Sie bitte einen Status aus',
    'NOVALNET_INVALID_AMOUNT'                         => 'Ungültiger Betrag',
    'NOVALNET_INVALID_DUEDATE'                        => 'Ungültiges Fälligkeitsdatum',
    'NOVALNET_INVALID_PAST_DUEDATE'                   => 'Das Datum sollte in der Zukunft liegen',
    'NOVALNET_INVALID_SEPA_DETAILS'                   => 'Ihre Kontodaten sind ungültig',
    'NOVALNET_INVALID_GUARANTEE_MINIMUM_AMOUNT_ERROR' => 'Der Mindestbetrag sollte bei mindestens 9,99 EUR',
    'NOVALNET_INVALID_TARIFF_PERIOD_ERROR'            => 'Geben Sie bitte eine gültige Abonnementsperiode ein',
    'NOVALNET_CONFIRM_CAPTURE'                        => 'Sind Sie sicher, dass Sie die Zahlung einziehen möchten?',
    'NOVALNET_CONFIRM_CANCEL'                         => 'Sind Sie sicher, dass Sie die Zahlung stornieren wollen?',
    'NOVALNET_CONFIRM_AMOUNT_UPDATE'                  => 'Sind Sie sich sicher, dass Sie den Bestellbetrag ändern wollen?',
    'NOVALNET_CONFIRM_DUEDATE_UPDATE'                 => 'Sind Sie sich sicher, dass Sie den Betrag / das Fälligkeitsdatum der Bestellung ändern wollen?',
    'NOVALNET_CONFIRM_REFUND'                         => 'Sind Sie sicher, dass Sie den Betrag zurückerstatten möchten?',
    'NOVALNET_CONFIRM_BOOKED'                         => 'Sind Sie sich sicher, dass Sie den Bestellbetrag buchen wollen?',

    'NOVALNET_SUBSCRIPTION_CANCEL_TITLE'     => 'Stornierung von Abonnements',
    'NOVALNET_SUBSCRIPTION_CANCEL_LABEL'     => 'Wählen Sie bitte den Grund aus',
    'NOVALNET_SUBSCRIPTION_CANCEL_REASON_1'  => 'Angebot zu teuer',
    'NOVALNET_SUBSCRIPTION_CANCEL_REASON_2'  => 'Betrug',
    'NOVALNET_SUBSCRIPTION_CANCEL_REASON_3'  => '(Ehe-)Partner hat Einspruch eingelegt',
    'NOVALNET_SUBSCRIPTION_CANCEL_REASON_4'  => 'Finanzielle Schwierigkeiten',
    'NOVALNET_SUBSCRIPTION_CANCEL_REASON_5'  => 'Inhalt entsprach nicht meinen Vorstellungen',
    'NOVALNET_SUBSCRIPTION_CANCEL_REASON_6'  => 'Inhalte nicht ausreichend',
    'NOVALNET_SUBSCRIPTION_CANCEL_REASON_7'  => 'Nur an Probezugang interessiert',
    'NOVALNET_SUBSCRIPTION_CANCEL_REASON_8'  => 'Seite zu langsam',
    'NOVALNET_SUBSCRIPTION_CANCEL_REASON_9'  => 'Zufriedener Kunde',
    'NOVALNET_SUBSCRIPTION_CANCEL_REASON_10' => 'Zugangsprobleme',
    'NOVALNET_SUBSCRIPTION_CANCEL_REASON_11' => 'Sonstige',
    'NOVALNET_SUBSCRIPTION_CANCELED_MESSAGE' => '<br><br>Das Abonnement wurde gekündigt wegen: ',
    'NOVALNET_INVALID_CANCEL_REASON'         => 'Wählen Sie bitte den Grund für die Abonnementskündigung aus',
    'NOVALNET_CANCEL_REASON'                 => 'Sind Sie sicher, dass Sie das Abonnement kündigen wollen?',

    'NOVALNET_BOOK_AMOUNT_TITLE'                => 'Transaktion durchführen',
    'NOVALNET_BOOK_AMOUNT_LABEL'                => 'Buchungsbetrag der Transaktion',
    'NOVALNET_AMOUNT_BOOKED_MESSAGE'            => '<br><br>Ihre Bestellung wurde mit einem Betrag von %s gebucht. Ihre neue TID für den gebuchten Betrag: %s',
    'NOVALNET_INVALID_CONFIG_ERROR'             => 'Füllen Sie bitte alle Pflichtfelder aus',
    'NOVALNET_INVALID_SEPA_CONFIG_ERROR'        => 'SEPA Fälligkeitsdatum Ungültiger',
    'NOVALNET_INVALID_INVOICE_REF_CONFIG_ERROR' => 'Wählen Sie mindestens einen Verwendungszweck aus',
    'NOVALNET_DEFAULT_ERROR_MESSAGE'            => 'Die Zahlung war nicht erfolgreich. Ein Fehler trat auf',
    'NOVALNET_INVALID_PHP_PACKAGE'              => 'Erwähnt PHP-Paket (e) in diesem Server nicht verfügbar ist. Bitte aktivieren Sie sie.',

    'NOVALNET_IFRAME_CONFIGURATION_TITLE'             => 'Darstellung des Formulars',
    'NOVALNET_IFRAME_FIELD_STYLE_CONFIGURATION_TITLE' => 'CSS-Einstellungen für Felder mit Kreditkartendaten',
    'NOVALNET_IFRAME_FIELD'                           => 'Formularfelder',
    'NOVALNET_IFRAME_LABEL'                           => 'Beschriftung',
    'NOVALNET_IFRAME_INPUT_FIELD'                     => 'Eingabefeld',
    'NOVALNET_IFRAME_CARD_HOLDER'                     => 'Name des Karteninhabers',
    'NOVALNET_IFRAME_CARD_NUMBER'                     => 'Kreditkartennummer',
    'NOVALNET_IFRAME_CARD_EXP_DATE'                   => 'Ablaufdatum',
    'NOVALNET_IFRAME_CARD_CVC'                        => 'CVC/CVV/CID',
    'NOVALNET_IFRAME_STYLE_CONFIGURATION_TITLE'       => 'CSS-Einstellungen für den iFrame mit Kreditkartendaten',
    'NOVALNET_IFRAME_INPUT'                           => 'Eingabe',
    'NOVALNET_IFRAME_CSS'                             => 'Text für das CSS',
    'NOVALNET_BARZAHLEN'                              => 'Novalnet Barzahlen',
    'NOVALNET_BARZAHLEN_DUE_DATE_TITLE'               => 'Verfallsdatum des Zahlscheins (in Tagen)',
    'NOVALNET_BARZAHLEN_DUE_DATE_DESCRIPTION'         => 'Geben Sie die Anzahl der Tage ein, um den Betrag in einer Barzahlen-Partnerfiliale in Ihrer Nähe zu bezahlen. Wenn das Feld leer ist, werden standardmäßig 14 Tage als Fälligkeitsdatum gesetzt.',
    'NOVALNET_BARZAHLEN_DUE_DATE_UPDATE_TITLE'        => 'Betrag/Verfallsdatum des Zahlscheins ändern',
    'NOVALNET_BARZAHLEN_DUE_DATE_LABEL'               => 'Verfallsdatum des Zahlscheins',
    'NOVALNET_BARZAHLEN_DUE_DATE'                     => '<br>Verfallsdatum des Zahlscheins: ',
    'NOVALNET_BARZAHLEN_PAYMENT_STORE'                => '<br><br>Barzahlen-Partnerfiliale in Ihrer Nähe<br><br>',
    'NOVALNET_CONFIRM_SLIPDATE_UPDATE'                => 'Sind Sie sicher, dass sie den Bestellbetrag / das Ablaufdatum des Zahlscheins ändern wollen?',
    'NOVALNET_INVALID_SLIPEDATE'                      => 'Geben Sie bitte ein gültiges Ablaufdatum für den Zahlschein ein.',
    'NOVALNET_AMOUNT_DATE_UPDATED_MESSAGE'            => '<br><br>Die Transaktion wurde mit dem Betrag %s und dem Fälligkeitsdatum %s aktualisiert.',

    'NOVALNET_ADMIN_MENU'                             => 'Novalnet-updates',
    'NOVALNET_ADMIN_MENU_CHECHOUT'                    =>'Sehen Sie, was es neues gibt!',
    'NOVALNET_ADMIN_UPDATE_VERSION'                   => '<h2><b>Novalnet-Zahlungsplugin V11.1.6</b></h2>',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE'                       => 'Vielen Dank, dass Sie die neueste Version des Novalnet Zahlungs-moduls installiert haben. Diese Version bringt einige großartige neue Funktionen und Erweiterungen.',
   'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_IT'                     =>'Hoffentlich macht es Ihnen Spaß, damit zu arbeiten!',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_KEY'                   =>'Aktivierungsschlüssel des Produkts',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_KEY_DESC'              =>'Novalnet hat den Aktivierungsschlüssel für Produkte eingeführt, um die gesamten Händler-Zugangsdaten automatisch einzutragen, wenn dieser Schlüssel in die Novalnet-Hauptkonfiguration eingetragen wird.Um diesen Aktivierungschlüssel für das Produkt zu erhalten',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_IP'                    =>'Einstellung der IP-Adresse',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_IP_DESC'               =>'Für alle Zugriffe auf die API (automatische Konfiguration mit dem Aktivierungsschlüssel des Produkts, Laden eines Kreditkarten-iFrame, Zugriff auf die API für die Übermittlung von Transaktionen, die Abfrage des Transaktionsstatus und Änderungen an Transaktionen), muss eine IP-Adresse für den Server im Novalnet-Administrationsportal eingerichtet sein.',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_VENDOR_URL'            =>'Aktualisierung der Händlerskript-URL',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_VENDOR_URL_DESC'       =>'Die Händlerskript-URL wird dazu benötigt, um den Transaktionsstatus in der Datenbank / im System des Händlers aktuell und auf demselben Stand wie bei Novalnet zu halten. Dazu muss die Händlerskript-URL im Novalnet-Händleradministrationsportal eingerichtet werden.Vom Novalnet-Server wird die Information zu jeder Transaktion und deren Status (durch asynchrone Aufrufe) an den Server des Händlers übertragen.',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_ONE_CLICK'             =>'Shopping mit einem Klick',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_ONE_CLICK_DESC'        =>'Möchten Sie Ihre Kunden eine Bestellung mit einem einzigen Klick aufgeben lassen? Mit dem Novalnet Zahlungs-modul ist dies möglich! Dieses Merkmal ermöglicht es dem Endkunden, bequemer mit hinterlegten Konto-/Kartendaten zu bezahlen.',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_ZERO_AMOUNT'           =>'Buchung mit Betrag 0',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_ZERO_AMOUNT_DESC'      =>'Die Funktion "Buchung mit Betrag 0" ermöglicht es dem Händler, ein Produkt zu unterschiedlichen Preisen im Shop zu verkaufen. Die Bestellung wird zuerst mit dem Betrag 0 verarbeitet, danach kann der Händler später den Bestellbetrag abbuchen, um die Transaktion abzuschließen.',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_CC_IFRAME'             =>'Beschleunigter Kreditkarten-iFrame',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_CC_IFRAME_DESC'        =>'Jetzt haben wir den iFrame für Kreditkartenzahlungen mit den dynamischsten Funktionen aktualisiert. Mit nur wenig Code haben wir den Inhalt des Kreditkarten-iFrame beschleunigt und nutzerfreundlicher gemacht.Der Händler kann selbst die CSS-Einstellungen des Kreditkarten-iFrame-Formulars anpassen.',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_KEY_DESCS'             =>'Um diesen Aktivierungschlüssel für das Produkt zu erhalten, gehen Sie zum <a href=https://admin.novalnet.de target=_blank> Novalnet Admin-Portal</a> - Projekte: Informationen zum jeweiligen Projekt - Parameter Ihres Shops: API Signature (Aktivierungsschlüssel des Produkts)',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_IP_DESCS'              =>'Um eine IP-Adresse einzurichten, gehen Sie im  <a href=https://admin.novalnet.de target=_blank>Novalnet Admin-Portal </a> zu Projekte: Informationen zum jeweiligen Projekt - Projektübersicht: IPs für Zahlungsaufrufe.',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_VENDOR_URL_DESCS'      =>'Um den Händlerskript-URL einzurichten, gehen Sie im <a href=https://admin.novalnet.de target=_blank> Novalnet-Admin-Portal</a> zu  Projekte: Informationen zum jeweiligen Projekt - Projektübersicht: Händlerskript-URL',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_MORE'                  =>'Moment, es gibt noch mehr!',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_PAYPAL'                =>'Paypal',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_PAYPAL_DESE'           =>'Um PayPal-Zahlungen verarbeiten zu können, müssen Sie Ihre PayPal-API-Details im Novalnet-Adminstrationsportal konfigurieren.',
    'NOVALNET_ADMIN_CONFIG_PAYMENT_MODULE_UPDATE_PAYPAL_DESES'          =>'Um die PayPal-API-Details zu konfigurieren, gehen Sie bitte im <a href=https://admin.novalnet.de target=_blank>Novalnet-Admin-Portal</a> zu Projekte: [Informationen zum jeweiligen Projekt] - Zahlungsmethoden : PayPal - Konfigurieren.',
    'NOVALNET_ORDER_CONFIRMATION'               => 'Bestellbestätigung - Ihre Bestellung ',
    'NOVALNET_ORDER_CONFIRMATION1'               => ' bei ',
    'NOVALNET_ORDER_CONFIRMATION2'               => ' wurde bestätigt',
    'NOVALNET_ORDER_CONFIRMATION3'               => 'Wir freuen uns Ihnen mitteilen zu können, dass Ihre Bestellung bestätigt wurde',
    'NOVALNET_PAYMENT_INFORMATION'               => 'Zahlung Informationen:',
    'NOVALNET_PAYMENT_GUARANTEE_COMMENTS'        => 'Diese Transaktion wird mit Zahlungsgarantie verarbeitet<br>',
);
?>
